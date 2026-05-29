<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DongBoNhaSieuTotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Đọc file view.json bạn vừa lưu trong storage/app/
        $filePath = 'view.json';

        if (!Storage::exists($filePath)) {
            $this->command->error("❌ Khong tim thay file view.json trong storage/app/");
            return;
        }

        $jsonContent = Storage::get($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("❌ File JSON bi loi cu phap, hay kiem tra lai!");
            return;
        }

        // Kênh xử lý: Nếu bạn bọc nhiều phòng trong mảng [] thì duyệt mảng, 
        // nếu chỉ có 1 phòng duy nhất (dạng đối tượng {}) thì tự động bọc lại thành mảng để xử lý.
        $rooms = isset($data['ad_id']) ? [$data] : $data;

        $countSuccess = 0;
        $this->command->info("🚀 Bat dau dong bo du lieu phong tro thuc te vao PostGIS...");

        // Lấy ID của một tài khoản chủ trọ mẫu để gán quyền sở hữu phòng trọ này
        $idChuTroMau = DB::table('nguoi_dung')->where('vai_tro', 'chu_tro')->value('id') ?? 1;

        foreach ($rooms as $room) {
            // Khớp chuẩn các key tiếng Anh từ file view.json bạn vừa gửi
            $tieuDe = $room['subject'] ?? 'Phong tro EasyM';
            $giaPhong = intval($room['price'] ?? 0);
            $dienTich = floatval($room['size'] ?? 20);
            $diaChi = $room['address_full'] ?? 'Chua cap nhat';
            $moTa = $room['body'] ?? 'Chua co mo ta chi tiet.';

            // Lấy ảnh đầu tiên trong mảng media_urls làm ảnh đại diện chính cho phòng trọ
            $anhPhong = null;
            if (!empty($room['media_urls']) && is_array($room['media_urls'])) {
                $anhPhong = $room['media_urls'][0];
            }

            // Trích xuất chính xác cặp tọa độ địa lý hình học
            $lat = floatval($room['latitude'] ?? null);
            $lng = floatval($room['longitude'] ?? null);

            if (!$lat || !$lng) {
                $this->command->error(" ⚠️ Bo qua phong do khong tim thay toa do lat/lng.");
                continue;
            }

            try {
                // Thay đổi tên bảng và tên cột cho khớp với cấu trúc Database thực tế của bạn
                DB::table('phong_tro')->insert([
                    'id_chu_tro' => $idChuTroMau,
                    'tieu_de' => $tieuDe,
                    'gia_phong' => $giaPhong,
                    'dien_tich' => $dienTich,
                    'dia_chi_chi_tiet' => $diaChi,
                    'mo_ta' => $moTa,
                    'anh_phong' => $anhPhong,
                    'muc_do_xac_thuc' => 2, // Đánh nhãn 2 để hiển thị badge xanh uy tín "Đã xác thực thực địa"
                    'trang_thai_thue' => 'con_trong',
                    // Găm tọa độ địa lý PostGIS: Hệ tọa độ WGS 84 (SRID 4326) theo thứ tự POINT(lng lat)
                    'vi_tri' => DB::raw("ST_GeomFromText('POINT($lng $lat)', 4326)"),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $countSuccess++;
                $this->command->line(" ✅ Dong bo thanh cong: " . substr($tieuDe, 0, 45) . "...");
            } catch (\Exception $e) {
                $this->command->error(" ❌ Loi khi insert vao Postgres: " . $e->getMessage());
            }
        }

        $this->command->info("🎉 Hoan thanh! Da nap thanh cong $countSuccess phong tro thuc te vao database.");
    }
}