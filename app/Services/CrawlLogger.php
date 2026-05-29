<?php

namespace App\Services;

use App\Models\CrawlLog;
use Illuminate\Support\Facades\Log;

class CrawlLogger
{
    public function info(int $taskId, string $message, array $context = [], ?string $queue = null): void
    {
        $this->write('info', $taskId, $message, $context, $queue);
        Log::info($message, array_merge($context, ['task_id' => $taskId, 'queue' => $queue]));
    }

    public function warning(int $taskId, string $message, array $context = [], ?string $queue = null): void
    {
        $this->write('warning', $taskId, $message, $context, $queue);
        Log::warning($message, array_merge($context, ['task_id' => $taskId, 'queue' => $queue]));
    }

    public function error(int $taskId, string $message, array $context = [], ?string $queue = null): void
    {
        $this->write('error', $taskId, $message, $context, $queue);
        Log::error($message, array_merge($context, ['task_id' => $taskId, 'queue' => $queue]));
    }

    protected function write(string $level, int $taskId, string $message, array $context = [], ?string $queue = null): void
    {
        // Normalize context and detect timeout-like errors
        $ctx = $context ?: [];

        // Detect exception message strings indicating timeouts
        $msgCandidates = [];
        if (isset($ctx['message'])) $msgCandidates[] = $ctx['message'];
        if (isset($ctx['error'])) $msgCandidates[] = $ctx['error'];
        if (isset($ctx['exception']) && is_string($ctx['exception'])) $msgCandidates[] = $ctx['exception'];

        $joined = strtolower(implode(' ', $msgCandidates));
        if (
            str_contains($joined, 'curl error 28') ||
            str_contains($joined, 'connectionexception') ||
            str_contains($joined, 'ssl connection timeout') ||
            str_contains($joined, 'timed out')
        ) {
            $ctx['error_type'] = 'timeout';
        }

        // Ensure numeric metrics are cast to int (if provided)
        $metrics = [
            'total_packages',
            'processed_packages',
            'success_packages',
            'failed_packages',
            'timeout_packages',
            'skipped_packages'
        ];
        foreach ($metrics as $m) {
            if (isset($ctx[$m])) {
                $ctx[$m] = is_numeric($ctx[$m]) ? (int) $ctx[$m] : $ctx[$m];
            }
        }

        // Standardize job_name using context or backtrace
        if (empty($ctx['job_name'])) {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
            foreach ($bt as $frame) {
                if (!empty($frame['class']) && stripos($frame['class'], 'App\\Jobs') !== false) {
                    $ctx['job_name'] = $frame['class'];
                    break;
                }
            }
        }

        // Normalize execution time if present
        if (isset($ctx['execution_time_ms'])) {
            $ctx['execution_time_ms'] = is_numeric($ctx['execution_time_ms']) ? (int) $ctx['execution_time_ms'] : $ctx['execution_time_ms'];
        } elseif (isset($ctx['duration_seconds'])) {
            $ctx['execution_time_ms'] = (int) round($ctx['duration_seconds'] * 1000);
        } elseif (isset($ctx['duration'])) {
            // duration may be seconds
            $ctx['execution_time_ms'] = is_numeric($ctx['duration']) ? (int) round($ctx['duration'] * 1000) : $ctx['duration'];
        }

        // Infer status
        if (empty($ctx['status'])) {
            $upper = strtoupper($message);
            if (str_contains($upper, 'START') || str_contains($upper, 'BEGIN')) {
                $ctx['status'] = 'running';
            } elseif (str_contains($upper, 'DONE') || str_contains($upper, 'FINISHED') || str_contains($upper, 'SUCCESS')) {
                $ctx['status'] = 'done';
            } elseif ($level === 'error') {
                $ctx['status'] = 'failed';
            }
        }

        // Extract exception details when exception object provided
        if (isset($ctx['exception']) && $ctx['exception'] instanceof \Throwable) {
            $ex = $ctx['exception'];
            $ctx['exception_class'] = get_class($ex);
            $ctx['stacktrace'] = $ex->getTraceAsString();
        }

        // system_error if exception present or timeout
        if (!isset($ctx['system_error'])) {
            $ctx['system_error'] = isset($ctx['exception_class']) || (isset($ctx['error_type']) && $ctx['error_type'] === 'timeout') || $level === 'error';
        }

        CrawlLog::create([
            'crawl_task_id' => $taskId,
            'queue' => $queue,
            'level' => $level,
            'message' => $message,
            'context' => empty($ctx) ? null : $ctx,
            'created_at' => now(),
        ]);
    }
}
