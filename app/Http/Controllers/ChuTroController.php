<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhongTro;
use Illuminate\Support\Facades\Auth;

class ChuTroController extends Controller
{
    /**
     * Hiển thị danh sách phòng đã đăng của Chủ trọ
     */
    public function danhSachPhong()
    {
        // Phân trang 10 phòng mỗi trang
        $danhSachPhong = PhongTro::where('id_chu_tro', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('chu_tro.danh_sach_phong', compact('danhSachPhong'));
    }

    /**
     * Hiển thị form đăng phòng mới
     */
    public function create()
    {
        return view('chu_tro.form_phong');
    }

    /**
     * Xử lý lưu phòng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'required|string',
            'gia_phong' => 'required|numeric|min:0',
            'dien_tich' => 'nullable|numeric|min:0',
            'dia_chi_chi_tiet' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'anh_phong.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'anh_phap_ly.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $anhPhongPaths = [];
        if ($request->hasFile('anh_phong')) {
            foreach ($request->file('anh_phong') as $file) {
                $path = $file->store('phong_tro', 'public');
                $anhPhongPaths[] = asset('storage/' . $path);
            }
        }

        $anhPhapLyPaths = [];
        if ($request->hasFile('anh_phap_ly')) {
            foreach ($request->file('anh_phap_ly') as $file) {
                $path = $file->store('phap_ly', 'public');
                $this->applyWatermark($path); // Thêm Watermark
                $anhPhapLyPaths[] = asset('storage/' . $path);
            }
        }

        $phong = new PhongTro();
        $phong->id_chu_tro = Auth::id();
        $phong->tieu_de = $request->tieu_de;
        $phong->mo_ta = $request->mo_ta;
        $phong->gia_phong = $request->gia_phong;
        $phong->dien_tich = $request->dien_tich;
        $phong->dia_chi_chi_tiet = $request->dia_chi_chi_tiet;
        $phong->anh_phong = $anhPhongPaths;
        $phong->anh_phap_ly = $anhPhapLyPaths;
        $phong->muc_do_xac_thuc = count($anhPhapLyPaths) > 0 ? 1 : 0; // Nếu có up sổ đỏ thì nâng mức xác thực lên 1 (Chờ duyệt)
        $phong->trang_thai_thue = 1; // 1 = Còn trống, 2 = Đã cho thuê
        
        $lat = floatval($request->lat);
        $lng = floatval($request->lng);
        $phong->vi_tri = \Illuminate\Support\Facades\DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)");
        
        $phong->save();

        return redirect()->route('chutro.phong')->with('success', 'Đã đăng phòng mới thành công!');
    }

    /**
     * Hiển thị form sửa phòng
     */
    public function edit($id)
    {
        $phong = PhongTro::where('id', $id)->where('id_chu_tro', Auth::id())->firstOrFail();
        
        // Lấy tọa độ để fill vào form
        $coords = \Illuminate\Support\Facades\DB::selectOne("SELECT ST_Y(vi_tri::geometry) as lat, ST_X(vi_tri::geometry) as lng FROM phong_tro WHERE id = ?", [$phong->id]);
        $phong->lat = $coords ? $coords->lat : 21.028511;
        $phong->lng = $coords ? $coords->lng : 105.804817;

        return view('chu_tro.form_phong', compact('phong'));
    }

    /**
     * Xử lý cập nhật phòng
     */
    public function update(Request $request, $id)
    {
        $phong = PhongTro::where('id', $id)->where('id_chu_tro', Auth::id())->firstOrFail();

        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'required|string',
            'gia_phong' => 'required|numeric|min:0',
            'dien_tich' => 'nullable|numeric|min:0',
            'dia_chi_chi_tiet' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'anh_phong.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'anh_phap_ly.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $anhPhongPaths = is_array($phong->anh_phong) ? $phong->anh_phong : [];
        if ($request->hasFile('anh_phong')) {
            $anhPhongPaths = []; // Ghi đè toàn bộ ảnh cũ nếu có up ảnh mới
            foreach ($request->file('anh_phong') as $file) {
                $path = $file->store('phong_tro', 'public');
                $anhPhongPaths[] = asset('storage/' . $path);
            }
        }

        $anhPhapLyPaths = is_array($phong->anh_phap_ly) ? $phong->anh_phap_ly : [];
        if ($request->hasFile('anh_phap_ly')) {
            $anhPhapLyPaths = []; // Ghi đè toàn bộ giấy tờ cũ nếu có up giấy tờ mới
            foreach ($request->file('anh_phap_ly') as $file) {
                $path = $file->store('phap_ly', 'public');
                $this->applyWatermark($path); // Thêm Watermark
                $anhPhapLyPaths[] = asset('storage/' . $path);
            }
        }

        $phong->tieu_de = $request->tieu_de;
        $phong->mo_ta = $request->mo_ta;
        $phong->gia_phong = $request->gia_phong;
        $phong->dien_tich = $request->dien_tich;
        $phong->dia_chi_chi_tiet = $request->dia_chi_chi_tiet;
        $phong->anh_phong = $anhPhongPaths;
        $phong->anh_phap_ly = $anhPhapLyPaths;
        
        $lat = floatval($request->lat);
        $lng = floatval($request->lng);
        $phong->vi_tri = \Illuminate\Support\Facades\DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)");
        
        $phong->save();

        return redirect()->route('chutro.phong')->with('success', 'Đã cập nhật thông tin phòng thành công!');
    }

