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
        if (!Schema::hasColumn('phong_tro', 'gioi_tinh_cho_thue')) {
            Schema::table('phong_tro', function (Blueprint $table) {
                $table->string('gioi_tinh_cho_thue', 10)->default('Tat ca')->after('dien_tich');
            });
        } else {
            Schema::table('phong_tro', function (Blueprint $table) {
                $table->string('gioi_tinh_cho_thue', 10)->default('Tat ca')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không xóa cột nếu cột này đã được định nghĩa ở migration gốc
    }
};
