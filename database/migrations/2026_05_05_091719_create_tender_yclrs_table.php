<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_yclrs', function (Blueprint $table) {
            $table->id();

            $table->string('notify_no')->index();
            $table->string('notify_version')->nullable();

            // lưu full list luôn
            $table->json('data')->nullable();

            // raw full response
            $table->json('raw')->nullable();

            $table->timestamps();

            $table->unique(['notify_no', 'notify_version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_yclrs');
    }
};
