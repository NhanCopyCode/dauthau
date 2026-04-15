<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dateTime('reoffer_start_time')->nullable()->after('bid_open_date');
            $table->dateTime('reoffer_end_time')->nullable()->after('reoffer_start_time');

            // optional index nếu cần filter
            $table->index('reoffer_start_time');
            $table->index('reoffer_end_time');
        });
    }

    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dropIndex(['reoffer_start_time']);
            $table->dropIndex(['reoffer_end_time']);

            $table->dropColumn([
                'reoffer_start_time',
                'reoffer_end_time'
            ]);
        });
    }
};
