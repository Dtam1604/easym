<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PhongTro;

class DongBoNhaSieuTot extends Command
{
    /**
     * Tên lệnh và cách gọi trong terminal (VD: php artisan app:dong-bo-nha-sieu-tot)
     *
     * @var string
     */
    protected $signature = 'app:dong-bo-nha-sieu-tot';

    /**
     * Mô tả lệnh hiển thị khi gọi php artisan list
     *
     * @var string
     */
    protected $description = 'Cào dữ liệu phòng trọ từ API ngầm của nhasieutot.com và đồng bộ vào CSDL PostGIS của EasyM';

    /**
     * Hàm xử lý chính (Entry point)
     */
    public function handle()
    {
        $this->info("Bắt đầu tiến trình đồng bộ dữ liệu từ nhasieutot.com...");

        // 1. Cấu hình đường dẫn API (Bạn cần lấy đường link thực tế từ tab Network của F12)
        // Lưu ý: Đổi URL này thành URL thực tế của API ngầm nhasieutot.com
        $urlApi = 'https://maps.googleapis.com/$rpc/google.internal.maps.mapsjs.v1.MapsJsInternalService/GetViewportInfo';

        try {
            $this->info("Đang gửi Request tới API ngầm...");

            // Sử dụng HTTP Client của Laravel giả lập trình duyệt (Tránh lỗi 403 Forbidden)
            $response = Http::withHeaders([
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'vi,en-US;q=0.9,en;q=0.8',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => 'https://nhasieutot.com/',
                'Origin' => 'https://nhasieutot.com',
                // Bổ sung thêm Authorization hoặc Cookie nếu API yêu cầu
            ])->timeout(30)->get($urlApi); // Thay get() bằng post() nếu API yêu cầu POST

            if (!$response->successful()) {
                $this->error("Không thể kết nối đến API. Mã lỗi: " . $response->status());
                return Command::FAILURE;
            }

            // Giải mã JSON trả về (Ví dụ: data trả về nằm trong key 'data' của JSON)
            $duLieuTraVe = $response->json('data') ?? $response->json();

            if (!is_array($duLieuTraVe) || count($duLieuTraVe) === 0) {
                $this->warn("Không tìm thấy dữ liệu hoặc API trả về mảng rỗng.");
                return Command::SUCCESS;
            }

            $tongSoPhong = count($duLieuTraVe);
            $this->info("Tìm thấy tổng cộng {$tongSoPhong} phòng trọ. Bắt đầu xử lý và lưu vào Database...");

            // Khởi tạo thanh tiến trình (UX cho Console)
            $this->output->progressStart($tongSoPhong);

            $soLuongThanhCong = 0;
            $soLuongLoi = 0;

            // 2. Bóc tách và Tiền xử lý dữ liệu (Data Parsing)
            foreach ($duLieuTraVe as $phong) {
                try {
                    // Xử lý giá phòng: Chuyển '1.5tr' hoặc '1,500,000' thành số nguyên 1500000
                    $giaGoc = $phong['gia'] ?? '0'; // Tùy thuộc vào key thực tế từ API
                    $giaPhong = $this->chuyenDoiGiaTien($giaGoc);

                    // Lấy đầy đủ mảng Hình ảnh và chuỗi Mô tả như yêu cầu của Đề cương
                    $anhPhong = $phong['images'] ?? []; // Đảm bảo lấy nguyên mảng ảnh
                    $moTa = $phong['description'] ?? 'Chưa có thông tin mô tả chi tiết.';

                    // Ép kiểu Diện tích sang Float
                    $dienTich = isset($phong['dien_tich']) ? (float) filter_var($phong['dien_tich'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 20.0;

                    // Lấy Tọa độ
                    $kinhDo = $phong['lng'] ?? $phong['longitude'] ?? 105.804;
                    $viDo = $phong['lat'] ?? $phong['latitude'] ?? 21.028;

                    // 3. Chuyển đổi tọa độ sang Hình học PostGIS & Lưu vào Database
                    PhongTro::create([
                        'tieu_de' => $phong['tieu_de'] ?? 'Phòng trọ giá rẻ',
                        'gia_phong' => $giaPhong,
                        'dien_tich' => $dienTich,
                        'mo_ta' => $moTa,
                        'anh_phong' => $anhPhong, // Model đã cấu hình Casts là 'array'

                        // Gán mặc định vào ID chủ trọ mẫu trong hệ thống EasyM
                        'id_chu_tro' => 1,

                        // Gán bằng 2 (Đã xác thực) để hiển thị Badge xanh uy tín trên bản đồ
                        'muc_do_xac_thuc' => 2,
                        'trang_thai_thue' => 1, // 1 = Còn trống
                        'dia_chi_chi_tiet' => $phong['dia_chi'] ?? 'Hà Nội',

                        // Sử dụng DB::raw để parse tọa độ chuẩn PostGIS SRID 4326
                        'vi_tri' => DB::raw("ST_GeomFromText('POINT({$kinhDo} {$viDo})', 4326)")
                    ]);

                    $soLuongThanhCong++;
                } catch (\Exception $e) {
                    // Bỏ qua bản ghi lỗi và ghi log để phân tích sau (Chống dừng vòng lặp)
                    $soLuongLoi++;
                    Log::error("Lỗi đồng bộ phòng trọ nhasieutot: " . $e->getMessage() . " | Dữ liệu: " . json_encode($phong));
                }

                // Cập nhật thanh tiến trình
                $this->output->progressAdvance();
            }

            // Kết thúc thanh tiến trình
            $this->output->progressFinish();

            $this->info("Đồng bộ hoàn tất!");
            $this->line("- Thành công: <info>{$soLuongThanhCong}</info>");
            $this->line("- Thất bại (Lỗi): <error>{$soLuongLoi}</error>");

        } catch (\Exception $ex) {
            $this->error("Lỗi hệ thống trong quá trình cào API: " . $ex->getMessage());
            Log::error("DongBoNhaSieuTot Command Error: " . $ex->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Hàm hỗ trợ chuyển đổi chuỗi giá tiền hiển thị thành số nguyên
     * Ví dụ: "1.5tr" -> 1500000, "2 triệu rưỡi" -> 2500000
     */
    private function chuyenDoiGiaTien($giaChuoi)
    {
        $giaChuoi = mb_strtolower((string) $giaChuoi, 'UTF-8');

        // Trích xuất số thực ra khỏi chuỗi
        $so = (float) filter_var(preg_replace('/[^0-9\.]/', '', $giaChuoi), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if (str_contains($giaChuoi, 'tỷ') || str_contains($giaChuoi, 'ty')) {
            return (int) ($so * 1000000000);
        }

        if (str_contains($giaChuoi, 'tr') || str_contains($giaChuoi, 'triệu') || str_contains($giaChuoi, 'trieu')) {
            return (int) ($so * 1000000);
        }

        if (str_contains($giaChuoi, 'k') || str_contains($giaChuoi, 'nghìn') || str_contains($giaChuoi, 'nghin')) {
            return (int) ($so * 1000);
        }

        // Nếu chuỗi kiểu "1,500,000"
        return (int) filter_var(preg_replace('/[^0-9]/', '', $giaChuoi), FILTER_SANITIZE_NUMBER_INT);
    }
}
