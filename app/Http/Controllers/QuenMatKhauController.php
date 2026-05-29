<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class QuenMatKhauController extends Controller
{
    /**
     * Hiển thị giao diện yêu cầu quên mật khẩu
     */
    public function hienThiFormQuenMatKhau()
    {
        return view('auth.quen_mat_khau');
    }

    /**
     * Xử lý yêu cầu gửi link đặt lại mật khẩu (Mock)
     */
    public function guiLinkDatLaiMatKhau(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:nguoi_dung,email'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.exists' => 'Email này không tồn tại trong hệ thống.'
        ]);

        $email = $request->input('email');
        $token = Str::random(40);

        // Lưu tạm token vào session để xác thực ở bước sau
        session(['password_reset_token_' . $email => $token]);

        return back()->with([
            'success' => 'Đã gửi link đặt lại mật khẩu thành công.',
            'demo_email' => $email,
            'demo_token' => $token
        ]);
    }

    /**
     * Hiển thị giao diện đặt lại mật khẩu mới
     */
    public function hienThiFormDatLaiMatKhau(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        // Kiểm tra token hợp lệ trong session
        $savedToken = session('password_reset_token_' . $email);

        if (!$savedToken || $savedToken !== $token) {
            return redirect()->route('login')->with('error', 'Liên kết đặt lại mật khẩu đã hết hạn hoặc không hợp lệ.');
        }

        return view('auth.dat_lai_mat_khau', [
            'email' => $email,
            'token' => $token
        ]);
    }

    /**
     * Xử lý cập nhật mật khẩu mới
     */
    public function capNhatMatKhau(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:nguoi_dung,email'],
            'token' => ['required'],
            'mat_khau' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.exists' => 'Email không tồn tại.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu mới.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $email = $request->input('email');
        $token = $request->input('token');

        // Xác thực token
        $savedToken = session('password_reset_token_' . $email);

        if (!$savedToken || $savedToken !== $token) {
            return redirect()->route('login')->with('error', 'Yêu cầu không hợp lệ hoặc liên kết đã hết hạn.');
        }

        // Cập nhật mật khẩu mới
        $user = NguoiDung::where('email', $email)->first();
        if ($user) {
            $user->mat_khau = Hash::make($request->input('mat_khau'));
            $user->save();

            // Xóa token khỏi session
            session()->forget('password_reset_token_' . $email);

            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập bằng mật khẩu mới.');
        }

        return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại.');
    }
}
