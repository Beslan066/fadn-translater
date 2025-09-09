<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Export;
use App\Models\User;
use App\Models\Region;
use App\Notifications\ExportCompletedNotification;
use App\Notifications\ExportFailedNotification;

class ExportRegionSentencesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $regionId;
    public $userId;
    public $otherSentence;
    public $timeout = 7200;
    public $tries = 1;

    public function __construct(int $regionId, int $userId, ?int $otherSentence = null)
    {
        $this->regionId = $regionId;
        $this->userId = $userId;
        $this->otherSentence = $otherSentence;
    }

    public function handle()
    {
        $region = Region::find($this->regionId);
        $user = User::find($this->userId);
        $export = null;
        $handle = null;
        $filePath = null;

        if (!$region || !$user) {
            throw new \Exception('Регион или пользователь не найдены');
        }

        try {
            // Генерируем уникальное имя файла
            $fileName = $this->generateFileName();
            $filePath = storage_path('app/exports/' . $fileName);

            \Log::info('Starting export job. File will be saved as: ' . $fileName);
            \Log::info('Full path: ' . $filePath);

            // Создаем директорию если не существует
            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }

            // Создаем запись в базе данных
            $export = Export::create([
                'user_id' => $this->userId,
                'region_id' => $this->regionId,
                'file_name' => $fileName,
                'file_size' => 0,
                'status' => 'processing',
                'processed_count' => 0
            ]);

            // Открываем файл для записи
            $handle = fopen($filePath, 'w');
            if (!$handle) {
                throw new \Exception("Cannot open file: {$filePath}. Check permissions.");
            }

            // Добавляем BOM для корректного отображения кириллицы в Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Заголовки CSV
            fputcsv($handle, [
                'ID', 'Предложение', 'Перевод', 'Автор перевода',
                'Тип предложения', 'Статус перевода', 'Дата создания'
            ], ';');

            $offset = 0;
            $limit = 1000;
            $totalProcessed = 0;
            $batchCount = 0;
            $hasData = true;

            while ($hasData) {
                $batchCount++;
                Log::info("Processing batch {$batchCount} for region {$this->regionId}, offset: {$offset}");

                $translations = $this->getTranslationsBatch($offset, $limit);

                if ($translations->isEmpty()) {
                    Log::info("No more translations found for region {$this->regionId}");
                    $hasData = false;
                    continue;
                }

                foreach ($translations as $translation) {
                    fputcsv($handle, [
                        $translation->sentence_id,
                        $this->escapeCsvField($translation->original_sentence ?? ''),
                        $this->escapeCsvField($translation->translated_text ?? ''),
                        $this->escapeCsvField($translation->translator_name ?? 'Не назначен'),
                        $translation->otherSentence ?? '',
                        $this->getStatusText($translation->status),
                        $translation->created_at
                    ], ';');
                }

                $processedCount = $translations->count();
                $totalProcessed += $processedCount;
                $offset += $limit;

                Log::info("Processed {$processedCount} records in batch {$batchCount}, total: {$totalProcessed}");

                // Периодически обновляем прогресс в базе
                if ($batchCount % 5 === 0) {
                    $export->update([
                        'processed_count' => $totalProcessed,
                        'updated_at' => now(),
                    ]);
                }

                // Освобождаем память
                unset($translations);
                gc_collect_cycles();

                // Небольшая пауза между батчами
                if ($batchCount % 20 === 0) {
                    sleep(1);
                }
            }

            // Важно: закрываем файл перед проверкой его существования
            fclose($handle);
            $handle = null;

            // Даем системе время на запись файла
            sleep(2);

            // Проверяем что файл создан и имеет размер
            if (!file_exists($filePath)) {
                throw new \Exception("Export file was not created: {$filePath}");
            }

            $fileSize = filesize($filePath);
            Log::info("File created: {$filePath}, size: {$fileSize} bytes");

            if ($fileSize === 0) {
                throw new \Exception("Export file is empty: {$filePath}");
            }

            // Обновляем запись об экспорте
            $export->update([
                'status' => 'completed',
                'file_size' => $fileSize,
                'processed_count' => $totalProcessed,
                'completed_at' => now(),
            ]);

            Log::info("Export completed successfully for region: {$this->regionId}, file: {$fileName}");

            // Отправляем уведомление
            $this->sendNotification($fileName, $export->id);

        } catch (\Exception $e) {
            // Закрываем файл если открыт
            if ($handle) {
                @fclose($handle);
            }

            Log::error("Export failed for region {$this->regionId}: " . $e->getMessage());

            // Удаляем частично созданный файл только если он пустой или очень маленький
            if ($filePath && file_exists($filePath)) {
                $fileSize = @filesize($filePath);
                if ($fileSize < 100) { // Удаляем только совсем маленькие файлы
                    @unlink($filePath);
                    Log::info("Removed incomplete file: {$filePath}");
                } else {
                    Log::info("Keeping partially created file: {$filePath}, size: {$fileSize} bytes");
                }
            }

            // Обновляем статус экспорта
            if ($export) {
                $export->update([
                    'status' => 'failed',
                    'error_message' => substr($e->getMessage(), 0, 255),
                    'completed_at' => now(),
                ]);
            }

            // Отправляем уведомление об ошибке
            if ($user) {
                $user->notify(new ExportFailedNotification($e->getMessage(), $this->regionId));
            }

            throw $e;
        }
    }

    private function getTranslationsBatch(int $offset, int $limit)
    {
        try {
            $query = DB::table('translations as t')
                ->select([
                    't.sentence_id',
                    't.translated_text',
                    't.status',
                    't.created_at',
                    's.sentence as original_sentence',
                    's.otherSentence',
                    'u.name as translator_name'
                ])
                ->leftJoin('sentences as s', 't.sentence_id', '=', 's.id')
                ->leftJoin('users as u', 't.translator_id', '=', 'u.id')
                ->where('t.region_id', $this->regionId)
                ->orderBy('t.sentence_id');

            // Фильтр по типу предложения если указан
            if ($this->otherSentence !== null) {
                $query->where('s.otherSentence', $this->otherSentence);
            }

            return $query->offset($offset)
                ->limit($limit)
                ->get();

        } catch (\Exception $e) {
            Log::error("Database query failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function escapeCsvField($value)
    {
        if ($value === null) {
            return '';
        }

        $value = str_replace('"', '""', (string)$value);

        if (strpos($value, ';') !== false || strpos($value, '"') !== false) {
            return '"' . $value . '"';
        }

        return $value;
    }

    private function getStatusText($status): string
    {
        $statuses = [
            0 => 'Назначен',
            1 => 'Переведен',
            2 => 'Проверен',
            3 => 'Отклонен',
            4 => 'Завершен админом'
        ];

        return $statuses[$status] ?? 'Неизвестно';
    }

    private function generateFileName(): string
    {
        $type = $this->otherSentence !== null ? "_type_{$this->otherSentence}" : '';
        return "corpus_region_{$this->regionId}{$type}_" . now()->format('Y-m-d_His') . '.csv';
    }

    private function sendNotification(string $fileName, int $exportId)
    {
        try {
            $user = User::find($this->userId);
            if ($user) {
                $user->notify(new ExportCompletedNotification($fileName, $this->regionId, $exportId));
                Log::info("Notification sent to user {$this->userId}");
            }
        } catch (\Exception $e) {
            Log::error("Notification failed: " . $e->getMessage());
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error("Export job completely failed for region {$this->regionId}: " . $exception->getMessage());

        // Находим пользователя и отправляем уведомление об ошибке
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new ExportFailedNotification(
                $exception->getMessage(),
                $this->regionId
            ));
        }
    }
}
