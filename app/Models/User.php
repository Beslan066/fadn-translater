<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'region_id',
        'role',
        'is_active',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public const ROLES = [
        'fadn' => 'ФАДН',
        'region_admin' => 'Администратор региона',
        'translator' => 'Переводчик',
        'proofreader' => 'Корректор',
        'user' => 'Обычный пользователь',
        'super_admin' => 'Супер-админ',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translator_id');
    }


    public function proofreads(): HasMany
    {
        return $this->hasMany(Proofread::class);
    }

    public function isFadn(): bool
    {
        return $this->role === 'fadn';
    }

    public function isRegionAdmin(): bool
    {
        return $this->role === 'region_admin';
    }

    public function isTranslator(): bool
    {
        return $this->role === 'translator';
    }

    public function isProofreader(): bool
    {
        return $this->role === 'proofreader';
    }

    public function getRoleNameAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? Storage::disk('public')->url($this->avatar)
            : asset('assets/img/user.png');
    }


    // Статусы переводов
    public function assignedTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translator_id')
            ->where('status', Translation::STATUS_ASSIGNED);
    }

    public function translatedTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translator_id')
            ->where('status', Translation::STATUS_TRANSLATED);
    }

    public function proofreadTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translator_id')
            ->where('status', Translation::STATUS_PROOFREAD);
    }

    public function rejectedTranslations(): HasMany
    {
        return $this->hasMany(Translation::class, 'translator_id')
            ->where('status', Translation::STATUS_REJECTED);
    }

    // Проверенные им же переводы для корректора
    public function proofreadByMe(): HasMany
    {
        return $this->hasMany(Translation::class, 'proofreader_id');
    }
}
