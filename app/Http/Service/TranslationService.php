<?php

namespace App\Services;

use App\Models\Sentence;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    /**
     * Назначить предложение для перевода
     */
    public function assignSentence(User $translator): ?Translation
    {
        return DB::transaction(function () use ($translator) {
            // Находим доступное предложение для региона переводчика
            $sentence = Sentence::available($translator->region_id)
                ->where(function($query) {
                    $query->whereNull('locked_at')
                        ->orWhere('locked_at', '<', now()->subMinutes(30));
                })
                ->orderBy('complexity')
                ->first();

            if (!$sentence) {
                return null;
            }

            // Оптимистичная блокировка
            $locked = Sentence::where('id', $sentence->id)
                ->where(function($query) {
                    $query->whereNull('locked_at')
                        ->orWhere('locked_at', '<', now()->subMinutes(30));
                })
                ->update([
                    'locked_at' => now(),
                    'locked_by' => $translator->id,
                ]);

            if (!$locked) {
                return null;
            }

            // Создаем запись о переводе
            $translation = Translation::create([
                'sentence_id' => $sentence->id,
                'region_id' => $translator->region_id,
                'translator_id' => $translator->id,
                'status' => Translation::STATUS_ASSIGNED,
                'assigned_at' => now(),
            ]);

            return $translation;
        });
    }

    /**
     * Сохранить перевод
     */
    public function saveTranslation(Translation $translation, string $text): bool
    {
        return DB::transaction(function () use ($translation, $text) {
            $updated = $translation->update([
                'translated_text' => $text,
                'status' => Translation::STATUS_TRANSLATED,
                'translated_at' => now(),
                'locked_at' => null,
            ]);

            if ($updated) {
                $translation->sentence()->update([
                    'locked_at' => null,
                    'locked_by' => null,
                ]);
            }

            return $updated;
        });
    }

    /**
     * Проверить перевод (корректором)
     */
    public function proofreadTranslation(Translation $translation, User $proofreader, string $action, ?string $text = null): bool
    {
        return DB::transaction(function () use ($translation, $proofreader, $action, $text) {
            $status = $action === 'accept'
                ? Translation::STATUS_PROOFREAD
                : Translation::STATUS_REJECTED;

            $updateData = [
                'proofreader_id' => $proofreader->id,
                'status' => $status,
                'proofread_at' => now(),
            ];

            if ($action === 'accept') {
                $updateData['proofread_text'] = $text ?? $translation->translated_text;
            } else {
                $updateData['rejected_at'] = now();
                $updateData['reject_reason'] = $text;
            }

            return $translation->update($updateData);
        });
    }

    /**
     * Получить статистику по переводам для пользователя
     */
    public function getUserStats(User $user): array
    {
        $query = Translation::where('translator_id', $user->id);

        return [
            'total' => $query->count(),
            'assigned' => $query->where('status', Translation::STATUS_ASSIGNED)->count(),
            'translated' => $query->where('status', Translation::STATUS_TRANSLATED)->count(),
            'proofread' => $query->where('status', Translation::STATUS_PROOFREAD)->count(),
            'rejected' => $query->where('status', Translation::STATUS_REJECTED)->count(),
        ];
    }
}
