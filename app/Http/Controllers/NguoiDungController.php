<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NguoiDungController extends Controller
{
    /**
     * Hien thi form khao sat loi song.
     */
    public function showSurvey()
    {
        $ds_tieu_chi = \App\Models\TrongSoThuatToan::all();
        $loiSongHienTai = Auth::user() ? Auth::user()->khao_sat_loi_song : null;
        return view('auth.lifestyle_survey', compact('ds_tieu_chi', 'loiSongHienTai'));
    }

    /**
     * Xu ly cap nhat thong tin khao sat vao JSONB.
     */
    public function updateSurvey(Request $request)
    {
        $ds_tieu_chi = \App\Models\TrongSoThuatToan::all();
        $rules = [
            'uu_tien' => 'array|max:2', // Toi da chon 2 tieu chi uu tien
            'uu_tien.*' => 'string',
            'ton_giao_loc_cung' => 'nullable',
            'van_hoa_loc_cung' => 'nullable',
        ];

        foreach($ds_tieu_chi as $tc) {
            $rules[$tc->ten_tieu_chi] = 'required';
        }

        $validatedData = $request->validate($rules);
        $validatedData['ton_giao_loc_cung'] = $request->boolean('ton_giao_loc_cung');
        $validatedData['van_hoa_loc_cung'] = $request->boolean('van_hoa_loc_cung');

        // 2. Luu vao khao_sat_loi_song (JSONB) cua user hien tai
        $user = Auth::user();
        if ($user) {
            // Vi da khai bao Cast 'array' trong Model NguoiDung,
            // Laravel tu dong ma hoa mang $validatedData thanh JSONB truoc khi luu vao CSDL.
            $user->khao_sat_loi_song = $validatedData;
            $user->save();
            
            // 3. Chuyen huong ve trang ket qua ban do goi y
            // Cập nhật: Chuyển hướng người dùng về trang Tìm bạn ở ghép sau khi khảo sát xong
            return redirect('/tim-ban-o-ghep')->with('success', 'Hồ sơ lối sống đã được tạo thành công! Hệ thống đang đề xuất các bạn ở ghép phù hợp nhất với bạn.');
        }

        // Truong hop chua dang nhap
        return redirect()->back()->withErrors('Bạn cần đăng nhập để lưu cấu hình lối sống.');
    }

    /**
     * Hiển thị trang cấu hình thông tin cá nhân
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'so_dien_thoai' => 'nullable|string|max:20',
            'gioi_tinh' => 'nullable|in:nam,nu,khac',
            'nam_sinh' => 'nullable|integer|min:1900|max:' . date('Y'),
            'nghe_nghiep' => 'nullable|string|max:255',
            'anh_dai_dien' => 'nullable|url|max:500',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Đã cập nhật thông tin cá nhân thành công!');
    }
}
