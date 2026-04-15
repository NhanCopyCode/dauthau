<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->json('raw_json')->nullable()->after('scope_table');
        });
    }

    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dropColumn('raw_json');
        });
    }
};