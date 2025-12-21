<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QueueWorker;

class UpdateWorkerHeartbeat extends Command
{
    protected $signature = 'queue:heartbeat {queue}';
    protected $description = 'Update worker heartbeat';

    public function handle()
    {
        $queue = $this->argument('queue');
        QueueWorker::heartbeat($queue);
        $this->info("Heartbeat updated for queue: {$queue}");
    }
}