    /**
     * AJAX: Thay đổi trạng thái thuê của phòng (Còn trống <-> Đã cho thuê)
     */
    public function toggleTrangThaiThue(Request $request, $id)
    {
        try {
            $phong = PhongTro::where('id', $id)
                ->where('id_chu_tro', Auth::id())
                ->firstOrFail();

            // Đảo ngược trạng thái (1 = Còn trống, 2 = Đã cho thuê)
            $phong->trang_thai_thue = $phong->trang_thai_thue == 1 ? 2 : 1;
            $phong->save();

            return response()->json([
                'success' => true,
                'trang_thai_moi' => $phong->trang_thai_thue,
                'message' => 'Đã cập nhật trạng thái phòng thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra hoặc bạn không có quyền thao tác phòng này.'
            ], 403);
        }
    }

    /**
     * AJAX: Gửi yêu cầu xác thực thực địa cho phòng
     */
    public function yeuCauXacThuc(Request $request, $id)
    {
        try {
            $phong = PhongTro::where('id', $id)
                ->where('id_chu_tro', Auth::id())
                ->firstOrFail();

            if ($phong->muc_do_xac_thuc > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phòng này đã được gửi yêu cầu xác thực trước đó.'
                ]);
            }

            // Nâng mức độ xác thực lên 1 (Đang chờ CTV)
            $phong->muc_do_xac_thuc = 1;
            $phong->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi yêu cầu xác thực. Hệ thống sẽ điều phối CTV sớm nhất.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra hoặc bạn không có quyền thao tác phòng này.'
            ], 403);
        }
    }

    /**
     * Thuật toán Đóng dấu bản quyền (Watermark) vào ảnh pháp lý
     * Ngăn chặn việc tải ảnh về và sử dụng cho mục đích xấu.
     */
    private function applyWatermark($imagePath)
    {
        $fullPath = storage_path('app/public/' . $imagePath);
        if (!file_exists($fullPath)) return;

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        
        // Tạo Resource ảnh từ file
        if (in_array($extension, ['jpg', 'jpeg'])) {
            if (!function_exists('imagecreatefromjpeg')) return;
            $image = @\imagecreatefromjpeg($fullPath);
        } elseif ($extension == 'png') {
            if (!function_exists('imagecreatefrompng')) return;
            $image = @\imagecreatefrompng($fullPath);
        } else {
            return;
        }

        if (!$image) return;

        // Cài đặt Watermark Text
        $watermarkText = 'EASYM VERIFICATION ONLY ' . date('Y-m-d');
        // Màu đỏ trong suốt (Alpha = 70)
        $color = \imagecolorallocatealpha($image, 255, 0, 0, 70); 
        $font = 5; // Font GD mặc định lớn nhất
        
        $width = \imagesx($image);
        $height = \imagesy($image);
        
        // Vẽ Text lặp lại khắp ảnh
        for ($y = 0; $y < $height; $y += 100) {
            for ($x = 0; $x < $width; $x += 200) {
                \imagestring($image, $font, $x, $y, $watermarkText, $color);
            }
        }

        // Lưu đè lại ảnh gốc
        if (in_array($extension, ['jpg', 'jpeg'])) {
            \imagejpeg($image, $fullPath, 90);
        } elseif ($extension == 'png') {
            \imagepng($image, $fullPath);
        }
        
        \imagedestroy($image);
    }
}
