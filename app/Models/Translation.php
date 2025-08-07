<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    const STATUS_ASSIGNED = 0;
    const STATUS_TRANSLATED = 1;
    const STATUS_PROOFREAD = 2;
    const STATUS_REJECTED = 3;

    protected $fillable = [
        'sentence_id', 'region_id', 'translator_id',
        'proofreader_id', 'translated_text', 'status',
        'assigned_at', 'translated_at', 'proofread_at',
        'reject_reason',
        'rejected_at', 'locked_at', 'locked_by'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'translated_at' => 'datetime',
        'proofread_at' => 'datetime',
        'rejected_at' => 'datetime',
        'locked_at' => 'datetime'
    ];

    public function sentence(): BelongsTo
    {
        return $this->belongsTo(Sentence::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function translator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'translator_id');
    }

    public function proofreader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proofreader_id');
    }

    public function isLockedByOther(int $userId): bool
    {
        return $this->locked_by && $this->locked_by != $userId &&
            $this->locked_at && $this->locked_at > now()->subHours(2);
    }

    public function assignTo(User $user): bool
    {
        return $this->update([
            'translator_id' => $user->id,
            'status' => self::STATUS_ASSIGNED,
            'assigned_at' => now(),
            'locked_by' => $user->id,
            'locked_at' => now()
        ]);
    }

    public function markAsTranslated(string $text): bool
    {
        return $this->update([
            'translated_text' => $text,
            'status' => self::STATUS_TRANSLATED,
            'translated_at' => now(),
            'locked_by' => null,
            'locked_at' => null
        ]);
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($user->isProofreader()) {
            return $this->status === self::STATUS_TRANSLATED;
        }

        if ($user->isTranslator()) {
            return $this->translator_id === $user->id &&
                in_array($this->status, [self::STATUS_ASSIGNED, self::STATUS_REJECTED]);
        }

        return false;
    }
}
