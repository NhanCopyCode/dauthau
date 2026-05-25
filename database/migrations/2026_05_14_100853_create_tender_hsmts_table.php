<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_hsmts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tender_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

            $table->uuid('notify_id')->nullable()->index();

            $table->enum('type', ['online', 'offline'])
                ->nullable()
                ->index();

            $table->string('process_apply')
                ->nullable()
                ->index();

            /*
            |--------------------------------------------------------------------------
            | Final Render Model
            |--------------------------------------------------------------------------
            */

            // frontend render trực tiếp
            $table->json('view_json')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Optional Raw Backup
            |--------------------------------------------------------------------------
            */

            // optional để debug
            $table->longText('raw_json')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Stats
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('chapter_count')
                ->default(0);

            $table->unsignedInteger('attachment_count')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_hsmts');
    }
};
