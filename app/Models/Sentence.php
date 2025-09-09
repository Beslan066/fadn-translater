<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Sentence extends Model
{
    // Статусы предложений (оставляем для совместимости)
    const STATUS_AVAILABLE = 0;
    const STATUS_ASSIGNED = 1;
    const STATUS_TRANSLATED = 2;
    const STATUS_PROOFREAD = 3;
    const STATUS_REJECTED = 4;

    protected $fillable = [
        'sentence', 'price', 'complexity',
        'locked_at', 'locked_by', 'delayed_until', 'status', 'otherSentence','user_id',
        'region_id',
        'file_name',
        'file_size',
        'status',
        'processed_count',
        'downloaded_at'
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'delayed_until' => 'datetime',
        'status' => 'integer',
        'downloaded_at' => 'datetime',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    public function translationForRegion(?int $regionId = null)
    {
        // Если regionId не передан, используем регион текущего пользователя
        $regionId = $regionId ?? auth()->user()->region_id;

        return $this->hasOne(Translation::class)
            ->where('region_id', $regionId);
    }
    public function scopeAvailableForRegion(Builder $query, int $regionId): Builder
    {
        return $query->where(function($q) use ($regionId) {
            $q->whereDoesntHave('translations', function($q) use ($regionId) {
                $q->where('region_id', $regionId)
                    ->whereIn('status', [
                        Translation::STATUS_TRANSLATED,
                        Translation::STATUS_PROOFREAD,
                        Translation::STATUS_COMPLETED_BY_ADMIN // Добавляем новый статус
                    ]);
            })
                ->orWhereHas('translations', function($q) use ($regionId) {
                    $q->where('region_id', $regionId)
                        ->where('status', Translation::STATUS_REJECTED);
                });
        })
            ->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeAssignedTo(Builder $query, int $userId): Builder
    {
        return $query->whereHas('translations', function($q) use ($userId) {
            $q->where('translator_id', $userId)
                ->where('status', Translation::STATUS_ASSIGNED);
        });
    }

    public function isTranslatedForRegion(int $regionId): bool
    {
        return $this->translations()
            ->where('region_id', $regionId)
            ->whereIn('status', [
                Translation::STATUS_TRANSLATED,
                Translation::STATUS_PROOFREAD,
                Translation::STATUS_COMPLETED_BY_ADMIN // Добавляем новый статус
            ])
            ->exists();
    }

    public function canBeTranslatedBy(User $user): bool
    {
        if (!$user->isTranslator()) {
            return false;
        }

        // Проверяем блокировку на уровне перевода
        $translation = $this->translationForRegion($user->region_id)->first();

        if ($translation && $translation->isLockedByOther($user->id)) {
            return false;
        }

        return !$this->isTranslatedForRegion($user->region_id);
    }

    public function markAsProofread(): bool
    {
        return $this->update([
            'status' => self::STATUS_PROOFREAD,
            'locked_by' => null,
            'locked_at' => null
        ]);
    }

    public function assignToRegion(int $regionId, User $translator): Translation
    {
        return DB::transaction(function () use ($regionId, $translator) {
            // Создаем или получаем перевод для региона
            $translation = Translation::firstOrCreate(
                [
                    'sentence_id' => $this->id,
                    'region_id' => $regionId
                ],
                [
                    'translator_id' => $translator->id,
                    'status' => Translation::STATUS_ASSIGNED,
                    'assigned_at' => now(),
                    'locked_by' => $translator->id,
                    'locked_at' => now()
                ]
            );

            // Обновляем блокировку если перевод уже существовал
            $translation->update([
                'locked_by' => $translator->id,
                'locked_at' => now()
            ]);

            return $translation;
        });
    }

    public function scopeForRegion(Builder $query, int $regionId): Builder
    {
        return $query->with(['translationForRegion' => function($q) use ($regionId) {
            $q->where('region_id', $regionId);
        }]);
    }

    public function scopeByOtherSentence(Builder $query, ?int $type = null): Builder
    {
        if ($type !== null) {
            return $query->where('otherSentence', $type);
        }

        return $query;
    }

    public function exportSentences(Region $region, Request $request): StreamedResponse
    {
        $request->validate([
            'other_sentence' => 'nullable|in:1,2'
        ]);

        $otherSentence = $request->input('other_sentence');

        $fileName = 'corpus_region_' . $region->id . '_' . ($otherSentence ? 'type_' . $otherSentence . '_' : '') . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return Response::stream(function () use ($region, $otherSentence) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Предложение', 'Перевод', 'Автор перевода',
                'Тип предложения', 'Статус перевода', 'Дата создания'
            ], ';');

            // Используем join для оптимальной производительности
            $query = Translation::select([
                'translations.*',
                'sentences.sentence as original_sentence',
                'sentences.otherSentence',
                'users.name as translator_name'
            ])
                ->join('sentences', 'translations.sentence_id', '=', 'sentences.id')
                ->leftJoin('users', 'translations.translator_id', '=', 'users.id')
                ->where('translations.region_id', $region->id);

            if ($otherSentence) {
                $query->where('sentences.otherSentence', $otherSentence);
            }

            $query->orderBy('translations.sentence_id')->chunk(5000, function ($translations) use ($handle) {
                foreach ($translations as $translation) {
                    fputcsv($handle, [
                        $translation->sentence_id,
                        $translation->original_sentence,
                        $translation->translated_text,
                        $translation->translator_name ?? 'Не назначен',
                        $translation->otherSentence,
                        $this->getStatusText($translation->status),
                        $translation->created_at->format('Y-m-d H:i:s')
                    ], ';');
                }

                if (ob_get_level() > 0) ob_flush();
                flush();
            });

            fclose($handle);
        }, 200, $headers);
    }
}
