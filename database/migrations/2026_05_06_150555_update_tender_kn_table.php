<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_kns', function (Blueprint $table) {

            // ✅ drop column an toàn
            $columnsToDrop = [
                'ref_id',
                'req_no',
                'req_name',
                'bid_name',
                'contractor_code',
                'contractor_name',
                'req_date',
                'res_date',
                'req_content',
                'res_content',
                'req_file_id',
                'req_file_name',
                'res_file_id',
                'res_file_name',
            ];

            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('tender_kns', $col)) {
                    $table->dropColumn($col);
                }
            }

            // ✅ add column nếu chưa có
            if (!Schema::hasColumn('tender_kns', 'kn_count')) {
                $table->integer('kn_count')->nullable()->after('notify_no');
            }

            if (!Schema::hasColumn('tender_kns', 'latest_req_date')) {
                $table->dateTime('latest_req_date')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'latest_res_date')) {
                $table->dateTime('latest_res_date')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'data')) {
                $table->json('data')->nullable();
            }
        });

        // ⚠️ change() tách riêng để tránh lỗi DBAL
        if (Schema::hasColumn('tender_kns', 'raw')) {
            DB::statement('ALTER TABLE tender_kns MODIFY raw JSON NULL');
        }
    }

    public function down(): void
    {
        Schema::table('tender_kns', function (Blueprint $table) {

            // ❌ xoá field mới
            $columnsToDrop = [
                'kn_count',
                'latest_req_date',
                'latest_res_date',
                'data',
            ];

            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('tender_kns', $col)) {
                    $table->dropColumn($col);
                }
            }

            // ✅ restore lại field cũ (QUAN TRỌNG)
            if (!Schema::hasColumn('tender_kns', 'ref_id')) {
                $table->string('ref_id')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'req_no')) {
                $table->string('req_no')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'req_name')) {
                $table->string('req_name')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'bid_name')) {
                $table->string('bid_name')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'contractor_code')) {
                $table->string('contractor_code')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'contractor_name')) {
                $table->string('contractor_name')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'req_date')) {
                $table->dateTime('req_date')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'res_date')) {
                $table->dateTime('res_date')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'req_content')) {
                $table->longText('req_content')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'res_content')) {
                $table->longText('res_content')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'req_file_id')) {
                $table->string('req_file_id')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'req_file_name')) {
                $table->string('req_file_name')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'res_file_id')) {
                $table->string('res_file_id')->nullable();
            }

            if (!Schema::hasColumn('tender_kns', 'res_file_name')) {
                $table->string('res_file_name')->nullable();
            }
        });
    }
};
