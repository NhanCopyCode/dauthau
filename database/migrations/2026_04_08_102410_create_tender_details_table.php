<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop table nếu đã tồn tại
        if (Schema::hasTable('tender_details')) {
            Schema::drop('tender_details');
        }

        Schema::create('tender_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tender_id')->nullable();

            // IDENTIFICATION
            $table->string('notify_no', 50);
            $table->string('notify_version', 10)->nullable();
            $table->string('plan_no', 50)->nullable();

            // BASIC INFO
            $table->dateTime('public_date')->nullable();
            $table->string('plan_type', 50)->nullable();
            $table->text('plan_name')->nullable();

            // TENDER INFO
            $table->text('bid_name')->nullable();
            $table->string('investor_name')->nullable();
            $table->string('capital_detail')->nullable();
            $table->string('invest_field', 50)->nullable();
            $table->string('bid_form', 50)->nullable();
            $table->string('contract_type', 50)->nullable();
            $table->boolean('is_domestic')->nullable();
            $table->string('bid_mode', 50)->nullable();
            $table->integer('contract_period')->nullable();
            $table->boolean('is_multi_lot')->nullable();

            // BIDDING METHOD
            $table->boolean('is_online_bidding')->nullable();
            $table->text('issue_location')->nullable();
            $table->text('receive_location')->nullable();
            $table->text('execution_location')->nullable();

            // BIDDING TIME
            $table->dateTime('bid_close_date')->nullable();
            $table->dateTime('bid_open_date')->nullable();
            $table->text('bid_open_location')->nullable();
            $table->integer('bid_validity_period')->nullable();

            // FINANCIAL
            $table->decimal('bid_guarantee_amount', 18, 2)->nullable();
            $table->text('bid_guarantee_form')->nullable();
            $table->decimal('bid_submission_fee', 18, 2)->nullable();

            // APPROVAL
            $table->string('approval_decision_number', 100)->nullable();
            $table->dateTime('approval_decision_date')->nullable();
            $table->string('approval_agency')->nullable();
            $table->string('approval_file_name')->nullable();
            $table->string('modification_file_name')->nullable();

            // TIMESTAMPS
            $table->timestamps();

            // INDEXES
            $table->index('notify_no', 'idx_notify_no');
            $table->index('plan_no', 'idx_plan_no');
            $table->index('public_date', 'idx_public_date');
            $table->index('bid_close_date', 'idx_bid_close_date');

            $table->foreign('tender_id')
                ->references('id')
                ->on('tenders')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('tender_details')) {
            Schema::drop('tender_details');
        }
    }
};
