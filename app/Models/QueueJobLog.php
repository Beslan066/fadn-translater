<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueJobLog extends Model
{
    protected $fillable = [
        'job_id',
        'job_type',
        'queue',
        'payload',
        'status',
        'execution_time',
        'output',
        'error_message',
        'metadata'
    ];

    protected $casts = [
        'payload' => 'array',
        'metadata' => 'array'
    ];

    public function scopeRecent($query, $limit = 100)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
