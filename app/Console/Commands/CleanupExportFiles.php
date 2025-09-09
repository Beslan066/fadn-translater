<?php

namespace App\Console\Commands;

use App\Models\Export;
use Illuminate\Console\Command;

class CleanupExportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-export-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $exports = Export::where('created_at', '<', now()->subDays(1))->get();

        foreach ($exports as $export) {
            $filePath = storage_path("app/exports/{$export->file_name}");

            if (file_exists($filePath)) {
                unlink($filePath);
                $this->info("Removed: {$export->file_name}");
            }
        }

        $this->info('Cleanup completed');
    }
}
