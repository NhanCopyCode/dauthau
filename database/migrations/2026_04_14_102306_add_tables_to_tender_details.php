<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->json('lot_table')->nullable()->after('modification_file_name');
            $table->json('scope_table')->nullable()->after('lot_table');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            //
            $table->dropColumn('lot_table');
            $table->dropColumn('scope_table');
        });
    }
};
