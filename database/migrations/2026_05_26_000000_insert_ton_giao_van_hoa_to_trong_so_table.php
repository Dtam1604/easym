<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra và chèn ton_giao
        if (DB::table('trong_so_thuat_toan')->where('ten_tieu_chi', 'ton_giao')->count() == 0) {
            DB::table('trong_so_thuat_toan')->insert([
                'ten_tieu_chi' => 'ton_giao',
                'tieu_de_hien_thi' => 'Tôn giáo',
                'loai_input' => 'select',
                'trong_so_nen' => 2.0,
                'he_so_uu_tien' => 1.5,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Kiểm tra và chèn van_hoa
        if (DB::table('trong_so_thuat_toan')->where('ten_tieu_chi', 'van_hoa')->count() == 0) {
            DB::table('trong_so_thuat_toan')->insert([
                'ten_tieu_chi' => 'van_hoa',
                'tieu_de_hien_thi' => 'Văn hóa vùng miền',
                'loai_input' => 'select',
                'trong_so_nen' => 1.5,
                'he_so_uu_tien' => 1.5,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('trong_so_thuat_toan')->whereIn('ten_tieu_chi', ['ton_giao', 'van_hoa'])->delete();
    }
};
