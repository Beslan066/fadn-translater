<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueWorker extends Model
{
    protected $fillable = [
        'queue',
        'connection',
        'status',
        'pid',
        'options',
        'processed_jobs',
        'failed_jobs',
        'last_heartbeat'

    ];

    protected $casts = [
        'options' => 'array',
        'last_heartbeat' => 'datetime'
    ];

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isAlive(): bool
    {
        if (!$this->pid || !$this->last_heartbeat) {
            return false;
        }

        // Проверяем, что процесс жив и heartbeat не старше 5 минут
        if ($this->last_heartbeat->diffInMinutes(now()) > 5) {
            return false;
        }

        return true;
    }

    // Статический метод для обновления heartbeat
    public static function heartbeat(string $queue): void
    {
        static::where('queue', $queue)->update(['last_heartbeat' => now()]);
    }
}
