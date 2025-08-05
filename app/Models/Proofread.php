<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proofread extends Model
{
    protected $fillable = ['translation_id', 'user_id', 'comments'];

    public function translation(): BelongsTo
    {
        return $this->belongsTo(Translation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
