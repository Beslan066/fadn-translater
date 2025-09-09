<?php

namespace App\Jobs;


use App\Models\Sentence;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessOtherSentenceBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $sentences;

    public function __construct(array $sentences)
    {
        $this->sentences = $sentences;
    }

    public function handle()
    {
        try {
            Log::info('Начало обработки чанка дополнительных предложений', ['memory' => memory_get_usage()]);
            $data = [];

            foreach ($this->sentences as $sentence) {
                $trimmedSentence = trim($sentence);
                $length = mb_strlen($trimmedSentence);

                if (!empty($trimmedSentence)) {
                    $price = match (true) {
                        $length <= 100 => 6,
                        $length <= 200 => 12,
                        default => 18,
                    };

                    $data[] = [
                        'sentence' => $trimmedSentence,
                        'price' => $price,
                        'otherSentence' => 1, // Помечаем как дополнительное предложение
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($data)) {
                Sentence::insert($data);
            }

            Cache::increment('processed_other_sentences', count($data));
            Log::info('Чанк дополнительных предложений успешно обработан', ['memory' => memory_get_usage()]);
        } catch (\Exception $e) {
            Log::channel('sentence_jobs')->error('Ошибка в ProcessOtherSentenceBatchJob: ' . $e->getMessage(), [
                'sentences' => $this->sentences,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
