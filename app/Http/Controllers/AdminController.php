<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhongTro;
use App\Models\NguoiDung;
use App\Models\TrongSoThuatToan;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Hien thi Dashboard voi so lieu co ban.
     */
    public function index()
    {
        $danhSachTrongSo = TrongSoThuatToan::all();
        // Lấy danh sách các báo cáo thực địa đang chờ duyệt từ CTV
        $baoCaoChoDuyet = \App\Models\XacThucThucDia::with(['phongTro.chuTro', 'congTacVien'])
            ->where('trang_thai', 'cho_duyet')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.dashboard', compact('danhSachTrongSo', 'baoCaoChoDuyet'));
    }

    /**
     * API (AJAX): Cap nhat Trong so Thuat toan
     * Validation dam bao khong nhap so am lam hong thuat toan.
     */
    public function updateWeights(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:trong_so_thuat_toan,id',
            'trong_so_nen' => 'required|numeric|min:0.1|max:10',
            'he_so_uu_tien' => 'required|numeric|min:1.0|max:5.0',
        ]);

        $trongSo = TrongSoThuatToan::find($validated['id']);
        $trongSo->trong_so_nen = $validated['trong_so_nen'];
        $trongSo->he_so_uu_tien = $validated['he_so_uu_tien'];
        $trongSo->save();

        /*
         * GIAI THICH TAC DONG DEN THUAT TOAN:
         * Khi Admin tang 'trong_so_nen' cua 'do_sach_se' tu 1 len 3, 
         * thi diem Matching cho nhung cap nguoi dung co cung quan diem ve sach se se tang vot (+3 diem).
         * Neu tang 'he_so_uu_tien' len 2.0, thi diem Boost (Neu user list no vao danh sach Uu tien) se = 3 * 2.0 = 6 diem.
         * Thay doi nay anh huong TRUC TIEP theo thoi gian thuc tren giao dien Goi Y cua nguoi dung.
         */

        return response()->json([
            'success' => true, 
            'message' => 'Cập nhật trọng số thành công!'
        ]);
    }

    /**
     * API: Phe duyet bao cao thuc dia tu CTV
     */
    public function approveRoom(Request $request, $id)
    {
        // $id ở đây là ID của báo cáo thực địa (XacThucThucDia)
        $baoCao = \App\Models\XacThucThucDia::findOrFail($id);
        $baoCao->trang_thai = 'da_duyet';
        $baoCao->save();

        $phong = PhongTro::findOrFail($baoCao->id_phong);
        // Nang cap tu muc 1 (Cho CTV/Admin duyet) len muc 2 (Da xac thuc thuc dia uy tin)
        $phong->muc_do_xac_thuc = 2; 
        $phong->save();

        return redirect()->back()->with('success', 'Đã phê duyệt báo cáo! Phòng trọ hiện đã có nhãn Xác Thực Thực Địa.');
    }

    /**
     * API: Tu choi bao cao thuc dia
     */
    public function rejectRoom(Request $request, $id)
    {
        $baoCao = \App\Models\XacThucThucDia::findOrFail($id);
        $baoCao->trang_thai = 'tu_choi';
        $baoCao->save();

        $phong = PhongTro::findOrFail($baoCao->id_phong);
        // Ha cap phong tro xuong muc 0 (Chua xac thuc) de chu tro lam lai hoac tuc do hien thi van la 1.
        // O day ta set ve 0 de yeu cau lam lai tu dau.
        $phong->muc_do_xac_thuc = 0;
        $phong->save();

        return redirect()->back()->with('success', 'Đã từ chối báo cáo. Phòng trọ bị giáng cấp về Chưa xác thực.');
    }

    /**
     * API: Lay du lieu bieu do cho Chart.js
     */
    public function getStats()
    {
        // 1. Ty le nguoi dung (Pie Chart)
        $userStatsRaw = NguoiDung::select('vai_tro', DB::raw('count(*) as total'))
            ->groupBy('vai_tro')
            ->get();
            
        $usersLabelMap = [
            'admin' => 'Quản trị viên',
            'chu_tro' => 'Chủ trọ',
            'nguoi_tim_tro' => 'Người tìm trọ',
            'cong_tac_vien' => 'Cộng tác viên'
        ];

        $userLabels = [];
        $userData = [];
        foreach($userStatsRaw as $stat) {
            $userLabels[] = $usersLabelMap[$stat->vai_tro] ?? $stat->vai_tro;
            $userData[] = $stat->total;
        }

        // 2. So luong phong theo khu vuc (Bar Chart)
        // Vi du nay gia lap nhom cac phong tro theo id de ve bieu do (Trong thuc te se query ST_Within de group theo quan/huyen)
        $roomStats = [
            'Xuân Mai' => PhongTro::where('id', '<=', 4)->count() ?? 5,
            'Hòa Lạc' => PhongTro::whereBetween('id', [5, 8])->count() ?? 3,
            'Hà Đông' => PhongTro::where('id', '>', 8)->count() ?? 2,
        ];

        return response()->json([
            'users' => [
                'labels' => $userLabels,
                'data' => $userData
            ],
            'rooms' => [
                'labels' => array_keys($roomStats),
                'data' => array_values($roomStats)
            ]
        ]);
    }

    /**
     * Hien thi danh sach tieu chi (Dynamic Criteria)
     */
    public function tieuchiIndex()
    {
        $ds_tieu_chi = TrongSoThuatToan::all();
        return view('admin.tieuchi', compact('ds_tieu_chi'));
    }

    /**
     * Luu tieu chi moi tu Form
     */
    public function tieuchiStore(Request $request)
    {
        $validated = $request->validate([
            'ten_tieu_chi' => 'required|string|unique:trong_so_thuat_toan,ten_tieu_chi',
            'tieu_de_hien_thi' => 'required|string',
            'loai_input' => 'required|in:boolean,scale5,text,select',
            'trong_so_nen' => 'required|numeric',
            'he_so_uu_tien' => 'required|numeric'
        ]);

        TrongSoThuatToan::create($validated);

        return redirect()->back()->with('success', 'Đã thêm tiêu chí mới thành công! Hệ thống ghép đôi và Khảo sát đã được cập nhật.');
    }

    /**
     * Hien thi form chinh sua tieu chi
     */
    public function tieuchiEdit($id)
    {
        $tieuChi = TrongSoThuatToan::findOrFail($id);
        return view('admin.tieuchi_edit', compact('tieuChi'));
    }

    /**
     * Cap nhat tieu chi vao CSDL
     */
    public function tieuchiUpdate(Request $request, $id)
    {
        $tieuChi = TrongSoThuatToan::findOrFail($id);

        $validated = $request->validate([
            'ten_tieu_chi' => 'required|string|unique:trong_so_thuat_toan,ten_tieu_chi,' . $id,
            'tieu_de_hien_thi' => 'required|string',
            'loai_input' => 'required|in:boolean,scale5,text,select',
            'trong_so_nen' => 'required|numeric',
            'he_so_uu_tien' => 'required|numeric'
        ]);

        $tieuChi->update($validated);

        return redirect()->route('admin.tieuchi.index')->with('success', 'Cập nhật tiêu chí thành công!');
    }

    /**
     * Xoa tieu chi
     */
    public function tieuchiDestroy($id)
    {
        $tieuChi = TrongSoThuatToan::findOrFail($id);
        $tieuChi->delete();

        return redirect()->back()->with('success', 'Đã xóa tiêu chí thành công!');
    }

    /**
     * Hiển thị danh sách KYC đang chờ duyệt
     */
    public function kycIndex()
    {
        // Lấy danh sách user có thong_tin_cccd nhưng chưa được xác thực
        // Và bỏ qua những người bị từ chối (trang_thai = tu_choi)
        $ds_kyc = NguoiDung::where('da_xac_thuc_cccd', false)
                    ->whereNotNull('thong_tin_cccd')
                    ->whereRaw("(thong_tin_cccd->>'trang_thai' IS NULL OR thong_tin_cccd->>'trang_thai' != 'tu_choi')")
                    ->orderBy('updated_at', 'desc')
                    ->get();

        // Lấy danh sách user đã được xác thực thành công
        $ds_da_duyet = NguoiDung::where('da_xac_thuc_cccd', true)
                    ->whereNotNull('thong_tin_cccd')
                    ->orderBy('updated_at', 'desc')
                    ->get();

        return view('admin.kyc_list', compact('ds_kyc', 'ds_da_duyet'));
    }

    /**
     * Phê duyệt hồ sơ KYC
     */
    public function kycApprove($id)
    {
        $user = NguoiDung::findOrFail($id);
        $user->da_xac_thuc_cccd = true;
        
        // Cập nhật thêm trạng thái vào JSON (Optional)
        $thongTin = $user->thong_tin_cccd;
        $thongTin['trang_thai'] = 'da_duyet';
        $user->thong_tin_cccd = $thongTin;
        
        $user->save();

        return redirect()->back()->with('success', 'Đã phê duyệt hồ sơ KYC thành công!');
    }

    /**
     * Từ chối hồ sơ KYC kèm lý do
     */
    public function kycReject(Request $request, $id)
    {
        $request->validate([
            'ly_do' => 'required|string|max:255'
        ]);

        $user = NguoiDung::findOrFail($id);
        
        // Không xóa mảng để giữ lịch sử, nhưng cập nhật trạng thái thành tu_choi
        // Xóa thông tin ảnh để tiết kiệm dung lượng (Optional), ở đây ta chỉ đánh dấu từ chối để user upload lại.
        $user->thong_tin_cccd = [
            'trang_thai' => 'tu_choi',
            'ly_do' => $request->input('ly_do'),
            'ngay_tu_choi' => now()->toDateTimeString()
        ];
        $user->da_xac_thuc_cccd = false;
        $user->save();

        return redirect()->back()->with('success', 'Đã từ chối hồ sơ KYC!');
    }

    /**
     * Quản lý Báo cáo vi phạm
     */
    public function danhSachBaoCao()
    {
        $baocaos = \App\Models\BaoCaoPhong::with(['nguoiBaoCao', 'phong', 'phong.chuTro'])
            ->orderByRaw("CASE WHEN trang_thai = 'chua_xu_ly' THEN 1 WHEN trang_thai = 'dang_xem_xet' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.bao_cao', compact('baocaos'));
    }

    /**
     * Xử lý báo cáo
     */
    public function xuLyBaoCao(Request $request, $id)
    {
        $baocao = \App\Models\BaoCaoPhong::findOrFail($id);
        
        $request->validate([
            'hanh_dong' => 'required|in:dang_xem_xet,da_giai_quyet,xoa_phong'
        ]);
        
        $hanhDong = $request->input('hanh_dong');
        
        if ($hanhDong === 'xoa_phong') {
            // Xóa phòng
            $phong = $baocao->phong;
            if ($phong) {
                $phong->delete();
            }
            $baocao->trang_thai = 'da_giai_quyet';
            $baocao->save();
            return redirect()->back()->with('success', 'Đã xóa phòng trọ vi phạm và đánh dấu báo cáo là Đã giải quyết.');
        }
        
        $baocao->trang_thai = $hanhDong;
        $baocao->save();
        
        return redirect()->back()->with('success', 'Đã cập nhật trạng thái báo cáo thành công.');
    }

    /**
     * Hien thi danh sach tat ca phong tro cho Admin quan ly
     */
    public function phongtroIndex()
    {
        // Lay tat ca phong tro kem thong tin chu tro, phan trang 20 phong/trang
        $phongTros = PhongTro::with('chuTro')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.phong_tro_list', compact('phongTros'));
    }

    /**
     * Xoa phong tro (Admin quyen cao nhat)
     */
    public function phongtroDestroy($id)
    {
        try {
            $phong = PhongTro::findOrFail($id);
            $phong->delete();
            return redirect()->route('admin.phongtro.index')->with('success', 'Đã xóa phòng trọ thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.phongtro.index')->with('error', 'Lỗi khi xóa phòng trọ: ' . $e->getMessage());
        }
    }
    /**
     * Hien thi danh sach tat ca tai khoan nguoi dung cho Admin
     */
    public function nguoidungIndex()
    {
        // Lay danh sach nguoi dung, phan trang 20/trang
        $nguoiDungs = NguoiDung::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.nguoi_dung_list', compact('nguoiDungs'));
    }

    /**
     * Xoa tai khoan nguoi dung (Admin quyen cao nhat)
     */
    public function nguoidungDestroy($id)
    {
        try {
            if ($id == auth()->id()) {
                return redirect()->route('admin.nguoidung.index')->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
            }
            $nguoiDung = NguoiDung::findOrFail($id);
            $nguoiDung->delete();
            return redirect()->route('admin.nguoidung.index')->with('success', 'Đã xóa tài khoản thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.nguoidung.index')->with('error', 'Lỗi khi xóa tài khoản: ' . $e->getMessage());
        }
    }

    /**
     * UC19 - Quản lý danh sách Cộng tác viên (CTV)
     */
    public function ctvManageIndex()
    {
        $ctvs = NguoiDung::where('vai_tro', 'cong_tac_vien')
            ->withCount('xacThucThucDias')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.ctv_list', compact('ctvs'));
    }

    /**
     * UC19 - Thêm mới tài khoản CTV
     */
    public function ctvManageStore(Request $request)
    {
        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi_dung,email',
            'so_dien_thoai' => 'required|string|max:15|unique:nguoi_dung,so_dien_thoai',
            'dia_ban_quan_ly' => 'nullable|string|max:255',
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
            'so_dien_thoai.unique' => 'Số điện thoại này đã tồn tại trong hệ thống.',
        ]);

        NguoiDung::create([
            'ho_ten' => $validated['ho_ten'],
            'email' => $validated['email'],
            'so_dien_thoai' => $validated['so_dien_thoai'],
            'dia_ban_quan_ly' => $validated['dia_ban_quan_ly'],
            'vai_tro' => 'cong_tac_vien',
            'mat_khau' => bcrypt('123456'), // Mật khẩu mặc định là 123456
            'trang_thai_khoa' => false,
        ]);

        return redirect()->back()->with('success', 'Đã tạo tài khoản Cộng tác viên thành công! Mật khẩu mặc định là: 123456');
    }

    /**
     * UC19 - Khóa/Mở khóa tài khoản CTV
     */
    public function ctvManageToggleLock($id)
    {
        $ctv = NguoiDung::where('vai_tro', 'cong_tac_vien')->findOrFail($id);
        $ctv->trang_thai_khoa = !$ctv->trang_thai_khoa;
        $ctv->save();

        $status = $ctv->trang_thai_khoa ? 'Khóa' : 'Mở khóa';
        return redirect()->back()->with('success', "Đã {$status} tài khoản Cộng tác viên {$ctv->ho_ten} thành công!");
    }

    /**
     * UC19 - Cập nhật phân vùng địa bàn quản lý cho CTV
     */
    public function ctvManageUpdateRegion(Request $request, $id)
    {
        $request->validate([
            'dia_ban_quan_ly' => 'nullable|string|max:255'
        ]);

        $ctv = NguoiDung::where('vai_tro', 'cong_tac_vien')->findOrFail($id);
        $ctv->dia_ban_quan_ly = $request->input('dia_ban_quan_ly');
        $ctv->save();

        return redirect()->back()->with('success', "Đã cập nhật địa bàn quản lý cho CTV {$ctv->ho_ten} thành công!");
    }

    /**
     * Khóa/Mở khóa tài khoản bất kỳ (Admin, Chủ trọ, Người tìm trọ, CTV)
     */
    public function userToggleLock($id)
    {
        try {
            if ($id == auth()->id()) {
                return redirect()->back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình!');
            }
            $user = NguoiDung::findOrFail($id);
            $user->trang_thai_khoa = !$user->trang_thai_khoa;
            $user->save();

            $status = $user->trang_thai_khoa ? 'Khóa' : 'Mở khóa';
            return redirect()->back()->with('success', "Đã {$status} tài khoản {$user->ho_ten} thành công!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi thay đổi trạng thái khóa tài khoản: ' . $e->getMessage());
        }
    }
}
