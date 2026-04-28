<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tender_hsmt_chapters', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('raw');
            $table->json('bidding_raw')->nullable()->after('attachments');
        });
    }

    public function down(): void
    {
        Schema::table('tender_hsmt_chapters', function (Blueprint $table) {
            $table->dropColumn(['attachments', 'bidding_raw']);
        });
    }
};
