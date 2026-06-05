<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crawl_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('crawl_tasks', 'failed_items')) {
                $table->unsignedInteger('failed_items')->default(0)->after('processed_items');
            }
        });
    }

    public function down(): void
    {
        Schema::table('crawl_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('crawl_tasks', 'failed_items')) {
                $table->dropColumn('failed_items');
            }
        });
    }
};
