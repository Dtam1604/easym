<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('dia_ban_quan_ly')->nullable()->after('vai_tro');
            $table->boolean('trang_thai_khoa')->default(false)->after('dia_ban_quan_ly');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn(['dia_ban_quan_ly', 'trang_thai_khoa']);
        });
    }
};
