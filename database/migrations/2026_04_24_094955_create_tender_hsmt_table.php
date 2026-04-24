<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tender_hsmt', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tender_id')->index();
            
            $table->uuid('notify_id')->nullable()->index();

            $table->enum('type', ['online', 'offline'])->index();
            $table->string('process_apply')->nullable()->index();

            // ONLINE
            $table->string('chapter_code')->nullable()->index();
            $table->string('form_code')->nullable()->index();
            $table->json('bidding_data')->nullable();

            // OFFLINE
            $table->string('file_id')->nullable()->index();
            $table->string('file_name')->nullable();
            $table->string('decision_no')->nullable();
            $table->timestamp('decision_date')->nullable();
            $table->json('other_files')->nullable();

            // RAW
            $table->longText('raw_response')->nullable();

            $table->timestamps();

            // ✅ FIX: unique theo từng form
            $table->unique(['tender_id', 'chapter_code', 'form_code'], 'unique_hsmt_online');

            $table->foreignId('tender_id')
                ->constrained('tenders') // table tenders
                ->cascadeOnDelete();
            $table->index(['tender_id', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_hsmt');
    }
};
