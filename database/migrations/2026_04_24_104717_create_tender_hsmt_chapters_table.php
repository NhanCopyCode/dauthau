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
        Schema::create('tender_hsmt_chapters', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tender_id')->index();

            // API fields
            $table->uuid('api_id')->nullable()->index(); // id từ API
            $table->string('code')->nullable()->index(); // code: P1, BD.MT...
            $table->string('pcode')->nullable()->index(); // parent code

            $table->string('name')->nullable(); // hiển thị chính
            $table->string('name_en')->nullable();

            $table->text('description')->nullable();

            $table->integer('order_index')->default(0);
            $table->integer('level')->default(0); // lev

            $table->boolean('is_webform')->default(false);

            $table->string('bid_form')->nullable();
            $table->string('bid_field')->nullable();
            $table->string('bid_file')->nullable();
            $table->string('contract_type')->nullable();

            $table->string('process_type')->nullable();

            $table->json('raw')->nullable(); // backup full API

            $table->timestamps();

            // tránh duplicate
            $table->unique(['tender_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_hsmt_chapters');
    }
};
