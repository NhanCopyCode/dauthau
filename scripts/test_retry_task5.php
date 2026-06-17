<?php
// scripts/test_retry_task5.php
// Usage: php scripts/test_retry_task5.php candidates|retry|jobs

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CrawlTask;
use Illuminate\Support\Facades\DB;

$action = $argv[1] ?? 'candidates';
$taskId = 5;
$task = CrawlTask::find($taskId);
if (!$task) {
    echo json_encode(['error' => "Task not found: $taskId"]) . PHP_EOL;
    exit(1);
}

if ($action === 'candidates') {
    $controller = new App\Http\Controllers\CrawlRetryController();
    $resp = $controller->candidates($task);
    $data = $resp->getData(true);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit(0);
}

if ($action === 'retry') {
    $controller = new App\Http\Controllers\CrawlRetryController();
    $resp = $controller->retry(new Illuminate\Http\Request(), $task);
    $data = $resp->getData(true);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit(0);
}

if ($action === 'jobs') {
    // show last 20 jobs
    $rows = DB::table('jobs')->orderBy('id', 'desc')->limit(20)->get();
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit(0);
}

echo json_encode(['error' => 'unknown action']) . PHP_EOL;
