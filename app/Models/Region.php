<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'name',
        'code',
        'language_code',
        'is_active',
        'translators_count',
        'proofreaders_count',
        'deleted_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Пользователи региона
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Переводы региона
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Активные переводчики региона
     */
    public function translators()
    {
        return $this->users()
            ->where('role', 'translator')
            ->where('is_active', true);
    }

    /**
     * Активные корректоры региона
     */
    public function proofreaders()
    {
        return $this->users()
            ->where('role', 'proofreader')
            ->where('is_active', true);
    }

    /**
     * Получить статистику по переводам
     */
    public function getTranslationStats(): array
    {
        return [
            'total' => $this->translations()->count(),
            'assigned' => $this->translations()->where('status', Translation::STATUS_ASSIGNED)->count(),
            'translated' => $this->translations()->where('status', Translation::STATUS_TRANSLATED)->count(),
            'proofread' => $this->translations()->where('status', Translation::STATUS_PROOFREAD)->count(),
            'published' => $this->translations()->where('status', Translation::STATUS_REJECTED)->count(),
        ];
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
