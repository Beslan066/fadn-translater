<?php

namespace App\Http\Controllers;

use App\Jobs\ExportRegionSentencesJob;
use App\Jobs\ProcessOtherSentenceBatchJob;
use App\Jobs\ProcessSentenceBatchJob;
use App\Models\Export;
use App\Models\Sentence;
use App\Models\Statistic;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Region;
use Illuminate\Support\Facades\Response;

use Symfony\Component\HttpFoundation\StreamedResponse;

class SentenceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $sentences = Sentence::query()->where('otherSentence', 0)->when($search, function($query) use ($search) {
            return $query->where('sentence', 'like', '%'.$search.'%');
        })->paginate(20);

        return view('pages.sentences.index', [
            'sentences' => $sentences,
            'search' => $search
        ]);
    }

    public function otherSentences(Request $request)
    {
        $search = $request->input('search');

        $sentences = Sentence::query()->where('otherSentence', 1)->when($search, function($query) use ($search) {
            return $query->where('sentence', 'like', '%'.$search.'%');
        })->paginate(20);

        return view('pages.sentences.other-sentences', [
            'sentences' => $sentences,
            'search' => $search
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:txt|max:307200',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $chunkSize = 1000;
        $totalLines = 0;

        if (($handle = fopen($filePath, 'r')) !== false) {
            try {
                DB::beginTransaction();

                $batch = Bus::batch([])->dispatch();
                $currentBatch = [];

                while (($line = fgets($handle)) !== false) {
                    $trimmedLine = trim($line);

                    if (!empty($trimmedLine)) {
                        $currentBatch[] = $trimmedLine;

                        if (count($currentBatch) >= $chunkSize) {
                            $batch->add(new ProcessSentenceBatchJob($currentBatch));
                            $currentBatch = [];
                        }

                        $totalLines++;
                    }
                }

                if (count($currentBatch) > 0) {
                    $batch->add(new ProcessSentenceBatchJob($currentBatch));
                }

                fclose($handle);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Файл отправлен в очередь на обработку.',
                    'total_lines' => $totalLines,
                    'batch_id' => $batch->id,
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Ошибка при загрузке файла: ' . $e->getMessage(), [
                    'stack' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка обработки файла: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось открыть файл.',
        ], 500);
    }

    public function otherSentencesUpload(Request $request) {
        $request->validate([
            'file' => 'required|file|mimes:txt|max:307200',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $chunkSize = 1000;
        $totalLines = 0;

        if (($handle = fopen($filePath, 'r')) !== false) {
            try {
                DB::beginTransaction();

                $batch = Bus::batch([])->dispatch();
                $currentBatch = [];

                while (($line = fgets($handle)) !== false) {
                    $trimmedLine = trim($line);

                    if (!empty($trimmedLine)) {
                        $currentBatch[] = $trimmedLine;

                        if (count($currentBatch) >= $chunkSize) {
                            $batch->add(new ProcessOtherSentenceBatchJob($currentBatch));
                            $currentBatch = [];
                        }

                        $totalLines++;
                    }
                }

                if (count($currentBatch) > 0) {
                    $batch->add(new ProcessOtherSentenceBatchJob($currentBatch));
                }

                fclose($handle);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Дополнительный корпус отправлен в очередь на обработку.',
                    'total_lines' => $totalLines,
                    'batch_id' => $batch->id,
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Ошибка при загрузке дополнительного корпуса: ' . $e->getMessage(), [
                    'stack' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка обработки дополнительного корпуса: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось открыть файл.',
        ], 500);
    }

    public function getRandomSentence(Request $request)
    {
        $user = Auth::user();
        $regionId = $user->region_id;

        if (Session::has('current_sentence_id')) {
            $sentenceId = Session::get('current_sentence_id');
            $sentence = Sentence::find($sentenceId);

            if (!$sentence || $sentence->status != 'pending' || $sentence->locked_by != $user->id) {
                Session::forget('current_sentence_id');
                $sentence = null;
            }
        }

        if (isset($sentence)) {
            DB::transaction(function () use (&$sentence, $user, $regionId) {
                $sentence = Sentence::where('status', 'pending')
                    ->whereNull('locked_by')
                    ->whereDoesntHave('translations', function($query) use ($regionId) {
                        $query->where('region_id', $regionId);
                    })
                    ->inRandomOrder()
                    ->first();

                if ($sentence) {
                    $sentence->update(['locked_by' => $user->id]);
                    Session::put('current_sentence_id', $sentence->id);
                }
            });
        }

        return view('sentences.random', [
            'sentence' => $sentence,
            'region_id' => $regionId,
            'user' => $user,
        ]);
    }

    public function getRegionalStatistics(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'fadn') {
            $statistics = Statistic::with('region')
                ->orderBy('record_date', 'desc')
                ->get()
                ->groupBy('region_id');
        } else {
            $statistics = Statistic::where('region_id', $user->region_id)
                ->orderBy('record_date', 'desc')
                ->get();
        }

        return Inertia::render('Dashboard', [
            'statistics' => $statistics,
            'auth' => [
                'user' => $user,
            ],
        ]);
    }

    public function getRegionalTranslations(Request $request)
    {
        $user = Auth::user();
        $query = Translation::with(['sentence', 'user', 'proofreads.user'])
            ->orderBy('created_at', 'desc');

        if ($user->role !== 'fadn') {
            $query->where('region_id', $user->region_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $translations = $query->paginate(10);

        return view('pages.sentences.translations', [
            'translations' => $translations,
        ]);
    }

    public function destroy(Sentence $sentence)
    {
        $sentence->delete();

        return redirect()->route('sentences.index');
    }

    public function exportSentences(Region $region, Request $request): JsonResponse
    {
        $request->validate([
            'other_sentence' => 'nullable|in:1,2'
        ]);

        try {
            ExportRegionSentencesJob::dispatch(
                $region->id,
                auth()->id(),
                $request->input('other_sentence')
            );

            return response()->json([
                'success' => true,
                'message' => 'Экспорт запущен в фоновом режиме'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка запуска экспорта: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkExportStatus(Request $request)
    {
        try {
            \Log::info('Checking export status for user: ' . auth()->id());

            $exports = \App\Models\Export::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subDays(2))
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($export) {
                    // Проверяем существует ли файл физически
                    $filePath = storage_path('app/exports/' . $export->file_name);
                    $fileExists = file_exists($filePath);

                    // Если файл не существует, но статус completed - меняем статус
                    $status = $export->status;
                    if ($status === 'completed' && !$fileExists) {
                        $status = 'file_missing';
                    }

                    return [
                        'id' => $export->id,
                        'file_name' => $export->file_name,
                        'file_size' => $export->file_size,
                        'status' => $status,
                        'file_exists' => $fileExists,
                        'processed_count' => $export->processed_count,
                        'created_at' => $export->created_at->format('d.m.Y H:i'),
                        'download_url' => route('export.download', ['fileName' => $export->file_name]),

                        'region_id' => $export->region_id,
                    ];
                });

            \Log::info('Found exports: ' . $exports->count());

            // Логируем детали для отладки
            foreach ($exports as $export) {
                \Log::info("Export: {$export['file_name']}, Status: {$export['status']}, Exists: {$export['file_exists']}");
            }

            return response()->json([
                'success' => true,
                'exports' => $exports,
                'has_completed' => $exports->where('status', 'completed')->isNotEmpty(),
                'has_missing_files' => $exports->where('status', 'file_missing')->isNotEmpty()
            ]);

        } catch (\Exception $e) {
            \Log::error('Export status check error: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка проверки статуса'
            ], 500);
        }
    }

    public function downloadExport($fileName)
    {
        try {
            // Безопасная проверка имени файла
            if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $fileName)) {
                return response()->json(['error' => 'Некорректное имя файла'], 400);
            }

            $filePath = storage_path("app/exports/{$fileName}");

            // Если файл не существует, проверяем старые пути
            if (!file_exists($filePath)) {
                // Возможно файл в другой папке или был перемещен
                $alternativePaths = [
                    storage_path("exports/{$fileName}"),
                    storage_path("app/{$fileName}"),
                    storage_path("{$fileName}")
                ];

                foreach ($alternativePaths as $altPath) {
                    if (file_exists($altPath)) {
                        $filePath = $altPath;
                        break;
                    }
                }
            }

            if (!file_exists($filePath)) {
                // Помечаем экспорт как missing
                $export = Export::where('file_name', $fileName)->first();
                if ($export) {
                    $export->update(['status' => 'file_missing']);
                }

                return response()->json(['error' => 'Файл не найден'], 404);
            }

            // Проверяем права доступа
            $export = Export::where('file_name', $fileName)
                ->where('user_id', auth()->id())
                ->first();

            if (!$export && !auth()->user()->isAdmin()) {
                return response()->json(['error' => 'Доступ запрещен'], 403);
            }

            // Обновляем время скачивания
            if ($export) {
                $export->update(['downloaded_at' => now()]);
            }

            return response()->download($filePath, $fileName, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);

        } catch (\Exception $e) {
            \Log::error('Download error: ' . $e->getMessage());
            return response()->json(['error' => 'Ошибка скачивания файла'], 500);
        }
    }



}
