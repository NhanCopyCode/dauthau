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
        Schema::create('crawl_tasks', function (Blueprint $table) {

            $table->id();

            $table->string('type');
            // full | daily | range

            $table->string('status')->default('pending');
            // pending | running | completed | failed

            $table->dateTime('from_date')->nullable();
            $table->dateTime('to_date')->nullable();

            $table->integer('total_pages')->default(0);
            $table->integer('processed_pages')->default(0);

            $table->integer('total_items')->default(0);
            $table->integer('processed_items')->default(0);

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->text('error')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crawl_tasks');
    }
};
