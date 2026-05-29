<?php

namespace App\Services;

class LogClassifier
{
    /**
     * Classify a log entry into lifecycle statuses.
     * Priority: explicit context.status -> heuristics on level/message/context
     * Returns array: [status => 'running|success|failed|info', event_type => string|null, is_terminal => bool]
     */
    public function classify(array $log): array
    {
        $context = $log['context'] ?? [];

        // Prefer explicit context status if present
        if (!empty($context['status'])) {
            $s = strtolower((string) $context['status']);
            $mapped = $this->mapStatus($s);
            return [
                'status' => $mapped,
                'event_type' => $context['event_type'] ?? null,
                'is_terminal' => in_array($mapped, ['success', 'failed']),
            ];
        }

        // Try context event_type (broader token matching)
        if (!empty($context['event_type'])) {
            $et = (string) $context['event_type'];
            $etU = strtoupper($et);
            // Success-like tokens
            if (str_contains($etU, 'SUCCESS') || str_contains($etU, 'DONE') || str_contains($etU, 'FINISH') || str_contains($etU, 'COMPLET')) {
                return ['status' => 'success', 'event_type' => $et, 'is_terminal' => true];
            }
            // Failed-like tokens
            if (str_contains($etU, 'FAILED') || str_contains($etU, 'ERROR') || str_contains($etU, 'EXCEPTION') || str_contains($etU, 'TIMEOUT') || str_contains($etU, 'CRITICAL')) {
                return ['status' => 'failed', 'event_type' => $et, 'is_terminal' => true];
            }
            // Running / processing tokens
            if (str_contains($etU, 'START') || str_contains($etU, 'RUN') || str_contains($etU, 'PROCESS') || str_contains($etU, 'REQUEST')) {
                return ['status' => 'running', 'event_type' => $et, 'is_terminal' => false];
            }
        }

        // Heuristic from level and message
        $level = strtolower($log['level'] ?? 'info');
        $message = strtoupper($log['message'] ?? '');

        // Failed heuristics (broader)
        if ($level === 'error' || str_contains($message, 'FAILED') || str_contains($message, 'CRITICAL') || str_contains($message, 'PERMANENTLY FAILED') || str_contains($message, 'EXCEPTION') || str_contains($message, 'TIMEOUT')) {
            return ['status' => 'failed', 'event_type' => $context['event_type'] ?? null, 'is_terminal' => true];
        }

        // Success heuristics (broader)
        if (str_contains($message, 'SUCCESS') || str_contains($message, 'DONE') || str_contains($message, 'FINISHED') || str_contains($message, 'COMPLET')) {
            return ['status' => 'success', 'event_type' => $context['event_type'] ?? null, 'is_terminal' => true];
        }

        // Running heuristics (include processing tokens)
        if (str_contains($message, 'START') || str_contains($message, 'REQUEST START') || str_contains($message, 'PROBE') || str_contains($message, 'BEGIN') || str_contains($message, 'PROCESS') || str_contains($message, 'RUNNING')) {
            return ['status' => 'running', 'event_type' => $context['event_type'] ?? null, 'is_terminal' => false];
        }

        // Fallback
        return ['status' => 'info', 'event_type' => $context['event_type'] ?? null, 'is_terminal' => false];
    }

    protected function mapStatus(string $s): string
    {
        $map = [
            'running' => 'running',
            'started' => 'running',
            'success' => 'success',
            'done' => 'success',
            'finished' => 'success',
            'failed' => 'failed',
            'error' => 'failed',
        ];

        return $map[$s] ?? 'info';
    }
}
