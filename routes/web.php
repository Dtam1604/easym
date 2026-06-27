<?php

use Illuminate\Support\Facades\Route;
// 1. IMPORT CÁC CONTROLLER VÀO ĐÂY ĐỂ TRÁNH LỖI 'Target class does not exist'
use App\Http\Controllers\NguoiDungController;
use App\Http\Controllers\GoiYController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TimBanController;

use App\Http\Controllers\DangKyController;
use App\Http\Controllers\DangNhapController;

use App\Http\Controllers\XacThucController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Nơi đây đăng ký toàn bộ các đường dẫn (URL) cho ứng dụng Web của bạn.
*/

// Cung cấp trực tiếp ảnh KYC ra ngoài mà không bị chặn bởi route nội bộ của Laravel
Route::get('/storage/kyc/{filename}', function ($filename) {
    $filePath = storage_path('app/public/kyc/' . $filename);
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    abort(404);
});

// Cung cấp trực tiếp ảnh thực địa ra ngoài mà không bị chặn bởi route nội bộ của Laravel
Route::get('/storage/thuc_dia/{filename}', function ($filename) {
    $filePath = storage_path('app/public/thuc_dia/' . $filename);
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    abort(404);
});

// Trang chủ giới thiệu (Landing Page)
Route::get('/', function() {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| NHÓM ROUTE PUBLIC (KHÔNG CẦN ĐĂNG NHẬP)
|--------------------------------------------------------------------------
*/
Route::get('/dang-nhap', [DangNhapController::class, 'hienThiForm'])->name('login');
Route::post('/dang-nhap', [DangNhapController::class, 'dangNhap']);
Route::get('/dang-ky', [DangKyController::class, 'hienThiForm'])->name('register');
Route::post('/dang-ky', [DangKyController::class, 'dangKy']);
Route::post('/dang-xuat', [DangNhapController::class, 'dangXuat'])->name('logout');

// Quên mật khẩu & Đặt lại mật khẩu
Route::get('/quen-mat-khau', [\App\Http\Controllers\QuenMatKhauController::class, 'hienThiFormQuenMatKhau'])->name('password.request');
Route::post('/quen-mat-khau', [\App\Http\Controllers\QuenMatKhauController::class, 'guiLinkDatLaiMatKhau'])->name('password.email');
Route::get('/dat-lai-mat-khau', [\App\Http\Controllers\QuenMatKhauController::class, 'hienThiFormDatLaiMatKhau'])->name('password.reset');
Route::post('/dat-lai-mat-khau', [\App\Http\Controllers\QuenMatKhauController::class, 'capNhatMatKhau'])->name('password.update');

// Đăng nhập bằng Google
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| NHÓM ROUTE YÊU CẦU ĐĂNG NHẬP (Middleware 'auth')
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->group(function () {
    
    // Định danh người dùng KYC (Dành cho cả Người tìm trọ và Chủ trọ)
    Route::get('/xac-thuc-danh-tinh', [XacThucController::class, 'kycForm'])->name('kyc.form');
    Route::post('/xac-thuc-danh-tinh', [XacThucController::class, 'guiYeuCauKYC'])->name('kyc.submit');

    // Cụm Route cho Hồ sơ (Profile) cá nhân
    Route::get('/ho-so', [NguoiDungController::class, 'editProfile'])->name('profile.edit');
    Route::put('/ho-so', [NguoiDungController::class, 'updateProfile'])->name('profile.update');

    // Cụm Route cho Bản đồ & Tìm trọ (Tất cả User đã login đều dùng được)
    Route::get('/tim-kiem-goi-y', [GoiYController::class, 'index'])->name('search.results');
    Route::get('/phong-tro/{id}', [GoiYController::class, 'show'])->name('room.show');
    Route::post('/phong-tro/{id}/bao-cao', [GoiYController::class, 'baoCaoPhong'])->name('room.report');

    // Nhóm tính năng cho NGƯỜI TÌM TRỌ
    Route::middleware('auth.role:nguoi_tim_tro')->group(function() {
        Route::get('/khao-sat-loi-song', [NguoiDungController::class, 'showSurvey'])->name('survey.show');
        Route::post('/api/survey/update', [NguoiDungController::class, 'updateSurvey'])->name('survey.update');
        Route::get('/tim-ban-o-ghep', [TimBanController::class, 'danhSachGoiYBan'])->name('tim-ban.index');
        
        // AJAX Lời mời kết nối
        Route::post('/tim-ban-o-ghep/ket-noi/{id}', [TimBanController::class, 'guiLoiMoi']);
        Route::post('/tim-ban-o-ghep/chap-nhan/{id}', [TimBanController::class, 'chapNhanLoiMoi']);
        Route::post('/tim-ban-o-ghep/tu-choi/{id}', [TimBanController::class, 'tuChoiLoiMoi']);
        Route::post('/tim-ban-o-ghep/huy-ket-noi/{id}', [TimBanController::class, 'huyKetNoi']);
    });

    // Nhóm tính năng cho CHỦ TRỌ
    Route::middleware('auth.role:chu_tro')->group(function() {
        Route::get('/quan-ly-phong', [App\Http\Controllers\ChuTroController::class, 'danhSachPhong'])->name('chutro.phong');
        Route::get('/quan-ly-phong/tao-moi', [App\Http\Controllers\ChuTroController::class, 'create'])->name('chutro.phong.create');
        Route::post('/quan-ly-phong/luu', [App\Http\Controllers\ChuTroController::class, 'store'])->name('chutro.phong.store');
        Route::get('/quan-ly-phong/sua/{id}', [App\Http\Controllers\ChuTroController::class, 'edit'])->name('chutro.phong.edit');
        Route::put('/quan-ly-phong/cap-nhat/{id}', [App\Http\Controllers\ChuTroController::class, 'update'])->name('chutro.phong.update');
        Route::post('/api/phong/{id}/toggle-thue', [App\Http\Controllers\ChuTroController::class, 'toggleTrangThaiThue']);
        Route::post('/api/phong/{id}/yeu-cau-xac-thuc', [App\Http\Controllers\ChuTroController::class, 'yeuCauXacThuc']);
        Route::get('/quan-ly-lich-hen', [App\Http\Controllers\DatLichController::class, 'danhSachLichHenChuTro'])->name('chutro.lich_hen');
        Route::post('/api/lich-hen/{id}/cap-nhat', [App\Http\Controllers\DatLichController::class, 'capNhatTrangThai']);
    });

    // Nhóm tính năng cho CỘNG TÁC VIÊN (Xác thực thực địa)
    Route::middleware('auth.role:cong_tac_vien')->prefix('ctv')->group(function() {
        Route::get('/', [XacThucController::class, 'ctvIndex'])->name('ctv.index');
        Route::get('/bao-cao/{id}', [XacThucController::class, 'ctvBaoCaoForm'])->name('ctv.baocao');
        Route::post('/bao-cao/{id}', [XacThucController::class, 'ctvNopBaoCao'])->name('ctv.baocao.submit');
    });

    // Route dùng chung khi đã đăng nhập (Người tìm trọ đặt lịch)
    Route::post('/api/dat-lich', [App\Http\Controllers\DatLichController::class, 'guiYeuCau'])->name('dat_lich.gui');

    // Route cho Hệ thống Thông báo (Bất kỳ user nào đăng nhập cũng dùng được)
    Route::prefix('thong-bao')->group(function() {
        Route::get('/', [\App\Http\Controllers\ThongBaoController::class, 'index'])->name('thong_bao.index');
        Route::get('/doc/{id}', [\App\Http\Controllers\ThongBaoController::class, 'docThongBao'])->name('thong_bao.doc');
        Route::post('/doc-tat-ca', [\App\Http\Controllers\ThongBaoController::class, 'docTatCa'])->name('thong_bao.doc_tat_ca');
    });
});

/*
|--------------------------------------------------------------------------
| NHÓM ROUTE DÀNH CHO ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware('auth.role:admin')->prefix('admin')->group(function() {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/cap-nhat-trong-so', [AdminController::class, 'updateWeights'])->name('admin.weights.update');
    Route::post('/duyet-phong/{id}', [AdminController::class, 'approveRoom'])->name('admin.room.approve');
    Route::post('/tu-choi-phong/{id}', [AdminController::class, 'rejectRoom'])->name('admin.room.reject');
    Route::get('/api/stats', [AdminController::class, 'getStats']);

    // Quản lý Tiêu chí Khảo sát (CRUD)
    Route::get('/tieu-chi', [AdminController::class, 'tieuchiIndex'])->name('admin.tieuchi.index');
    Route::post('/tieu-chi', [AdminController::class, 'tieuchiStore'])->name('admin.tieuchi.store');
    Route::get('/tieu-chi/{id}/edit', [AdminController::class, 'tieuchiEdit'])->name('admin.tieuchi.edit');
    Route::put('/tieu-chi/{id}', [AdminController::class, 'tieuchiUpdate'])->name('admin.tieuchi.update');
    Route::delete('/tieu-chi/{id}', [AdminController::class, 'tieuchiDestroy'])->name('admin.tieuchi.destroy');

    // Quản lý Báo cáo vi phạm
    Route::get('/bao-cao', [AdminController::class, 'danhSachBaoCao'])->name('admin.baocao.index');
    Route::post('/bao-cao/{id}/xu-ly', [AdminController::class, 'xuLyBaoCao'])->name('admin.baocao.xuly');

    // Quản lý Duyệt KYC
    Route::get('/kyc', [AdminController::class, 'kycIndex'])->name('admin.kyc.index');
    Route::post('/kyc/{id}/approve', [AdminController::class, 'kycApprove'])->name('admin.kyc.approve');
    Route::post('/kyc/{id}/reject', [AdminController::class, 'kycReject'])->name('admin.kyc.reject');

    // Quản lý Tất cả phòng trọ
    Route::get('/phong-tro', [AdminController::class, 'phongtroIndex'])->name('admin.phongtro.index');
    Route::delete('/phong-tro/{id}', [AdminController::class, 'phongtroDestroy'])->name('admin.phongtro.destroy');

    // Quản lý Tài khoản (Người dùng)
    Route::get('/nguoi-dung', [AdminController::class, 'nguoidungIndex'])->name('admin.nguoidung.index');
    Route::delete('/nguoi-dung/{id}', [AdminController::class, 'nguoidungDestroy'])->name('admin.nguoidung.destroy');
    Route::post('/nguoi-dung/{id}/toggle-lock', [AdminController::class, 'userToggleLock'])->name('admin.nguoidung.toggle_lock');

    // Quản lý Cộng tác viên (UC19)
    Route::get('/ctv-list', [AdminController::class, 'ctvManageIndex'])->name('admin.ctv.index');
    Route::post('/ctv-list', [AdminController::class, 'ctvManageStore'])->name('admin.ctv.store');
    Route::post('/ctv-list/{id}/toggle-lock', [AdminController::class, 'ctvManageToggleLock'])->name('admin.ctv.toggle_lock');
    Route::post('/ctv-list/{id}/update-region', [AdminController::class, 'ctvManageUpdateRegion'])->name('admin.ctv.update_region');
});

// Các API gọi từ AJAX Frontend của Admin Dashboard
Route::prefix('api/admin')->group(function () {
    // API Cập nhật trọng số (AJAX)
    Route::post('/weights/update', [AdminController::class, 'updateWeights']);
    
    // API Duyệt phòng
    Route::post('/rooms/{id}/approve', [AdminController::class, 'approveRoom']);
    
    // API Thống kê cho Chart.js
    Route::get('/stats', [AdminController::class, 'getStats']);
});
