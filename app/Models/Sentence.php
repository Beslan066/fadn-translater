<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
        'locked_at', 'locked_by', 'delayed_until', 'status'
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'delayed_until' => 'datetime',
        'status' => 'integer'
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
                        Translation::STATUS_PROOFREAD
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
                Translation::STATUS_PROOFREAD
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
}
