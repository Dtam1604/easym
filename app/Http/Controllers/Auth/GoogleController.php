<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang đăng nhập Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Xử lý Callback từ Google trả về
     */
    public function handleGoogleCallback()
    {
        try {
            // Lấy thông tin user từ Google
            $googleUser = Socialite::driver('google')->user();

            // Tìm xem user đã tồn tại trong DB chưa bằng google_id hoặc email
            $user = NguoiDung::where('google_id', $googleUser->id)
                             ->orWhere('email', $googleUser->email)
                             ->first();

            if ($user) {
                // Nếu user đã tồn tại (đăng ký thường hoặc đã liên kết Google trước đó)
                // Cập nhật google_id và ảnh đại diện nếu chưa có
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
                
                // Đăng nhập
                Auth::login($user);
            } else {
                // Nếu là user hoàn toàn mới, tự động tạo tài khoản (mặc định vai_tro = nguoi_tim_tro)
                $newUser = NguoiDung::create([
                    'ho_ten' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'anh_dai_dien' => $googleUser->avatar,
                    'mat_khau' => bcrypt(Str::random(16)), // Mật khẩu ngẫu nhiên bảo mật
                    'vai_tro' => 'nguoi_tim_tro',
                ]);

                Auth::login($newUser);
            }

            // Chuyển hướng theo vai trò (sử dụng lại logic của form đăng nhập)
            $loggedUser = Auth::user();
            if ($loggedUser->vai_tro === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập Google thành công.');
            } elseif ($loggedUser->vai_tro === 'chu_tro') {
                return redirect()->route('chutro.phong')->with('success', 'Đăng nhập Google thành công.');
            } elseif ($loggedUser->vai_tro === 'cong_tac_vien') {
                return redirect()->route('ctv.index')->with('success', 'Đăng nhập Google thành công.');
            } else {
                return redirect()->route('survey.show')->with('success', 'Đăng nhập Google thành công.');
            }

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Lỗi Google OAuth: ' . $e->getMessage() . ' - Stack Trace: ' . $e->getTraceAsString());
            return redirect()->route('login')->with('error', 'Lỗi đăng nhập Google: ' . $e->getMessage());
        }
    }
}
