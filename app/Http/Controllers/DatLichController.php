<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LichHen;
use App\Models\PhongTro;
use Illuminate\Support\Facades\Auth;

class DatLichController extends Controller
{
    /**
     * API (AJAX): Người tìm trọ gửi yêu cầu đặt lịch hẹn
     */
    public function guiYeuCau(Request $request)
    {
        $request->validate([
            'id_phong' => 'required|exists:phong_tro,id',
            'thoi_gian_hen' => 'required|date|after:today',
        ], [
            'thoi_gian_hen.after' => 'Thời gian hẹn phải bắt đầu từ ngày mai.',
            'thoi_gian_hen.required' => 'Vui lòng chọn thời gian hẹn.'
        ]);

        $phong = PhongTro::findOrFail($request->id_phong);

        // Kiểm tra xem đã đặt lịch phòng này và đang chờ duyệt chưa
        $daDatLich = LichHen::where('id_phong', $phong->id)
                            ->where('id_nguoi_thue', Auth::id())
                            ->whereIn('trang_thai_cuoc_hen', ['cho_duyet', 'da_duyet'])
                            ->exists();

        if ($daDatLich) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã đặt lịch hẹn xem phòng này rồi. Vui lòng chờ phản hồi hoặc kiểm tra lại lịch hẹn.'
            ]);
        }

        LichHen::create([
            'id_nguoi_thue' => Auth::id(),
            'id_chu_tro' => $phong->id_chu_tro,
            'id_phong' => $phong->id,
            'thoi_gian_hen' => $request->thoi_gian_hen,
            'trang_thai_cuoc_hen' => 'cho_duyet' // Trạng thái mặc định
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gửi yêu cầu đặt lịch thành công! Chủ trọ sẽ sớm liên hệ lại với bạn.'
        ]);
    }

    /**
     * Hiển thị danh sách Lịch hẹn dành cho CHỦ TRỌ
     */
    public function danhSachLichHenChuTro()
    {
        // Lấy danh sách lịch hẹn của chủ trọ đang đăng nhập
        $danhSachLichHen = LichHen::with(['nguoiThue', 'phongTro'])
            ->where('id_chu_tro', Auth::id())
            ->orderByRaw("CASE WHEN trang_thai_cuoc_hen = 'cho_duyet' THEN 1 ELSE 2 END")
            ->orderBy('thoi_gian_hen', 'asc')
            ->paginate(10);

        return view('chu_tro.quan_ly_lich_hen', compact('danhSachLichHen'));
    }

    /**
     * API (AJAX): Chủ trọ cập nhật trạng thái lịch hẹn (Duyệt / Từ chối)
     */
    public function capNhatTrangThai(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:da_duyet,tu_choi'
        ]);

        try {
            $lichHen = LichHen::with('phongTro', 'nguoiThue')->where('id', $id)
                ->where('id_chu_tro', Auth::id())
                ->firstOrFail();

            $lichHen->trang_thai_cuoc_hen = $request->trang_thai;
            $lichHen->save();

            $message = $request->trang_thai === 'da_duyet' 
                ? 'Đã duyệt lịch hẹn thành công.' 
                : 'Đã từ chối lịch hẹn.';
                
            // Gửi thông báo cho Người thuê
            if ($lichHen->nguoiThue) {
                $thongBaoMsg = $request->trang_thai === 'da_duyet'
                    ? 'Chủ trọ đã DUYỆT lịch hẹn xem phòng "' . $lichHen->phongTro->tieu_de . '" của bạn.'
                    : 'Chủ trọ đã TỪ CHỐI lịch hẹn xem phòng "' . $lichHen->phongTro->tieu_de . '".';
                $lichHen->nguoiThue->notify(new \App\Notifications\LichHenNotification($lichHen, $thongBaoMsg, $request->trang_thai));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'trang_thai_moi' => $request->trang_thai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi! Không tìm thấy lịch hẹn hoặc bạn không có quyền.'
            ], 403);
        }
    }
}
