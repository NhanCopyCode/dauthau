<?php
// scripts/debug_retry_task5.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CrawlTask;
use App\Services\LogClassifier;

$taskId = 5;
$task = CrawlTask::find($taskId);
if (!$task) {
    echo "Task not found\n";
    exit(1);
}

$logs = $task->logs()->get();
$classifier = new LogClassifier();

foreach ($logs as $log) {
    $derived = $classifier->classify(['level' => $log->level, 'message' => $log->message, 'context' => $log->context ?? []]);
    if ($derived['status'] !== 'failed') continue;
    $ctx = $log->context ?? [];
    $failedAt = $log->created_at;
    // infer job
    $controller = new App\Http\Controllers\CrawlRetryController();
    $inferred = null;
    // reuse protected method via reflection
    $ref = new ReflectionClass($controller);
    $m = $ref->getMethod('inferJobFromContext');
    $m->setAccessible(true);
    $inferred = $m->invoke($controller, $ctx, $log->message);

    echo "\nFAILED LOG id={$log->id} at={$failedAt} message={$log->message}\n";
    echo "inferred=" . json_encode($inferred) . "\n";

    if (!$inferred) {
        echo " => cannot infer job\n";
        continue;
    }

    list($jobClass, $params, $queue) = $inferred;
    // find later success or running logs that match
    $later = [];
    foreach ($logs as $l2) {
        if (empty($l2->created_at)) continue;
        if ($l2->created_at->lte($failedAt)) continue;
        $d2 = $classifier->classify(['level' => $l2->level, 'message' => $l2->message, 'context' => $l2->context ?? []]);
        if (!in_array($d2['status'], ['success', 'running'])) continue;
        $c2 = $l2->context ?? [];
        // paramsMatch exists in controller, call via reflection
        $m2 = $ref->getMethod('paramsMatch');
        $m2->setAccessible(true);
        $match = $m2->invoke($controller, $c2, $params);
        if ($match) {
            $later[] = ['id' => $l2->id, 'status' => $d2['status'], 'created_at' => (string)$l2->created_at, 'message' => $l2->message];
        }
    }

    echo "later matches: " . json_encode($later) . "\n";
}
