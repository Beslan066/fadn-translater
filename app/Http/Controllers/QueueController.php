<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Service\QueueManagerService; // Изменено здесь
use App\Models\QueueJobLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    protected QueueManagerService $queueManager;

    public function __construct(QueueManagerService $queueManager)
    {
        $this->queueManager = $queueManager;
    }

    public function dashboard()
    {
        $workers = $this->queueManager->getAllWorkersStatus();
        $stats = $this->queueManager->getQueueStats();

        // Получаем логи
        $logs = QueueJobLog::recent(50)->get();

        return view('pages.queue.dashboard', compact('workers', 'stats', 'logs'));
    }

    public function startWorker(Request $request)
    {
        $request->validate([
            'queue' => 'required|string',
            'options' => 'array'
        ]);

        $queue = $request->input('queue', 'default');
        $options = $request->input('options', []);

        $success = $this->queueManager->startWorker($queue, $options);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "Worker for queue '{$queue}' started successfully"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Failed to start worker for queue '{$queue}'"
        ], 500);
    }

    public function stopWorker(Request $request)
    {
        $request->validate(['queue' => 'required|string']);

        $queue = $request->input('queue');
        $success = $this->queueManager->stopWorker($queue);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "Worker for queue '{$queue}' stopped successfully"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Failed to stop worker for queue '{$queue}'"
        ], 500);
    }

    public function restartWorker(Request $request)
    {
        $request->validate(['queue' => 'required|string']);

        $queue = $request->input('queue');
        $success = $this->queueManager->restartWorker($queue);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "Worker for queue '{$queue}' restarted successfully"
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "Failed to restart worker for queue '{$queue}'"
        ], 500);
    }

    public function getStatus(Request $request)
    {
        $queue = $request->input('queue', 'default');
        $status = $this->queueManager->getWorkerStatus($queue);

        return response()->json($status);
    }

    public function getLogs(Request $request)
    {
        $logs = QueueJobLog::recent(100)->get();

        return response()->json($logs);
    }

    public function getLogDetails($id)
    {
        $log = QueueJobLog::find($id);

        if (!$log) {
            return response()->json(['error' => 'Log not found'], 404);
        }

        return response()->json([
            'id' => $log->id,
            'job_id' => $log->job_id,
            'job_type' => $log->job_type,
            'queue' => $log->queue,
            'status' => $log->status,
            'execution_time' => $log->execution_time,
            'output' => $log->output,
            'error_message' => $log->error_message,
            'payload' => $log->payload,
            'metadata' => $log->metadata,
            'created_at' => $log->created_at,
        ]);
    }

    public function clearQueue(Request $request)
    {
        $queue = $request->input('queue', 'default');

        try {
            DB::table('jobs')
                ->where('queue', $queue)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Queue '{$queue}' cleared successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to clear queue: " . $e->getMessage()
            ], 500);
        }
    }

    public function retryFailedJobs(Request $request)
    {
        try {
            \Artisan::call('queue:retry', ['--all' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Failed jobs queued for retry'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to retry jobs: " . $e->getMessage()
            ], 500);
        }
    }
}
