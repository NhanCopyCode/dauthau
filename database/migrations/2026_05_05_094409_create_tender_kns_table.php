<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_kns', function (Blueprint $table) {
            $table->id();

            $table->string('ref_id')->unique(); // id của KN
            $table->string('notify_no')->index();

            $table->string('req_no')->nullable();
            $table->string('req_name')->nullable();

            $table->text('bid_name')->nullable();

            $table->string('contractor_code')->nullable();
            $table->text('contractor_name')->nullable();

            $table->timestamp('req_date')->nullable();
            $table->timestamp('res_date')->nullable();

        
            $table->longText('req_content')->nullable();
            $table->longText('res_content')->nullable();

            $table->string('req_file_id')->nullable();
            $table->string('req_file_name')->nullable();

            $table->string('res_file_id')->nullable();
            $table->string('res_file_name')->nullable();

            $table->json('raw')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_kns');
    }
};
