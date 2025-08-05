<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Statistic extends Model
{
    protected $fillable = [
        'region_id', 'total_sentences', 'translated_count',
        'proofread_count', 'approved_count', 'total_cost', 'record_date'
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
