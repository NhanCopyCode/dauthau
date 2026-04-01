<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {

            // 🔑 Primary
            $table->id()->comment('PK');

            // 🔑 External IDs
            $table->string('egp_id')->unique()->comment('ID từ hệ thống đấu thầu');
            $table->string('notify_id')->nullable()->comment('ID thông báo');
            $table->string('bid_id')->nullable()->comment('ID gói thầu');

            // 🔢 Codes
            $table->string('notify_no')->nullable()->comment('Số TBMT');
            $table->string('notify_version')->nullable()->comment('Version TB');
            $table->string('notify_no_stand')->nullable()->comment('Số TB chuẩn');

            // 📌 Tender info
            $table->text('name')->nullable()->comment('Tên gói thầu');
            $table->json('bid_names')->nullable()->comment('Danh sách tên gói');

            // 👤 Investor
            $table->text('investor')->nullable()->comment('Chủ đầu tư');
            $table->string('investor_code')->nullable()->comment('Mã CĐT');

            // 🌍 Location
            $table->string('province')->nullable()->comment('Tỉnh');
            $table->json('locations')->nullable()->comment('Danh sách địa điểm');

            // 📅 Dates
            $table->dateTime('bid_close_date')->nullable()->comment('Đóng thầu');
            $table->dateTime('bid_open_date')->nullable()->comment('Mở thầu');
            $table->dateTime('public_date')->nullable()->comment('Ngày đăng');
            $table->dateTime('original_public_date')->nullable()->comment('Ngày đăng gốc');

            // 📊 Plan
            $table->string('plan_no')->nullable()->comment('Mã KHLCNT');
            $table->string('plan_type')->nullable()->comment('Loại KH');

            // ⚙️ Tender type
            $table->string('bid_form')->nullable()->comment('Hình thức');
            $table->string('bid_mode')->nullable()->comment('Mode');
            $table->string('process_apply')->nullable()->comment('Quy trình');

            // 📦 Field
            $table->string('invest_field')->nullable()->comment('Lĩnh vực chính');
            $table->json('invest_fields')->nullable()->comment('Danh sách lĩnh vực');

            // 💰 Price
            $table->decimal('bid_price', 20, 2)->nullable()->comment('Giá gói thầu');

            // 📌 Status
            $table->string('status')->nullable()->comment('Trạng thái');
            $table->string('status_for_notify')->nullable()->comment('Trạng thái hiển thị');
            $table->string('type')->nullable()->comment('Loại dữ liệu');
            $table->string('step_code')->nullable()->comment('Bước hiện tại');

            // 📊 Stats
            $table->integer('num_petition')->default(0)->comment('Số kiến nghị');
            $table->integer('num_clarify_req')->default(0)->comment('Yêu cầu làm rõ');
            $table->integer('num_bidder_tech')->default(0)->comment('Nhà thầu kỹ thuật');

            // Kiến nghị Hồ Sơ Mời Thầu (HSMT), Lời Chào Nhà Thầu (LCNT), Kết Quả Lời Chào Nhà Thầu (KQLCNT)
            $table->integer('num_petition_hsmt')->default(0)->comment('KN HSMT');
            $table->integer('num_petition_lcnt')->default(0)->comment('KN LCNT');
            $table->integer('num_petition_kqlcnt')->default(0)->comment('KN KQLCNT');

            // ⚡ Flags
            $table->boolean('is_internet')->nullable()->comment('Qua mạng');
            $table->boolean('is_domestic')->nullable()->comment('Trong nước');
            $table->boolean('is_medicine')->nullable()->comment('Gói y tế');

            // 👤 Creator
            $table->string('created_by')->nullable()->comment('Người tạo');

            // ⭐ Score
            $table->string('score')->nullable()->comment('Điểm');

            // 🕒 Timestamps
            $table->timestamps();

            // 🚀 Index
            $table->index('bid_close_date');
            $table->index('province');
            $table->index('notify_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};