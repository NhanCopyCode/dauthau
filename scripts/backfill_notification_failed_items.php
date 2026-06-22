<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notification;

$count = 0;

Notification::where(function ($q) {
    $q->whereNull('failed_items')->orWhere('failed_items', 0);
})->whereHas('crawlTask', function ($q) {
    $q->where('failed_items', '>', 0);
})->chunk(100, function ($notifications) use (&$count) {
    foreach ($notifications as $n) {
        $taskFailedItems = (int) ($n->crawlTask->failed_items ?? 0);
        if ($taskFailedItems > 0) {
            $n->update(['failed_items' => $taskFailedItems]);
            echo "Updated notification #{$n->id} with failed_items: {$taskFailedItems}\n";
            $count++;
        }
    }
});

echo "Done. Updated {$count} notifications.\n";
