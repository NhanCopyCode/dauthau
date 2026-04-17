<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->string('bid_no', 50)
                ->nullable()
                ->after('notify_no');

        });
    }

    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dropColumn('bid_no');
        });
    }
};