<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DangKyController extends Controller
{
    /**
     * Hiển thị giao diện đăng ký
     */
    public function hienThiForm()
    {
        return view('auth.dang_ky');
    }

    /**
     * Xử lý đăng ký tài khoản
     */
    public function dangKy(Request $request)
    {
        $validated = $request->validate([
            'ho_ten' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:nguoi_dung,email'],
            'mat_khau' => ['required', 'string', 'min:8', 'confirmed'],
            'vai_tro' => ['required', 'in:nguoi_tim_tro,chu_tro'],
        ], [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'vai_tro.in' => 'Vai trò không hợp lệ.'
        ]);

        $user = NguoiDung::create([
            'ho_ten' => $validated['ho_ten'],
            'email' => $validated['email'],
            'mat_khau' => Hash::make($validated['mat_khau']),
            'vai_tro' => $validated['vai_tro'],
            'khao_sat_loi_song' => [] // Khởi tạo mảng JSON rỗng
        ]);

        // Tự động đăng nhập ngay sau khi đăng ký
        Auth::login($user);

        // Điều hướng dựa trên vai trò
        if ($user->vai_tro === 'nguoi_tim_tro') {
            return redirect('/khao-sat-loi-song')->with('success', 'Đăng ký thành công! Vui lòng hoàn thành bài khảo sát lối sống.');
        }

        return redirect('/tim-kiem-goi-y')->with('success', 'Đăng ký thành công!');
    }
}
