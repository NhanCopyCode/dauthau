<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->char('bid_validity_period_unit', 1)
                ->nullable()
                ->after('bid_validity_period')
                ->comment('D: Day, M: Month');
        });
    }

    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dropColumn('bid_validity_period_unit');
        });
    }
};
