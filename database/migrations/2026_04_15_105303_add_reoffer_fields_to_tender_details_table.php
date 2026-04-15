<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->decimal('ceiling_price', 18, 2)->nullable()->after('lot_count');

            $table->decimal('price_step', 18, 2)->nullable()->after('ceiling_price');

            $table->integer('bid_validity_period_reoffer')->nullable()->after('price_step');
            $table->string('bid_validity_period_unit_reoffer', 5)->nullable()->after('bid_validity_period_reoffer');
        });
    }

    public function down(): void
    {
        Schema::table('tender_details', function (Blueprint $table) {
            $table->dropColumn([
                'ceiling_price',
                'price_step',
                'bid_validity_period_reoffer',
                'bid_validity_period_unit_reoffer'
            ]);
        });
    }
};
