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
        Schema::create('tender_hntdts', function (Blueprint $table) {
            $table->id();

            $table->string('notify_no')->index();
            $table->string('notify_version')->nullable();

            $table->json('data')->nullable(); 
            $table->json('raw')->nullable();

            $table->timestamps();

            $table->unique(['notify_no', 'notify_version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_hntdts');
    }
};
