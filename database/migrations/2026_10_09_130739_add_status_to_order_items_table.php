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
        // Thêm cột status vào order_items để theo dõi trạng thái từng dòng vé
        Schema::table('order_items', function (Blueprint $table) {
            // active: đang giữ / completed: đã thanh toán / cancelled: đã hủy/hết hạn
            $table->string('status')->default('active')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
