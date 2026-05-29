<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung;
use App\Models\TrongSoThuatToan;
use Illuminate\Support\Facades\Log;
use Exception;

class TimBanController extends Controller
{
    protected $trongSoModel;

    public function __construct(TrongSoThuatToan $trongSoModel)
    {
        $this->trongSoModel = $trongSoModel;
    }

    /**
     * API/View Endpoint: Lay danh sach gợi ý bạn ở ghép tiềm năng
     */
    public function danhSachGoiYBan(Request $request)
    {
        try {
            // Giả lập ID người dùng (hoặc lấy từ auth()->id() khi login)
            $idNguoiDung = auth()->id() ?? 4; 
            $nguoiDungHienTai = NguoiDung::with('loiMoiDaGui')->find($idNguoiDung);

            if (!$nguoiDungHienTai) {
                return response()->json(['message' => 'Người dùng không tồn tại.'], 404);
            }

            // 1. HARD FILTER: Lọc theo vai trò và Giới tính
            // Lấy danh sách những người tìm trọ khác, và BẮT BUỘC cùng giới tính
            // Cập nhật: Loại bỏ 'chu_tro' và 'admin' ra khỏi danh sách gợi ý ở ghép
            $query = NguoiDung::where('id', '!=', $idNguoiDung)
                              ->where('vai_tro', 'nguoi_tim_tro');

            // Nếu người dùng hiện tại có giới tính, thì chỉ tìm người cùng giới tính (Mặc định)
            // Cập nhật: Cho phép chọn giới tính từ bộ lọc
            $reqGioiTinh = $request->input('gioi_tinh', $nguoiDungHienTai->gioi_tinh);
            if ($reqGioiTinh && $reqGioiTinh !== 'tat_ca') {
                $query->where('gioi_tinh', $reqGioiTinh);
            }

            // Lọc theo Khoảng Tuổi (sử dụng Index trên cột nam_sinh thay vì YEAR() để tối ưu)
            $khoangTuoi = $request->input('khoang_tuoi');
            if ($khoangTuoi) {
                $namHienTai = (int)date('Y');
                if ($khoangTuoi === '18-22') {
                    $query->whereBetween('nam_sinh', [$namHienTai - 22, $namHienTai - 18]);
                } elseif ($khoangTuoi === '23-26') {
                    $query->whereBetween('nam_sinh', [$namHienTai - 26, $namHienTai - 23]);
                } elseif ($khoangTuoi === '>26') {
                    $query->where('nam_sinh', '<', $namHienTai - 26);
                }
            }

            // Lọc theo Khu vực (Thành phố)
            $thanhPho = $request->input('thanh_pho');
            if (!empty($thanhPho)) {
                $query->where('thanh_pho', 'ILIKE', '%' . $thanhPho . '%');
            }

            // --- Xử lý các tham số lọc từ UI ---
            $tuKhoa = $request->input('tu_khoa');
            if (!empty($tuKhoa)) {
                $query->where(function($q) use ($tuKhoa) {
                    $q->where('ho_ten', 'ILIKE', '%' . $tuKhoa . '%')
                      ->orWhere('nghe_nghiep', 'ILIKE', '%' . $tuKhoa . '%');
                });
            }
            // ------------------------------------

            $danhSachTiemNang = $query->get();

            // Lấy toàn bộ trọng số
            $danhSachTrongSo = $this->trongSoModel->all();

            $ketQuaGoiY = [];

            // 2. TÍNH ĐIỂM TƯƠNG ĐỒNG LỐI SỐNG (Roommate Matching Algorithm)
            foreach ($danhSachTiemNang as $nguoiTiemNang) {
                $diemSo = $this->tinhDiemMatching($nguoiDungHienTai, $nguoiTiemNang, $danhSachTrongSo);

                if ($diemSo > 0) {
                    $nguoiTiemNang->matching_score = $diemSo;
                    
                    // Tính phần trăm (Giả sử tổng điểm tối đa là 100 để hiển thị %)
                    // Tạm tính dựa trên điểm số thực tế
                    $nguoiTiemNang->matching_percentage = min(100, round($diemSo)); 
                    
                    $ketQuaGoiY[] = $nguoiTiemNang;
                }
            }

            // 3. Sắp xếp theo mức độ phù hợp giảm dần
            usort($ketQuaGoiY, function ($a, $b) {
                return $b->matching_score <=> $a->matching_score;
            });

            // Lấy danh sách lời mời đang chờ duyệt mà người này nhận được (dùng cho Panel)
            $loiMoiChoDuyet = \App\Models\LoiMoiOGhep::with('nguoiGui')
                ->where('id_nguoi_nhan', $idNguoiDung)
                ->where('trang_thai', 'cho_duyet')
                ->get();

            // Phân tích trạng thái kết nối với tất cả các user khác
            $dsLoiMoiLienQuan = \App\Models\LoiMoiOGhep::where('id_nguoi_gui', $idNguoiDung)
                                                       ->orWhere('id_nguoi_nhan', $idNguoiDung)
                                                       ->get();

            $trangThaiKetNoi = [];
            foreach ($dsLoiMoiLienQuan as $lm) {
                $otherId = $lm->id_nguoi_gui == $idNguoiDung ? $lm->id_nguoi_nhan : $lm->id_nguoi_gui;
                
                if ($lm->trang_thai == 'chap_nhan') {
                    $trangThaiKetNoi[$otherId] = 'connected';
                } elseif ($lm->trang_thai == 'cho_duyet') {
                    // Nếu chưa connected thì mới xét cho_duyet (tránh ghi đè)
                    if (!isset($trangThaiKetNoi[$otherId]) || $trangThaiKetNoi[$otherId] != 'connected') {
                        if ($lm->id_nguoi_gui == $idNguoiDung) {
                            $trangThaiKetNoi[$otherId] = 'sent';
                        } else {
                            $trangThaiKetNoi[$otherId] = 'received';
                        }
                    }
                }
            }

            return view('pro_search.roommates', [
                'nguoiDungHienTai' => $nguoiDungHienTai,
                'ds_goi_y' => $ketQuaGoiY,
                'trangThaiKetNoi' => $trangThaiKetNoi,
                'loiMoiChoDuyet' => $loiMoiChoDuyet,
            ]);

        } catch (Exception $e) {
            Log::error('Lỗi tại TimBanController@danhSachGoiYBan: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }

    /**
     * Thuật toán tính điểm tương đồng giữa Người với Người
     * [Phục vụ Luận văn: Bài toán Tìm người trước - Tìm phòng sau]
     * 
     * Cách thức hoạt động: Duyệt qua mảng JSONB khao_sat_loi_song của User A và User B.
     * Nếu trùng khớp 1 tiêu chí -> Cộng điểm (trong_so_nen)
     * Nếu tiêu chí đó nằm trong list "uu_tien" -> Nhân hệ số 1.5
     */
    private function tinhDiemMatching(NguoiDung $userA, NguoiDung $userB, $danhSachTrongSo): float
    {
        try {
            $loiSongA = $userA->khao_sat_loi_song ?? [];
            $loiSongB = $userB->khao_sat_loi_song ?? [];

            if (empty($loiSongA) || empty($loiSongB)) {
                return 10.0; // Điểm cơ bản nếu 1 trong 2 chưa làm khảo sát
            }

            // --- CƠ CHẾ RÀNG BUỘC THÍCH ỨNG (ADAPTIVE CONSTRAINTS) - HARD FILTER ---
            // 1. Giới tính: Nếu Giới tính của 2 người khác nhau hoàn toàn (không tính trường hợp Khác/Tất cả), loại bỏ hoàn toàn.
            if (!empty($userA->gioi_tinh) && !empty($userB->gioi_tinh)) {
                if ($userA->gioi_tinh !== $userB->gioi_tinh && $userA->gioi_tinh !== 'khac' && $userB->gioi_tinh !== 'khac') {
                    return 0.0; // Điểm tuyệt đối = 0 (Bị loại)
                }
            }

            // 2. Tôn giáo: Lọc cứng nếu một trong hai yêu cầu bắt buộc cùng tôn giáo và tôn giáo khác nhau
            $tonGiaoA = $loiSongA['ton_giao'] ?? null;
            $tonGiaoB = $loiSongB['ton_giao'] ?? null;
            $tonGiaoLocCungA = !empty($loiSongA['ton_giao_loc_cung']) && $loiSongA['ton_giao_loc_cung'] !== 'false';
            $tonGiaoLocCungB = !empty($loiSongB['ton_giao_loc_cung']) && $loiSongB['ton_giao_loc_cung'] !== 'false';

            if (($tonGiaoLocCungA || $tonGiaoLocCungB) && $tonGiaoA !== $tonGiaoB) {
                return 0.0; // Bị loại
            }

            // 3. Văn hóa vùng miền: Lọc cứng nếu một trong hai yêu cầu bắt buộc cùng vùng miền và vùng miền khác nhau
            $vanHoaA = $loiSongA['van_hoa'] ?? null;
            $vanHoaB = $loiSongB['van_hoa'] ?? null;
            $vanHoaLocCungA = !empty($loiSongA['van_hoa_loc_cung']) && $loiSongA['van_hoa_loc_cung'] !== 'false';
            $vanHoaLocCungB = !empty($loiSongB['van_hoa_loc_cung']) && $loiSongB['van_hoa_loc_cung'] !== 'false';

            if (($vanHoaLocCungA || $vanHoaLocCungB) && $vanHoaA !== $vanHoaB) {
                return 0.0; // Bị loại
            }

            $uuTienA = $loiSongA['uu_tien'] ?? [];
            $diemTongDatDuoc = 0.0;
            $tongDiemToiDaKhaThi = 0.0; // Biến lưu tổng điểm tối đa có thể đạt được

            foreach ($danhSachTrongSo as $tieuChi) {
                $maTieuChi = $tieuChi->ten_tieu_chi;

                // Bỏ qua nếu tiêu chí là uu_tien (vì đây là một mảng cấu hình, không phải tiêu chí so sánh)
                if ($maTieuChi === 'uu_tien') {
                    continue;
                }

                // Chỉ xét các tiêu chí mà User A có đánh giá trong bài khảo sát
                if (isset($loiSongA[$maTieuChi])) {
                    
                    // 1. CHUẨN HÓA CÔNG THỨC: Tính tổng điểm tối đa khả thi
                    // Nếu tiêu chí này nằm trong danh sách ưu tiên của User A, điểm tối đa phải nhân thêm hệ số ưu tiên
                    $diemToiDaTieuChi = $tieuChi->trong_so_nen;
                    if (in_array($maTieuChi, $uuTienA)) {
                        $diemToiDaTieuChi = $tieuChi->trong_so_nen * $tieuChi->he_so_uu_tien;
                    }
                    
                    // Cộng dồn vào mẫu số
                    $tongDiemToiDaKhaThi += $diemToiDaTieuChi;

                    if (isset($loiSongB[$maTieuChi])) {
                        // 2. ÉP KIỂU DỮ LIỆU (Loose/Casted Comparison)
                        // Ép kiểu tất cả về String để so sánh chuẩn xác, tránh lỗi "1" !== 1 trong JSON
                        $giaTriA = (string) $loiSongA[$maTieuChi];
                        $giaTriB = (string) $loiSongB[$maTieuChi];

                        if ($giaTriA === $giaTriB) {
                            // Trùng khớp hoàn toàn -> Đạt điểm tối đa của tiêu chí
                            $diemTongDatDuoc += $diemToiDaTieuChi;
                        } elseif ($tieuChi->loai_input == 'scale5') {
                            // Xử lý nới lỏng cho thang điểm 1-5 (Chênh lệch 1 bậc vẫn được tính 50% số điểm)
                            $doLech = abs((float)$giaTriA - (float)$giaTriB);
                            if ($doLech == 1) {
                                $diemTongDatDuoc += ($diemToiDaTieuChi * 0.5);
                            }
                        }
                    }
                }
            }

            // 3. TÍNH PHẦN TRĂM KHỚP (Formula Normalization)
            if ($tongDiemToiDaKhaThi == 0) {
                return 10.0; // Điểm tối thiểu nếu bài khảo sát lỗi/rỗng
            }

            // Công thức: (Tổng điểm đạt được / Tổng điểm tối đa) * 100
            $phanTramKhop = ($diemTongDatDuoc / $tongDiemToiDaKhaThi) * 100;
            
            return round($phanTramKhop, 2);

        } catch (Exception $e) {
            Log::error('Lỗi khi tính điểm ghép bạn: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * AJAX: Gửi lời mời kết nối ở ghép
     */
    public function guiLoiMoi(Request $request, $id)
    {
        try {
            $idNguoiGui = auth()->id();
            
            // Tránh tự gửi cho mình
            if ($idNguoiGui == $id) {
                return response()->json(['success' => false, 'message' => 'Không thể kết nối với chính mình.']);
            }

            // Kiểm tra xem người kia đã gửi cho mình chưa (Tránh spam 2 chiều)
            $loiMoiNguocLai = \App\Models\LoiMoiOGhep::where('id_nguoi_gui', $id)
                                                    ->where('id_nguoi_nhan', $idNguoiGui)
                                                    ->where('trang_thai', 'cho_duyet')
                                                    ->first();
            if ($loiMoiNguocLai) {
                // Người kia đã gửi rồi, giờ mình bấm Kết nối -> Tự động thành Đồng ý luôn!
                $loiMoiNguocLai->update(['trang_thai' => 'chap_nhan']);
                
                // Gửi thông báo
                $nguoiGui = NguoiDung::find($loiMoiNguocLai->id_nguoi_gui);
                $nguoiNhan = NguoiDung::find($loiMoiNguocLai->id_nguoi_nhan);
                if ($nguoiGui && $nguoiNhan) {
                    $msg = $nguoiNhan->ho_ten . ' đã CHẤP NHẬN lời mời ở ghép của bạn!';
                    $nguoiGui->notify(new \App\Notifications\LoiMoiOGhepNotification($loiMoiNguocLai, $msg, 'phan_hoi_loi_moi', 'dong_y'));
                }
                
                return response()->json(['success' => true, 'message' => 'Người này đã gửi lời mời cho bạn trước đó. Hệ thống đã tự động kết nối hai bạn!']);
            }

            // Kiểm tra xem mình đã gửi lời mời chưa
            $daTonTai = \App\Models\LoiMoiOGhep::where('id_nguoi_gui', $idNguoiGui)
                                                ->where('id_nguoi_nhan', $id)
                                                ->exists();

            if ($daTonTai) {
                return response()->json(['success' => false, 'message' => 'Lời mời đã được gửi trước đó.']);
            }

            $loiMoi = \App\Models\LoiMoiOGhep::create([
                'id_nguoi_gui' => $idNguoiGui,
                'id_nguoi_nhan' => $id,
                'trang_thai' => 'cho_duyet'
            ]);

            // Gửi thông báo cho người nhận
            $nguoiNhanMoi = NguoiDung::find($id);
            if ($nguoiNhanMoi) {
                $msg = auth()->user()->ho_ten . ' đã gửi cho bạn một lời mời kết nối ở ghép.';
                $nguoiNhanMoi->notify(new \App\Notifications\LoiMoiOGhepNotification($loiMoi, $msg, 'nhan_loi_moi'));
            }

            return response()->json(['success' => true, 'message' => 'Đã gửi lời mời kết nối thành công.']);
        } catch (\Exception $e) {
            Log::error('Lỗi gửi lời mời: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra.'], 500);
        }
    }

    /**
     * AJAX: Chấp nhận lời mời kết nối
     */
    public function chapNhanLoiMoi($id)
    {
        try {
            $loiMoi = \App\Models\LoiMoiOGhep::where('id', $id)
                        ->where('id_nguoi_nhan', auth()->id())
                        ->first();
            
            if ($loiMoi) {
                $loiMoi->update(['trang_thai' => 'chap_nhan']);
                
                // Gửi thông báo cho người gửi
                $nguoiGui = NguoiDung::find($loiMoi->id_nguoi_gui);
                if ($nguoiGui) {
                    $msg = auth()->user()->ho_ten . ' đã CHẤP NHẬN lời mời ở ghép của bạn!';
                    $nguoiGui->notify(new \App\Notifications\LoiMoiOGhepNotification($loiMoi, $msg, 'phan_hoi_loi_moi', 'dong_y'));
                }
                
                return response()->json(['success' => true, 'message' => 'Đã chấp nhận kết nối! Bạn có thể xem thông tin liên hệ của nhau.']);
            }
            return response()->json(['success' => false, 'message' => 'Không tìm thấy lời mời.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi máy chủ.'], 500);
        }
    }

    /**
     * AJAX: Từ chối lời mời kết nối
     */
    public function tuChoiLoiMoi($id)
    {
        try {
            $loiMoi = \App\Models\LoiMoiOGhep::where('id', $id)
                        ->where('id_nguoi_nhan', auth()->id())
                        ->first();
            
            if ($loiMoi) {
                $loiMoi->update(['trang_thai' => 'tu_choi']);
                
                // Gửi thông báo cho người gửi
                $nguoiGui = NguoiDung::find($loiMoi->id_nguoi_gui);
                if ($nguoiGui) {
                    $msg = auth()->user()->ho_ten . ' đã TỪ CHỐI lời mời ở ghép của bạn.';
                    $nguoiGui->notify(new \App\Notifications\LoiMoiOGhepNotification($loiMoi, $msg, 'phan_hoi_loi_moi', 'tu_choi'));
                }
                
                return response()->json(['success' => true, 'message' => 'Đã từ chối lời mời kết nối.']);
            }
            return response()->json(['success' => false, 'message' => 'Không tìm thấy lời mời.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi máy chủ.'], 500);
        }
    }
}
