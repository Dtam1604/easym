<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DangNhapController extends Controller
{
    /**
     * Hiển thị giao diện đăng nhập
     */
    public function hienThiForm()
    {
        return view('auth.dang_nhap');
    }

    /**
     * Xử lý đăng nhập
     */
    public function dangNhap(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'mat_khau' => ['required'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu.'
        ]);

        // Sử dụng Auth::attempt.
        // Cấu hình auth.php đã trỏ tới NguoiDung::class.
        // Eloquent ánh xạ 'password' sang cột 'mat_khau' qua hàm getAuthPassword() trong model
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['mat_khau']])) {
            $request->session()->regenerate();

            // Kiểm tra vai_tro để điều hướng
            $vaiTro = Auth::user()->vai_tro;
            if ($vaiTro === 'admin') {
                return redirect('/admin');
            } elseif ($vaiTro === 'nguoi_tim_tro') {
                // Nếu chưa làm khảo sát thì tự động văng vào trang khảo sát
                return redirect()->intended('/khao-sat-loi-song');
            } elseif ($vaiTro === 'chu_tro') {
                // Đưa chủ trọ vào trang Quản lý phòng
                return redirect('/quan-ly-phong');
            } elseif ($vaiTro === 'cong_tac_vien') {
                // Trang của CTV
                return redirect('/ctv');
            }

            return redirect('/');
        }

        // Trả về lỗi nếu đăng nhập thất bại
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    /**
     * Xử lý đăng xuất
     */
    public function dangXuat(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
