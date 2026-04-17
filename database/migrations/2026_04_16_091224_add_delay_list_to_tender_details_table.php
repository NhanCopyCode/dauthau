<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->json('delay_list')->nullable()->after('raw_json');
        });
    }

    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dropColumn('delay_list');
        });
    }
};
