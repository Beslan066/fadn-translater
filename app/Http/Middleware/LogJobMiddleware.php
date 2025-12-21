<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use App\Jobs\LogJobExecution;
use Illuminate\Support\Str;

class LogJobMiddleware
{
    public function handle($job, Closure $next)
    {
        $jobId = Str::uuid()->toString();
        $startTime = microtime(true);
        $jobType = get_class($job);

        try {
            // Логируем начало выполнения
            LogJobExecution::dispatch(
                $jobId,
                $jobType,
                'processing',
                null,
                null,
                null,
                ['payload' => $job->getRawBody() ?? []]
            )->onQueue('logs');

            $result = $next($job);

            $executionTime = round(microtime(true) - $startTime, 2);

            // Логируем успешное завершение
            LogJobExecution::dispatch(
                $jobId,
                $jobType,
                'completed',
                $executionTime,
                'Job completed successfully'
            )->onQueue('logs');

            return $result;

        } catch (\Exception $e) {
            $executionTime = round(microtime(true) - $startTime, 2);

            // Логируем ошибку
            LogJobExecution::dispatch(
                $jobId,
                $jobType,
                'failed',
                $executionTime,
                null,
                $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            )->onQueue('logs');

            throw $e;
        }
    }
}
