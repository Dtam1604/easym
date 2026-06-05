<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 5 người dùng với đủ các vai trò
        $users = [
            [
                'ho_ten' => 'Nguyễn Quản Trị',
                'email' => 'admin@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0901234567',
                'vai_tro' => 'admin',
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Trần Chủ Trọ',
                'email' => 'chutro1@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912345678',
                'vai_tro' => 'chu_tro',
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Lê Chủ Trọ 2',
                'email' => 'chutro2@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0923456789',
                'vai_tro' => 'chu_tro',
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Phạm Sinh Viên',
                'email' => 'sinhvien@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0934567890',
                'vai_tro' => 'nguoi_tim_tro',
                'khao_sat_loi_song' => json_encode(['gio_giac' => '23h', 'do_sach_se' => 5]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Hoàng Thực Địa',
                'email' => 'ctv@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0945678901',
                'vai_tro' => 'cong_tac_vien',
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('nguoi_dung')->insert($users);

        // Lấy ID của các chủ trọ vừa tạo (Email: chutro1 và chutro2)
        $chuTro1 = DB::table('nguoi_dung')->where('email', 'chutro1@easym.vn')->first()->id;
        $chuTro2 = DB::table('nguoi_dung')->where('email', 'chutro2@easym.vn')->first()->id;

        // 2. Tạo 10 phòng trọ xung quanh ĐH Lâm Nghiệp (Lat: 20.941, Lng: 105.558)
        $rooms = [];
        for ($i = 1; $i <= 10; $i++) {
            $lat = 20.941 + (rand(-50, 50) / 10000);
            $lng = 105.558 + (rand(-50, 50) / 10000);
            
            // Chia đều cho 2 chủ trọ
            $id_chu_tro = ($i <= 5) ? $chuTro1 : $chuTro2;

            $rooms[] = [
                'id_chu_tro' => $id_chu_tro,
                'tieu_de' => 'Phòng trọ Lâm Nghiệp - Mẫu ' . $i,
                'mo_ta' => 'Phòng mới xây, thiết kế đẹp, gần khuôn viên trường ĐH Lâm Nghiệp.',
                'gia_phong' => rand(15, 30) * 100000,
                'dia_chi_chi_tiet' => 'Số ' . rand(1, 100) . ' Ngõ ' . rand(1, 50) . ', Đường Lâm Nghiệp',
                'anh_phong' => json_encode(['https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=500&q=80']),
                'anh_phap_ly' => json_encode(['https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=500&q=80']),
                'muc_do_xac_thuc' => ($i % 3 == 0) ? 2 : 1, // 1/3 đã xác thực, 2/3 chờ duyệt
                'khao_sat_loi_song_chu_tro' => json_encode(['gio_giac' => 'chim_som', 'do_sach_se' => 4, 'hut_thuoc' => '0']),
                'vi_tri' => DB::raw("ST_GeomFromText('POINT({$lng} {$lat})', 4326)"),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('phong_tro')->insert($rooms);

        // 3. Khởi tạo Trọng số thuật toán
        $weights = [
            ['ten_tieu_chi' => 'gio_giac', 'trong_so_nen' => 2.0, 'he_so_uu_tien' => 2.0],
            ['ten_tieu_chi' => 'do_sach_se', 'trong_so_nen' => 1.5, 'he_so_uu_tien' => 1.5],
            ['ten_tieu_chi' => 'hut_thuoc', 'trong_so_nen' => 3.0, 'he_so_uu_tien' => 2.5],
            ['ten_tieu_chi' => 'nuoi_thu_cung', 'trong_so_nen' => 1.0, 'he_so_uu_tien' => 1.2],
            ['ten_tieu_chi' => 'ban_be_den_choi', 'trong_so_nen' => 1.5, 'he_so_uu_tien' => 1.5],
        ];
        DB::table('trong_so_thuat_toan')->insert($weights);

        // 4. Tạo thêm nhiều tài khoản người tìm trọ
        $this->call(NguoiTimTroSeeder::class);
    }
}
