<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class NguoiTimTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách ảnh đại diện Unsplash chất lượng cao cho Nam và Nữ
        $maleAvatars = [
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1489980508314-941910ded1f4?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1506803682981-6e718a9dd3ee?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1566492031773-4f4e44671857?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1542909168-82c3e7fdca5c?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1488161628813-04466f872be2?w=200&h=200&fit=crop',
        ];

        $femaleAvatars = [
            'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1554151228-14d9def656e4?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1541643600914-78b084683601?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=200&h=200&fit=crop',
            'https://images.unsplash.com/photo-1598550476439-6847785fce6e?w=200&h=200&fit=crop',
        ];

        // Danh sách thông tin mẫu cho Nam
        $namList = [
            ['ho_ten' => 'Nguyễn Minh Đức', 'nghe_nghiep' => 'Sinh viên ĐH Bách Khoa'],
            ['ho_ten' => 'Lê Hoàng Nam', 'nghe_nghiep' => 'Lập trình viên Backend'],
            ['ho_ten' => 'Trần Thế Anh', 'nghe_nghiep' => 'Sinh viên ĐH Kinh Tế Quốc Dân'],
            ['ho_ten' => 'Phạm Duy Mạnh', 'nghe_nghiep' => 'Nhân viên thiết kế đồ họa'],
            ['ho_ten' => 'Hoàng Minh Tuấn', 'nghe_nghiep' => 'Sinh viên ĐH Quốc Gia'],
            ['ho_ten' => 'Vũ Hải Long', 'nghe_nghiep' => 'Lập trình viên Frontend'],
            ['ho_ten' => 'Bùi Gia Huy', 'nghe_nghiep' => 'Sinh viên ĐH Lâm Nghiệp'],
            ['ho_ten' => 'Đỗ Xuân Trường', 'nghe_nghiep' => 'Sinh viên ĐH Xây Dựng'],
            ['ho_ten' => 'Phan Anh Tú', 'nghe_nghiep' => 'Kỹ sư cầu đường'],
            ['ho_ten' => 'Trịnh Quốc Bảo', 'nghe_nghiep' => 'Sinh viên ĐH Giao Thông Vận Tải'],
            ['ho_ten' => 'Nguyễn Khánh Nam', 'nghe_nghiep' => 'Sinh viên ĐH Công Nghiệp'],
            ['ho_ten' => 'Tạ Quang Thắng', 'nghe_nghiep' => 'Kế toán viên'],
            ['ho_ten' => 'Dương Hoàng Phong', 'nghe_nghiep' => 'Sinh viên ĐH Ngoại Thương'],
            ['ho_ten' => 'Lý Hữu Phước', 'nghe_nghiep' => 'Chuyên viên Marketing'],
            ['ho_ten' => 'Đặng Văn Lâm', 'nghe_nghiep' => 'Sinh viên ĐH Luật'],
        ];

        // Danh sách thông tin mẫu cho Nữ
        $nuList = [
            ['ho_ten' => 'Trần Thị Lan', 'nghe_nghiep' => 'Sinh viên ĐH Ngoại Thương'],
            ['ho_ten' => 'Phạm Minh Thư', 'nghe_nghiep' => 'Nhân viên kiểm toán'],
            ['ho_ten' => 'Vũ Thị Hương', 'nghe_nghiep' => 'Sinh viên ĐH Sư Phạm'],
            ['ho_ten' => 'Hoàng Thanh Trúc', 'nghe_nghiep' => 'Nhà thiết kế thời trang'],
            ['ho_ten' => 'Nguyễn Phương Vy', 'nghe_nghiep' => 'Sinh viên ĐH Y Dược'],
            ['ho_ten' => 'Lê Khánh Linh', 'nghe_nghiep' => 'Chuyên viên nhân sự'],
            ['ho_ten' => 'Bùi Quỳnh Anh', 'nghe_nghiep' => 'Sinh viên ĐH Lâm Nghiệp'],
            ['ho_ten' => 'Đỗ Mỹ Huyền', 'nghe_nghiep' => 'Sinh viên Học viện Ngân Hàng'],
            ['ho_ten' => 'Phan Thu Trang', 'nghe_nghiep' => 'Nhân viên Marketing'],
            ['ho_ten' => 'Đặng Thảo Vy', 'nghe_nghiep' => 'Sinh viên ĐH Kinh Tế'],
            ['ho_ten' => 'Lâm Bảo Châu', 'nghe_nghiep' => 'Sinh viên ĐH Quốc Gia'],
            ['ho_ten' => 'Trương Mai Chi', 'nghe_nghiep' => 'Giao dịch viên ngân hàng'],
            ['ho_ten' => 'Vương Cẩm Tú', 'nghe_nghiep' => 'Sinh viên ĐH Văn Hóa'],
            ['ho_ten' => 'Cao Thuỳ Dương', 'nghe_nghiep' => 'Biên dịch viên'],
            ['ho_ten' => 'Ngô Phương Thảo', 'nghe_nghiep' => 'Sinh viên Học viện Báo chí'],
        ];

        $cities = ['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ'];
        $religions = ['khong', 'phat_giao', 'thien_chua', 'tin_lanh', 'khac'];
        $regions = ['mien_bac', 'mien_trung', 'mien_nam'];

        $criteriaKeys = ['ban_be_den_choi', 'nuoi_thu_cung', 'hut_thuoc', 'do_sach_se', 'gio_giac'];

        $count = 0;

        // Tạo 15 User Nam
        for ($i = 0; $i < 15; $i++) {
            $namData = $namList[$i];
            $email = 'nguoitimtro_nam' . ($i + 1) . '@easym.vn';
            
            // Xây dựng Khảo sát lối sống ngẫu nhiên nhưng hợp lý
            $khaoSat = [];
            foreach ($criteriaKeys as $key) {
                if ($key === 'hut_thuoc') {
                    $khaoSat[$key] = rand(1, 3); // 1-3 (ít hoặc không hút)
                } elseif ($key === 'do_sach_se') {
                    $khaoSat[$key] = rand(3, 5); // Đa số thích sạch sẽ 3-5
                } else {
                    $khaoSat[$key] = rand(1, 5);
                }
            }

            $khaoSat['ton_giao'] = $religions[array_rand($religions)];
            $khaoSat['van_hoa'] = $regions[array_rand($regions)];
            
            // Lọc cứng ngẫu nhiên
            $khaoSat['ton_giao_loc_cung'] = (rand(1, 10) > 8); // 20% yêu cầu cùng tôn giáo
            $khaoSat['van_hoa_loc_cung'] = (rand(1, 10) > 7);  // 30% yêu cầu cùng vùng miền
            
            // Chọn ngẫu nhiên 0 đến 2 tiêu chí ưu tiên
            $uuTien = [];
            $allKeys = array_merge($criteriaKeys, ['ton_giao', 'van_hoa']);
            shuffle($allKeys);
            $numUuTien = rand(0, 2);
            for ($k = 0; $k < $numUuTien; $k++) {
                $uuTien[] = $allKeys[$k];
            }
            $khaoSat['uu_tien'] = $uuTien;

            NguoiDung::create([
                'ho_ten' => $namData['ho_ten'],
                'email' => $email,
                'mat_khau' => Hash::make('123456'), // Mật khẩu chung dễ test
                'so_dien_thoai' => '098' . rand(1000000, 9999999),
                'thanh_pho' => ($i < 10) ? 'Hà Nội' : $cities[array_rand($cities)], // Tập trung nhiều ở Hà Nội (DH Lâm Nghiệp)
                'vai_tro' => 'nguoi_tim_tro',
                'gioi_tinh' => 'nam',
                'nam_sinh' => rand(1996, 2007),
                'nghe_nghiep' => $namData['nghe_nghiep'],
                'anh_dai_dien' => $maleAvatars[$i % count($maleAvatars)],
                'da_xac_thuc_cccd' => (rand(1, 10) > 4), // 60% đã KYC
                'thong_tin_cccd' => [
                    'so_cccd' => '03709' . rand(1000000, 9999999),
                    'ngay_cap' => '2022-05-15',
                    'noi_cap' => 'Cục Cảnh sát QLHC về TTXH'
                ],
                'khao_sat_loi_song' => $khaoSat,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now(),
            ]);

            $count++;
        }

        // Tạo 15 User Nữ
        for ($i = 0; $i < 15; $i++) {
            $nuData = $nuList[$i];
            $email = 'nguoitimtro_nu' . ($i + 1) . '@easym.vn';
            
            // Xây dựng Khảo sát lối sống ngẫu nhiên nhưng hợp lý
            $khaoSat = [];
            foreach ($criteriaKeys as $key) {
                if ($key === 'hut_thuoc') {
                    $khaoSat[$key] = rand(1, 2); // Nữ hầu như không hút thuốc
                } elseif ($key === 'do_sach_se') {
                    $khaoSat[$key] = rand(4, 5); // Nữ sạch sẽ hơn
                } else {
                    $khaoSat[$key] = rand(1, 5);
                }
            }

            $khaoSat['ton_giao'] = $religions[array_rand($religions)];
            $khaoSat['van_hoa'] = $regions[array_rand($regions)];
            
            // Lọc cứng ngẫu nhiên
            $khaoSat['ton_giao_loc_cung'] = (rand(1, 10) > 8); // 20% yêu cầu cùng tôn giáo
            $khaoSat['van_hoa_loc_cung'] = (rand(1, 10) > 7);  // 30% yêu cầu cùng vùng miền
            
            // Chọn ngẫu nhiên 0 đến 2 tiêu chí ưu tiên
            $uuTien = [];
            $allKeys = array_merge($criteriaKeys, ['ton_giao', 'van_hoa']);
            shuffle($allKeys);
            $numUuTien = rand(0, 2);
            for ($k = 0; $k < $numUuTien; $k++) {
                $uuTien[] = $allKeys[$k];
            }
            $khaoSat['uu_tien'] = $uuTien;

            NguoiDung::create([
                'ho_ten' => $nuData['ho_ten'],
                'email' => $email,
                'mat_khau' => Hash::make('123456'), // Mật khẩu chung dễ test
                'so_dien_thoai' => '097' . rand(1000000, 9999999),
                'thanh_pho' => ($i < 10) ? 'Hà Nội' : $cities[array_rand($cities)], // Tập trung nhiều ở Hà Nội (DH Lâm Nghiệp)
                'vai_tro' => 'nguoi_tim_tro',
                'gioi_tinh' => 'nu',
                'nam_sinh' => rand(1996, 2007),
                'nghe_nghiep' => $nuData['nghe_nghiep'],
                'anh_dai_dien' => $femaleAvatars[$i % count($femaleAvatars)],
                'da_xac_thuc_cccd' => (rand(1, 10) > 4), // 60% đã KYC
                'thong_tin_cccd' => [
                    'so_cccd' => '03730' . rand(1000000, 9999999),
                    'ngay_cap' => '2023-08-20',
                    'noi_cap' => 'Cục Cảnh sát QLHC về TTXH'
                ],
                'khao_sat_loi_song' => $khaoSat,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now(),
            ]);

            $count++;
        }

        echo "🚀 Đã tạo thành công {$count} tài khoản người tìm trọ mới với đầy đủ thông tin cá nhân và khảo sát lối sống!\n";
    }
}
