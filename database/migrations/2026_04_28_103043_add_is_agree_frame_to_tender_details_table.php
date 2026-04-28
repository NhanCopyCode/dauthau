<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->tinyInteger('is_agree_frame')
                ->nullable()
                ->after('bid_mode') 
                ->comment('1 = agree frame, 0/null = normal');
        });
    }

    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dropColumn('is_agree_frame');
        });
    }
};
