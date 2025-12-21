<?php

namespace App\Jobs;

use App\Models\QueueJobLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LogJobExecution implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $jobId,
        public string $jobType,
        public string $status,
        public ?float $executionTime = null,
        public ?string $output = null,
        public ?string $error = null,
        public ?array $metadata = null
    ) {}

    public function handle(): void
    {
        try {
            QueueJobLog::create([
                'job_id' => $this->jobId,
                'job_type' => $this->jobType,
                'queue' => $this->queue,
                'status' => $this->status,
                'execution_time' => $this->executionTime,
                'output' => $this->output,
                'error_message' => $this->error,
                'metadata' => $this->metadata
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log job execution: ' . $e->getMessage());
        }
    }
}
