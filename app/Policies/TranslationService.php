<?php

namespace App\Policies;

use App\Models\Translation;
use App\Models\User;

class TranslationPolicy
{
    /**
     * Может ли пользователь просматривать перевод
     */
    public function view(User $user, Translation $translation): bool
    {
        return $user->isFadn()
            || ($user->region_id === $translation->region_id
                && ($user->isProofreader() || $user->isTranslator()));
    }

    /**
     * Может ли пользователь редактировать перевод
     */
    public function update(User $user, Translation $translation): bool
    {
        if ($user->isProofreader()) {
            return $translation->status === Translation::STATUS_TRANSLATED
                && $user->region_id === $translation->region_id;
        }

        if ($user->isTranslator()) {
            return $translation->translator_id === $user->id
                && in_array($translation->status, [
                    Translation::STATUS_ASSIGNED,
                    Translation::STATUS_REJECTED
                ]);
        }

        return false;
    }

    /**
     * Может ли пользователь проверять перевод
     */
    public function proofread(User $user, Translation $translation): bool
    {
        return $user->isProofreader()
            && $translation->status === Translation::STATUS_TRANSLATED
            && $user->region_id === $translation->region_id;
    }
}
