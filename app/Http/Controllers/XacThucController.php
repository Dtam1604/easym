<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PhongTro;

class XacThucController extends Controller
{
    /**
     * ==========================================
     * LUỒNG 1: USER KYC (XÁC THỰC NGƯỜI DÙNG)
     * ==========================================
     */

    /**
     * Hiển thị giao diện upload CCCD cho Người dùng
     */
    public function kycForm()
    {
        $user = Auth::user();
        return view('auth.xac_thuc_tk', compact('user'));
    }

    /**
     * Xử lý lưu ảnh CCCD và cập nhật trạng thái
     */
    public function guiYeuCauKYC(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'mat_truoc_cccd' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'mat_sau_cccd' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ];
        $messages = [
            'mat_truoc_cccd.required' => 'Vui lòng tải lên ảnh mặt trước.',
            'mat_sau_cccd.required' => 'Vui lòng tải lên ảnh mặt sau.',
            'image' => 'File phải là định dạng ảnh.',
            'mimes' => 'Hỗ trợ định dạng jpeg, png, jpg.',
            'max' => 'Kích thước ảnh tối đa là 5MB.',
            'so_do.required' => 'Vui lòng tải lên ảnh chụp Sổ đỏ/Giấy tờ nhà đất.',
        ];

        // Nếu là chủ trọ, bắt buộc phải tải lên Sổ đỏ
        if ($user->vai_tro === 'chu_tro') {
            $rules['so_do'] = 'required|image|mimes:jpeg,png,jpg|max:5120';
        }

        $request->validate($rules, $messages);

        // 1. Lưu trữ an toàn bằng Storage của Laravel trên ổ đĩa 'public'
        $matTruocPath = $request->file('mat_truoc_cccd')->store('kyc', 'public');
        $matSauPath = $request->file('mat_sau_cccd')->store('kyc', 'public');

        // Áp dụng thuật toán Đóng dấu bản quyền (Watermark)
        $this->applyWatermark($matTruocPath);
        $this->applyWatermark($matSauPath);

        // 2. Chuyển đường dẫn public để có thể truy cập qua Web
        $thongTinCccd = [
            'mat_truoc' => Storage::disk('public')->url($matTruocPath),
            'mat_sau' => Storage::disk('public')->url($matSauPath),
            'ngay_gui' => now()->toDateTimeString()
        ];

        // Xử lý lưu Sổ đỏ nếu có
        if ($user->vai_tro === 'chu_tro' && $request->hasFile('so_do')) {
            $soDoPath = $request->file('so_do')->store('kyc', 'public');
            $this->applyWatermark($soDoPath);
            $thongTinCccd['so_do'] = Storage::disk('public')->url($soDoPath);
        }

        // 3. Cập nhật vào trường JSONB thong_tin_cccd và set trạng thái chờ duyệt
        $user->update([
            'thong_tin_cccd' => $thongTinCccd,
            'da_xac_thuc_cccd' => false // false có nghĩa là đã gửi nhưng đang chờ duyệt (hoặc chưa làm gì cả). Ta ngầm hiểu có thong_tin_cccd mà da_xac_thuc_cccd = false là Chờ duyệt.
        ]);

        return redirect()->back()->with('success', 'Đã gửi yêu cầu xác thực thành công. Chúng tôi sẽ duyệt hồ sơ của bạn sớm nhất!');
    }


    /**
     * ==========================================
     * LUỒNG 2: CTV XÁC THỰC THỰC ĐỊA PHÒNG TRỌ
     * ==========================================
     */

    /**
     * Hiển thị danh sách phòng trọ đang chờ xác thực thực địa (muc_do_xac_thuc = 1)
     */
    public function ctvIndex()
    {
        // Lấy các phòng đã duyệt online (muc_do_xac_thuc = 1) để CTV đi check
        $ds_phong_cho = PhongTro::where('muc_do_xac_thuc', 1)->with('chuTro')->get();

        return view('ctv.index', compact('ds_phong_cho'));
    }

    /**
     * Hiển thị Form báo cáo checklist thực địa cho 1 phòng cụ thể
     */
    public function ctvBaoCaoForm($id)
    {
        $phong = PhongTro::findOrFail($id);
        return view('ctv.bao_cao', compact('phong'));
    }

    /**
     * Xử lý lưu form checklist và nâng mức độ xác thực
     */
    public function ctvNopBaoCao(Request $request, $id)
    {
        $phong = PhongTro::findOrFail($id);
        $ctv = Auth::user();

        // Validate đầu vào
        $request->validate([
            'phong_giong_anh' => 'required|boolean',
            'nuoc_sach' => 'required|boolean',
            'an_ninh' => 'required|boolean',
            'ghi_chu_thuc_dia' => 'nullable|string',
            'anh_thuc_dia' => 'required|array|min:1',
            'anh_thuc_dia.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'anh_thuc_dia.required' => 'Vui lòng tải lên ít nhất 1 ảnh thực địa đối chứng.',
            'anh_thuc_dia.min' => 'Vui lòng tải lên ít nhất 1 ảnh thực địa đối chứng.',
            'anh_thuc_dia.*.image' => 'File tải lên phải là hình ảnh.',
            'anh_thuc_dia.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg.',
            'anh_thuc_dia.*.max' => 'Kích thước mỗi ảnh tối đa là 5MB.',
        ]);

        $anhUrls = [];
        if ($request->hasFile('anh_thuc_dia')) {
            foreach ($request->file('anh_thuc_dia') as $file) {
                $path = $file->store('thuc_dia', 'public');
                $this->applyWatermark($path);
                $anhUrls[] = Storage::disk('public')->url($path);
            }
        }

        // 1. Tạo mảng Checklist
        $baoCaoChiTiet = [
            'phong_giong_anh' => $request->boolean('phong_giong_anh'),
            'nuoc_sach' => $request->boolean('nuoc_sach'),
            'an_ninh' => $request->boolean('an_ninh'),
            'ghi_chu' => $request->input('ghi_chu_thuc_dia'),
            'hinh_anh' => $anhUrls
        ];

        DB::beginTransaction();
        try {
            // 2. Insert vào bảng xac_thuc_thuc_dia
            DB::table('xac_thuc_thuc_dia')->insert([
                'id_phong' => $phong->id,
                'id_nguoi_xac_thuc' => $ctv->id,
                'bao_cao_chi_tiet' => json_encode($baoCaoChiTiet),
                'trang_thai' => 'cho_duyet', // Chuyển thành cho_duyet để Admin duyệt
                'ngay_thuc_hien' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Không tự động nâng cấp muc_do_xac_thuc nữa, việc này dành cho Admin
            $message = 'Báo cáo đã được gửi thành công! Vui lòng chờ Admin xét duyệt.';

            DB::commit();
            return redirect()->route('ctv.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi lưu báo cáo: ' . $e->getMessage());
        }
    }

    /**
     * Thuật toán Đóng dấu bản quyền (Watermark) vào ảnh eKYC
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
