<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class XuanMaiTroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa sạch dữ liệu cũ của các tài khoản test để tránh lỗi trùng lặp khi chạy lại seeder
        $testEmails = [
            'cuongchutro@easym.vn',
            'lanchutro@easym.vn',
            'sonchutro@easym.vn',
            'maichutro@easym.vn',
            'tuanchutro@easym.vn'
        ];
        DB::table('nguoi_dung')->whereIn('email', $testEmails)->delete();

        // 1. Tạo 5 chủ trọ mới tại Xuân Mai, Chương Mỹ
        $landlords = [
            [
                'ho_ten' => 'Nguyễn Văn Cường',
                'email' => 'cuongchutro@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0912444555',
                'thanh_pho' => 'Hà Nội',
                'vai_tro' => 'chu_tro',
                'da_xac_thuc_cccd' => true,
                'thong_tin_cccd' => json_encode([
                    'so_cccd' => '001090123456',
                    'ngay_cap' => '2021-10-12',
                    'noi_cap' => 'Cục Cảnh sát QLHC về TTXH'
                ]),
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Phạm Thị Lan',
                'email' => 'lanchutro@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0987111222',
                'thanh_pho' => 'Hà Nội',
                'vai_tro' => 'chu_tro',
                'da_xac_thuc_cccd' => true,
                'thong_tin_cccd' => json_encode([
                    'so_cccd' => '001091234567',
                    'ngay_cap' => '2022-03-15',
                    'noi_cap' => 'Cục Cảnh sát QLHC về TTXH'
                ]),
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Trần Thanh Sơn',
                'email' => 'sonchutro@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0965222333',
                'thanh_pho' => 'Hà Nội',
                'vai_tro' => 'chu_tro',
                'da_xac_thuc_cccd' => false,
                'thong_tin_cccd' => null,
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Bùi Thị Mai',
                'email' => 'maichutro@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0934666777',
                'thanh_pho' => 'Hà Nội',
                'vai_tro' => 'chu_tro',
                'da_xac_thuc_cccd' => true,
                'thong_tin_cccd' => json_encode([
                    'so_cccd' => '001092345678',
                    'ngay_cap' => '2022-09-20',
                    'noi_cap' => 'Cục Cảnh sát QLHC về TTXH'
                ]),
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'ho_ten' => 'Lê Minh Tuấn',
                'email' => 'tuanchutro@easym.vn',
                'mat_khau' => Hash::make('123456'),
                'so_dien_thoai' => '0904888999',
                'thanh_pho' => 'Hà Nội',
                'vai_tro' => 'chu_tro',
                'da_xac_thuc_cccd' => true,
                'thong_tin_cccd' => json_encode([
                    'so_cccd' => '001093456789',
                    'ngay_cap' => '2023-01-18',
                    'noi_cap' => 'Cục Cảnh sát QLHC về TTXH'
                ]),
                'khao_sat_loi_song' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('nguoi_dung')->insert($landlords);

        // Lấy ID của các chủ trọ vừa tạo để liên kết với phòng trọ
        $idCuong = DB::table('nguoi_dung')->where('email', 'cuongchutro@easym.vn')->value('id');
        $idLan = DB::table('nguoi_dung')->where('email', 'lanchutro@easym.vn')->value('id');
        $idSon = DB::table('nguoi_dung')->where('email', 'sonchutro@easym.vn')->value('id');
        $idMai = DB::table('nguoi_dung')->where('email', 'maichutro@easym.vn')->value('id');
        $idTuan = DB::table('nguoi_dung')->where('email', 'tuanchutro@easym.vn')->value('id');

        // Khởi tạo danh sách phòng trọ cho từng chủ trọ quanh Xuân Mai
        $rooms = [
            // Cường: Gần cổng trường ĐH Lâm Nghiệp
            [
                'id_chu_tro' => $idCuong,
                'tieu_de' => 'Phòng trọ khép kín full đồ ngay sau cổng trường Lâm Nghiệp',
                'mo_ta' => 'Phòng trọ diện tích 22m2, có điều hòa, nóng lạnh, giường tủ đầy đủ. Không chung chủ, giờ giấc tự do, có camera an ninh 24/7, chỗ để xe rộng rãi và miễn phí.',
                'gia_phong' => 1800000.00,
                'dien_tich' => 22.0,
                'dia_chi_chi_tiet' => 'Số 12, Ngõ 4 Đường Lâm Nghiệp, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600&q=80',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 2, // Đã xác thực
                'trang_thai_thue' => 1, // Còn trống
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => 'tu_do',
                    'do_sach_se' => 4,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '1',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.9412,
                'lng' => 105.5583,
            ],
            [
                'id_chu_tro' => $idCuong,
                'tieu_de' => 'Phòng trọ giá rẻ cho sinh viên gần ĐH Lâm Nghiệp',
                'mo_ta' => 'Phòng trọ sạch sẽ thoáng mát, vệ sinh khép kín, an ninh đảm bảo. Điện nước tính theo giá dân, chủ nhà thân thiện hỗ trợ nhiệt tình, gần trạm xe buýt.',
                'gia_phong' => 1200000.00,
                'dien_tich' => 18.0,
                'dia_chi_chi_tiet' => 'Số 32, Ngách 5 Ngõ 10 Đường Lâm Nghiệp, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 1, // Chờ duyệt
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => '23h',
                    'do_sach_se' => 3,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '0',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.9421,
                'lng' => 105.5591,
            ],
            [
                'id_chu_tro' => $idCuong,
                'tieu_de' => 'Chung cư mini mới xây gần cổng phụ trường Lâm Nghiệp',
                'mo_ta' => 'Nhà mới hoàn thiện, có thang máy tốc độ cao, khóa cửa vân tay vô cùng an toàn. Mỗi phòng có ban công thoáng rộng để phơi đồ, khu nấu ăn riêng biệt sạch sẽ.',
                'gia_phong' => 2500000.00,
                'dien_tich' => 28.0,
                'dia_chi_chi_tiet' => 'Số 8, Tổ dân phố Tân Xuân, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=600&q=80',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 2,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Nu',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => 'tu_do',
                    'do_sach_se' => 5,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '0',
                    'ban_be_den_choi' => '0'
                ]),
                'lat' => 20.9405,
                'lng' => 105.5568,
            ],

            // Lan: Khu vực Ngã tư Xuân Mai
            [
                'id_chu_tro' => $idLan,
                'tieu_de' => 'Căn hộ mini 1 phòng ngủ full nội thất gần ngã tư Xuân Mai',
                'mo_ta' => 'Phòng đầy đủ tiện nghi cao cấp: điều hòa nhiệt độ, bình nóng lạnh, tủ lạnh, máy giặt dùng chung tầng 1, giường đệm lò xo êm ái. Phù hợp cho hộ gia đình trẻ hoặc người đi làm văn phòng.',
                'gia_phong' => 3200000.00,
                'dien_tich' => 32.0,
                'dia_chi_chi_tiet' => 'Số 102, Quốc lộ 6, thị trấn Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=600&q=80',
                    'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 2,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => 'tu_do',
                    'do_sach_se' => 4,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '1',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.8988,
                'lng' => 105.5975,
            ],
            [
                'id_chu_tro' => $idLan,
                'tieu_de' => 'Phòng trọ khép kín giá rẻ ngay ngã tư Xuân Mai',
                'mo_ta' => 'Phòng trọ khép kín diện tích 20m2, có gác xép lửng tối ưu không gian sinh hoạt, cực kỳ sạch sẽ, thoáng mát. Vị trí đắc địa ngay trung tâm, dễ dàng di chuyển, gần chợ lớn Xuân Mai.',
                'gia_phong' => 1500000.00,
                'dien_tich' => 20.0,
                'dia_chi_chi_tiet' => 'Ngõ 2, đường Hồ Chí Minh, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 1,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => '23h',
                    'do_sach_se' => 3,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '0',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.8975,
                'lng' => 105.5982,
            ],
            [
                'id_chu_tro' => $idLan,
                'tieu_de' => 'Nhà trọ sinh viên độc lập lối đi riêng gần chợ Xuân Mai',
                'mo_ta' => 'Phòng rộng rãi, vệ sinh khép kín sạch sẽ, giờ giấc tự quản không chung chủ. Có chỗ để xe an toàn dưới tầng 1 có hệ thống camera giám sát liên tục.',
                'gia_phong' => 1300000.00,
                'dien_tich' => 19.0,
                'dia_chi_chi_tiet' => 'Khu Xuân Hà, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 2,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Nam',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => 'tu_do',
                    'do_sach_se' => 4,
                    'hut_thuoc' => '1',
                    'nuoi_thu_cung' => '0',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.8995,
                'lng' => 105.5998,
            ],

            // Sơn: Gần Cao đẳng Cảnh sát Nhân dân I
            [
                'id_chu_tro' => $idSon,
                'tieu_de' => 'Phòng trọ khép kín gần trường Cao đẳng Cảnh sát Nhân dân I',
                'mo_ta' => 'Phòng trọ rộng rãi, an ninh cực tốt nằm trong khu dân cư yên tĩnh, dân trí cao, gần trường công an. Có trang bị bình nóng lạnh, giường tủ gỗ cơ bản.',
                'gia_phong' => 1600000.00,
                'dien_tich' => 21.0,
                'dia_chi_chi_tiet' => 'Khu Xuân Hà (gần trường Cảnh Sát), Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=600&q=80'
                ]),
                'anh_phap_ly' => null,
                'muc_do_xac_thuc' => 1,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => '22h',
                    'do_sach_se' => 4,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '0',
                    'ban_be_den_choi' => '0'
                ]),
                'lat' => 20.9022,
                'lng' => 105.5945,
            ],
            [
                'id_chu_tro' => $idSon,
                'tieu_de' => 'Chung cư mini cao cấp có ban công, gần đường Hồ Chí Minh',
                'mo_ta' => 'Phòng khép kín đầy đủ thiết bị điều hòa, nóng lạnh, rèm cửa, giường đệm cao cấp mới tinh. Khu nhà có thang máy di chuyển nhanh, chỗ gửi xe rộng rãi free hoàn toàn.',
                'gia_phong' => 2800000.00,
                'dien_tich' => 30.0,
                'dia_chi_chi_tiet' => 'Số 15, Đường Hồ Chí Minh, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80',
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&q=80'
                ]),
                'anh_phap_ly' => null,
                'muc_do_xac_thuc' => 1,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Nu',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => 'tu_do',
                    'do_sach_se' => 5,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '0',
                    'ban_be_den_choi' => '0'
                ]),
                'lat' => 20.9011,
                'lng' => 105.5958,
            ],

            // Mai: Gần ĐH Sư phạm Thể dục Thể thao Hà Nội
            [
                'id_chu_tro' => $idMai,
                'tieu_de' => 'Phòng trọ rộng rãi gần trường ĐH Sư phạm TDTT Hà Nội',
                'mo_ta' => 'Phòng trọ khép kín, có điều hòa và bình nóng lạnh mới lắp đạt chuẩn. Gần trường TDTT rất tiện lợi cho các bạn sinh viên học tập, rèn luyện thể chất, đi lại an toàn.',
                'gia_phong' => 1700000.00,
                'dien_tich' => 23.0,
                'dia_chi_chi_tiet' => 'Tổ 5 (gần trường TDTT), Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 2,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => 'tu_do',
                    'do_sach_se' => 4,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '1',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.9125,
                'lng' => 105.5752,
            ],
            [
                'id_chu_tro' => $idMai,
                'tieu_de' => 'Nhà nguyên căn chia phòng gần ĐH Sư phạm TDTT',
                'mo_ta' => 'Nhà mới sửa đẹp đẽ, phòng rộng rãi thoáng mát có cửa sổ lớn đón gió tự nhiên. Giờ giấc tự do không chung chủ, thuận tiện tụ họp bạn bè học nhóm.',
                'gia_phong' => 2000000.00,
                'dien_tich' => 25.0,
                'dia_chi_chi_tiet' => 'Ngõ 8, Tổ 5, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=600&q=80',
                    'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 2,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => 'tu_do',
                    'do_sach_se' => 3,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '1',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.9138,
                'lng' => 105.5768,
            ],

            // Tuấn: Dọc trục Quốc lộ 6 & Cao đẳng Nông nghiệp Bắc Bộ
            [
                'id_chu_tro' => $idTuan,
                'tieu_de' => 'Căn hộ dịch vụ cao cấp full đồ ngay mặt đường QL6 Xuân Mai',
                'mo_ta' => 'Phòng thiết kế sang trọng phong cách căn hộ studio hiện đại, trang bị không gian bếp riêng, điều hòa inverter tiết kiệm điện, máy giặt riêng biệt, smart TV kết nối wifi tốc độ cao. Ban công hướng đường phố cực thoáng mát.',
                'gia_phong' => 3500000.00,
                'dien_tich' => 35.0,
                'dia_chi_chi_tiet' => 'Số 250, Quốc lộ 6, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=600&q=80',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&q=80',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 2,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => 'tu_do',
                    'do_sach_se' => 5,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '0',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.9080,
                'lng' => 105.5825,
            ],
            [
                'id_chu_tro' => $idTuan,
                'tieu_de' => 'Phòng trọ khép kín giá bình dân gần trường Cao đẳng Nông nghiệp',
                'mo_ta' => 'Phòng trọ khép kín có toilet trong phòng vệ sinh sạch sẽ, có sân phơi đồ chung rộng thoáng nhiều nắng gió. Gần các trục đường lớn vô cùng thuận lợi đón xe bus số 72 hoặc 88 đi lại.',
                'gia_phong' => 1300000.00,
                'dien_tich' => 20.0,
                'dia_chi_chi_tiet' => 'Khu Bùi Xá, Xuân Mai, Chương Mỹ, Hà Nội',
                'anh_phong' => json_encode([
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=600&q=80'
                ]),
                'anh_phap_ly' => json_encode([
                    'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=600&q=80'
                ]),
                'muc_do_xac_thuc' => 2,
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => 'Tat ca',
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => '23h',
                    'do_sach_se' => 4,
                    'hut_thuoc' => '0',
                    'nuoi_thu_cung' => '0',
                    'ban_be_den_choi' => '1'
                ]),
                'lat' => 20.9065,
                'lng' => 105.5840,
            ],
        ];

        // 2. Tạo thêm 80 phòng trọ ngẫu nhiên quanh Xuân Mai
        $landlordIds = [$idCuong, $idLan, $idSon, $idMai, $idTuan];
        $roomTemplates = [
            [
                'tieu_de' => 'Phòng trọ khép kín khang trang gần ĐH Lâm Nghiệp',
                'mo_ta' => 'Phòng sạch sẽ, thoáng mát, vệ sinh khép kín. Đầy đủ nóng lạnh, giường tủ, quạt trần. Chỗ để xe rộng rãi có camera an ninh.',
                'dia_chi' => 'Ngõ %d Đường Lâm Nghiệp, Xuân Mai, Chương Mỹ, Hà Nội',
                'min_gia' => 1200000,
                'max_gia' => 1800000,
                'min_dien_tich' => 18,
                'max_dien_tich' => 24,
            ],
            [
                'tieu_de' => 'Chung cư mini full đồ cao cấp ngã tư Xuân Mai',
                'mo_ta' => 'Phòng mới kính coong, thiết kế hiện đại kiểu studio. Có điều hòa, nóng lạnh, tủ lạnh, bếp nấu riêng biệt. Giờ giấc tự do khóa vân tay.',
                'dia_chi' => 'Số %d Quốc lộ 6, Xuân Mai, Chương Mỹ, Hà Nội',
                'min_gia' => 2200000,
                'max_gia' => 3200000,
                'min_dien_tich' => 25,
                'max_dien_tich' => 35,
            ],
            [
                'tieu_de' => 'Phòng trọ giá rẻ cho sinh viên gần ĐH Sư Phạm TDTT',
                'mo_ta' => 'Phòng trọ an ninh tốt, gần mặt đường lớn tiện đi lại và bắt xe bus. Điện nước giá dân tự chia vô cùng tiết kiệm. Thích hợp cho nhóm bạn ở ghép.',
                'dia_chi' => 'Tổ %d (gần trường TDTT), Xuân Mai, Chương Mỹ, Hà Nội',
                'min_gia' => 1000000,
                'max_gia' => 1500000,
                'min_dien_tich' => 15,
                'max_dien_tich' => 20,
            ],
            [
                'tieu_de' => 'Phòng trọ khép kín yên tĩnh gần Cao đẳng Cảnh sát',
                'mo_ta' => 'Khu trọ an ninh tuyệt đối, cổng khóa vân tay bảo mật cao. Phòng rộng rãi thoáng có gác xép để đồ. Không chung chủ, có sân phơi chung rộng.',
                'dia_chi' => 'Ngõ %d khu Xuân Hà, Xuân Mai, Chương Mỹ, Hà Nội',
                'min_gia' => 1400000,
                'max_gia' => 2000000,
                'min_dien_tich' => 20,
                'max_dien_tich' => 26,
            ],
            [
                'tieu_de' => 'Căn hộ dịch vụ tiện nghi dọc trục đường Hồ Chí Minh',
                'mo_ta' => 'Đầy đủ trang thiết bị nội thất: Tủ lạnh, điều hòa, máy giặt riêng, giường nệm cao cấp. Chỗ đỗ xe rộng rãi, bảo vệ 24/7.',
                'dia_chi' => 'Số %d đường Hồ Chí Minh, Xuân Mai, Chương Mỹ, Hà Nội',
                'min_gia' => 2500000,
                'max_gia' => 3500000,
                'min_dien_tich' => 28,
                'max_dien_tich' => 38,
            ]
        ];

        $imagesPool = [
            'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=600&q=80',
            'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=600&q=80',
            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=500&q=80',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=600&q=80',
            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&q=80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=600&q=80',
            'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=600&q=80',
            'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=600&q=80',
            'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=600&q=80',
            'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?w=600&q=80'
        ];

        $legalPool = [
            'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&q=80',
            'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=600&q=80'
        ];

        $centers = [
            ['lat' => 20.9412, 'lng' => 105.5583], // ĐH Lâm Nghiệp
            ['lat' => 20.8988, 'lng' => 105.5975], // Ngã tư Xuân Mai
            ['lat' => 20.9125, 'lng' => 105.5752], // ĐH Sư phạm TDTT
            ['lat' => 20.9022, 'lng' => 105.5945], // CĐ Cảnh sát ND
        ];

        $genders = ['Tat ca', 'Nam', 'Nu'];

        for ($k = 1; $k <= 80; $k++) {
            $tpl = $roomTemplates[array_rand($roomTemplates)];
            $center = $centers[array_rand($centers)];
            
            $lat = $center['lat'] + (rand(-80, 80) / 10000);
            $lng = $center['lng'] + (rand(-80, 80) / 10000);
            
            $gia = rand($tpl['min_gia'] / 100000, $tpl['max_gia'] / 100000) * 100000;
            $dienTich = rand($tpl['min_dien_tich'], $tpl['max_dien_tich']);
            
            $shuffledImages = $imagesPool;
            shuffle($shuffledImages);
            $roomImages = array_slice($shuffledImages, 0, rand(1, 3));

            $rooms[] = [
                'id_chu_tro' => $landlordIds[array_rand($landlordIds)],
                'tieu_de' => $tpl['tieu_de'] . ' - Căn ' . $k,
                'mo_ta' => $tpl['mo_ta'] . ' Phòng đẹp giá tốt phù hợp lâu dài.',
                'gia_phong' => (float)$gia,
                'dien_tich' => (float)$dienTich,
                'dia_chi_chi_tiet' => sprintf($tpl['dia_chi'], rand(1, 150)),
                'anh_phong' => json_encode($roomImages),
                'anh_phap_ly' => json_encode([$legalPool[array_rand($legalPool)]]),
                'muc_do_xac_thuc' => rand(1, 2),
                'trang_thai_thue' => 1,
                'gioi_tinh_cho_thue' => $genders[array_rand($genders)],
                'khao_sat_loi_song_chu_tro' => json_encode([
                    'gio_giac' => (rand(0, 1) ? 'tu_do' : '23h'),
                    'do_sach_se' => rand(3, 5),
                    'hut_thuoc' => (string)rand(0, 1),
                    'nuoi_thu_cung' => (string)rand(0, 1),
                    'ban_be_den_choi' => (string)rand(0, 1)
                ]),
                'lat' => $lat,
                'lng' => $lng,
            ];
        }

        // Insert từng phòng trọ với vị trí hình học POINT
        foreach ($rooms as $room) {
            DB::table('phong_tro')->insert([
                'id_chu_tro' => $room['id_chu_tro'],
                'tieu_de' => $room['tieu_de'],
                'mo_ta' => $room['mo_ta'],
                'gia_phong' => $room['gia_phong'],
                'dien_tich' => $room['dien_tich'],
                'dia_chi_chi_tiet' => $room['dia_chi_chi_tiet'],
                'anh_phong' => $room['anh_phong'],
                'anh_phap_ly' => $room['anh_phap_ly'],
                'muc_do_xac_thuc' => $room['muc_do_xac_thuc'],
                'trang_thai_thue' => $room['trang_thai_thue'],
                'gioi_tinh_cho_thue' => $room['gioi_tinh_cho_thue'],
                'khao_sat_loi_song_chu_tro' => $room['khao_sat_loi_song_chu_tro'],
                'vi_tri' => DB::raw("ST_GeomFromText('POINT({$room['lng']} {$room['lat']})', 4326)"),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        $countRooms = count($rooms);
        echo "🚀 Da tao thanh cong 5 tai khoan chu tro moi va {$countRooms} phong tro tai Xuan Mai, Chuong My, Ha Noi!\n";
    }
}
