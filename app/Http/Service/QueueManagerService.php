<?php

namespace App\Http\Service;

use App\Models\QueueWorker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Illuminate\Support\Facades\DB;

class QueueManagerService
{
    protected string $phpBinary;
    protected string $artisanPath;

    public function __construct()
    {
        $this->phpBinary = (new PhpExecutableFinder())->find();
        $this->artisanPath = base_path('artisan');
    }

    public function startWorker(string $queue = 'default', array $options = []): bool
    {
        try {
            // Проверяем, нет ли уже запущенного воркера
            if ($this->isWorkerRunning($queue)) {
                Log::warning("Worker for queue {$queue} is already running");
                return false;
            }

            $command = [
                $this->phpBinary,
                $this->artisanPath,
                'queue:work',
                'database',
                '--queue=' . $queue,
                '--sleep=3',
                '--tries=3',
                '--timeout=3600',
                '--memory=256',
                '--stop-when-empty',
            ];

            // Добавляем дополнительные опции
            if ($options['once'] ?? false) {
                $command[] = '--once';
            }

            // Запускаем процесс в фоне
            $process = Process::start(implode(' ', $command));

            // Сохраняем информацию о воркере
            $worker = QueueWorker::updateOrCreate(
                ['queue' => $queue],
                [
                    'status' => 'running',
                    'pid' => $process->id(),
                    'last_heartbeat' => now(),
                    'options' => $options,
                    'processed_jobs' => 0,
                    'failed_jobs' => 0,
                ]
            );

            Log::info("Worker started for queue {$queue}", [
                'pid' => $process->id(),
                'queue' => $queue
            ]);

            Cache::put("worker:{$queue}:pid", $process->id(), now()->addHour());

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to start worker for queue {$queue}: " . $e->getMessage());
            return false;
        }
    }

    public function stopWorker(string $queue = 'default'): bool
    {
        try {
            $worker = QueueWorker::where('queue', $queue)->first();

            if (!$worker) {
                return true; // Если нет записи, считаем что остановлен
            }

            // Отправляем сигнал остановки
            if ($worker->pid) {
                // Определяем ОС более надежным способом
                $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

                if ($isWindows) {
                    // Для Windows
                    exec("taskkill /PID {$worker->pid} /F 2>&1", $output, $returnCode);
                    Log::info("Windows kill command executed", [
                        'pid' => $worker->pid,
                        'output' => $output,
                        'return_code' => $returnCode
                    ]);
                } else {
                    // Для Linux/Unix систем

                    // Сначала пытаемся мягко остановить (SIGTERM = 15)
                    exec("kill {$worker->pid} 2>&1", $output, $returnCode);
                    Log::info("Unix kill command executed", [
                        'pid' => $worker->pid,
                        'output' => $output,
                        'return_code' => $returnCode
                    ]);

                    // Ждем немного
                    sleep(2);

                    // Если процесс все еще жив, принудительно завершаем (SIGKILL = 9)
                    if ($this->isPidAlive($worker->pid)) {
                        exec("kill -9 {$worker->pid} 2>&1", $output, $returnCode);
                        Log::info("Unix kill -9 command executed", [
                            'pid' => $worker->pid,
                            'output' => $output,
                            'return_code' => $returnCode
                        ]);
                    }
                }
            }

            // Обновляем статус
            $worker->update([
                'status' => 'stopped',
                'pid' => null,
                'last_heartbeat' => null
            ]);

            // Очищаем кэш
            Cache::forget("worker:{$queue}:pid");

            Log::info("Worker stopped for queue {$queue}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to stop worker for queue {$queue}: " . $e->getMessage());
            return false;
        }
    }

    private function isPidAlive($pid): bool
    {
        if (!$pid) {
            return false;
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            $output = shell_exec("tasklist /FI \"PID eq {$pid}\" 2>NUL");
            return $output && str_contains($output, (string)$pid);
        }

        // Для Linux/Unix
        $output = shell_exec("ps -p {$pid} 2>&1");
        return $output && str_contains($output, (string)$pid);
    }

    public function restartWorker(string $queue = 'default'): bool
    {
        $this->stopWorker($queue);
        sleep(3); // Даем больше времени на завершение
        return $this->startWorker($queue);
    }

    public function isWorkerRunning(string $queue = 'default'): bool
    {
        $worker = QueueWorker::where('queue', $queue)->first();

        if (!$worker || $worker->status !== 'running') {
            return false;
        }

        return $this->isWorkerAlive($worker);
    }

    private function isWorkerAlive(QueueWorker $worker): bool
    {
        if (!$worker->pid || !$worker->last_heartbeat) {
            return false;
        }

        // Проверяем, что heartbeat не старше 5 минут
        if ($worker->last_heartbeat->diffInMinutes(now()) > 5) {
            return false;
        }

        // Дополнительная проверка через систему
        return $this->isPidAlive($worker->pid);
    }

    public function getWorkerStatus(string $queue = 'default'): array
    {
        $worker = QueueWorker::where('queue', $queue)->first();

        if (!$worker) {
            // Создаем запись по умолчанию, если ее нет
            $worker = QueueWorker::create([
                'queue' => $queue,
                'status' => 'stopped',
                'processed_jobs' => 0,
                'failed_jobs' => 0,
            ]);
        }

        $isRunning = $this->isWorkerRunning($queue);

        return [
            'status' => $worker->status ?? 'stopped',
            'running' => $isRunning,
            'pid' => $worker->pid ?? null,
            'queue' => $worker->queue,
            'processed_jobs' => $worker->processed_jobs ?? 0,
            'failed_jobs' => $worker->failed_jobs ?? 0,
            'last_heartbeat' => $worker->last_heartbeat,
            'uptime' => $worker->last_heartbeat ?
                $worker->last_heartbeat->diffForHumans(null, true) : null,
        ];
    }

    public function getAllWorkersStatus(): array
    {
        $queues = ['default', 'exports', 'sentences']; // Ваши очереди
        $statuses = [];

        foreach ($queues as $queue) {
            $statuses[$queue] = $this->getWorkerStatus($queue);
        }

        return $statuses;
    }

    public function getQueueStats(): array
    {
        return [
            'pending_jobs' => DB::table('jobs')->count() ?? 0,
            'failed_jobs' => DB::table('failed_jobs')->count() ?? 0,
            'queues' => [
                'default' => DB::table('jobs')->where('queue', 'default')->count() ?? 0,
                'exports' => DB::table('jobs')->where('queue', 'exports')->count() ?? 0,
                'sentences' => DB::table('jobs')->where('queue', 'sentences')->count() ?? 0,
            ]
        ];
    }

    // Метод для обновления счетчиков
    public function incrementProcessedJobs(string $queue = 'default'): void
    {
        QueueWorker::where('queue', $queue)->increment('processed_jobs');
    }

    public function incrementFailedJobs(string $queue = 'default'): void
    {
        QueueWorker::where('queue', $queue)->increment('failed_jobs');
    }

    // Метод для обновления heartbeat
    public function updateHeartbeat(string $queue = 'default'): void
    {
        QueueWorker::where('queue', $queue)->update(['last_heartbeat' => now()]);
    }
}
