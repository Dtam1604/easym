<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class ThongBaoController extends Controller
{
    /**
     * Liệt kê tất cả thông báo
     */
    public function index()
    {
        $thongBaos = Auth::user()->notifications()->paginate(20);
        return view('thong_bao.index', compact('thongBaos'));
    }

    /**
     * Đánh dấu 1 thông báo đã đọc và chuyển hướng
     */
    public function docThongBao($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Đánh dấu đã đọc
        $notification->markAsRead();

        // Lấy URL từ data để chuyển hướng
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }

        return redirect()->back();
    }

    /**
     * Đánh dấu tất cả đã đọc
     */
    public function docTatCa()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
