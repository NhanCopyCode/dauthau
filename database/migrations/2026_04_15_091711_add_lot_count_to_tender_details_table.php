<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->integer('lot_count')->nullable()->after('is_multi_lot');
        });
    }

    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dropColumn('lot_count');
        });
    }
};
