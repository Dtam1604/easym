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

            // Nếu người dùng hiện tại chưa điền khảo sát lối sống
            if (empty($nguoiDungHienTai->khao_sat_loi_song)) {
                return view('pro_search.roommates', [
                    'nguoiDungHienTai' => $nguoiDungHienTai,
                    'ds_goi_y' => [],
                    'trangThaiKetNoi' => [],
                    'loiMoiChoDuyet' => collect(),
                ]);
            }

            // 1. HARD FILTER: Lọc theo vai trò và Giới tính
            // Lấy danh sách những người tìm trọ khác, và BẮT BUỘC cùng giới tính
            // Cập nhật: Loại bỏ 'chu_tro' và 'admin' ra khỏi danh sách gợi ý ở ghép
            // Đồng thời: Chỉ lấy những người đã điền khảo sát lối sống
            $query = NguoiDung::where('id', '!=', $idNguoiDung)
                              ->where('vai_tro', 'nguoi_tim_tro')
                              ->whereNotNull('khao_sat_loi_song');

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
                // Kiểm tra xem ứng viên này đã điền khảo sát chưa
                $loiSongB = $nguoiTiemNang->khao_sat_loi_song ?? [];
                if (empty($loiSongB)) {
                    continue; // Bỏ qua nếu họ chưa điền khảo sát
                }

                // Lọc cứng: Chỉ gợi ý những người có khả năng kết nối ở ghép (cùng khu vực khả thi)
                if (!$this->canConnect($idNguoiDung, $nguoiTiemNang->id)) {
                    continue;
                }

                $diemSo = $this->tinhDiemMatching($nguoiDungHienTai, $nguoiTiemNang, $danhSachTrongSo, $reqGioiTinh);

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

            foreach ($loiMoiChoDuyet as $lm) {
                if ($lm->nguoiGui) {
                    $diemSo = $this->tinhDiemMatching($nguoiDungHienTai, $lm->nguoiGui, $danhSachTrongSo, $reqGioiTinh);
                    $lm->nguoiGui->matching_percentage = min(100, round($diemSo));
                }
            }

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
    private function tinhDiemMatching(NguoiDung $userA, NguoiDung $userB, $danhSachTrongSo, $reqGioiTinh = null): float
    {
        try {
            $loiSongA = $userA->khao_sat_loi_song ?? [];
            $loiSongB = $userB->khao_sat_loi_song ?? [];

            if (empty($loiSongA) || empty($loiSongB)) {
                return 0.0; // Không hiển thị nếu chưa làm khảo sát
            }

            // --- CƠ CHẾ RÀNG BUỘC THÍCH ỨNG (ADAPTIVE CONSTRAINTS) - HARD FILTER ---
            // 1. Giới tính: Nếu Giới tính của 2 người khác nhau hoàn toàn (không tính trường hợp Khác/Tất cả), loại bỏ hoàn toàn.
            // Chỉ áp dụng lọc cứng nếu người dùng không chọn xem 'Tất cả' hoặc không chọn tìm giới tính khác.
            if ($reqGioiTinh === null || $reqGioiTinh === $userA->gioi_tinh) {
                if (!empty($userA->gioi_tinh) && !empty($userB->gioi_tinh)) {
                    if ($userA->gioi_tinh !== $userB->gioi_tinh && $userA->gioi_tinh !== 'khac' && $userB->gioi_tinh !== 'khac') {
                        return 0.0; // Điểm tuyệt đối = 0 (Bị loại)
                    }
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

            // 4. Địa điểm ở ghép (Lọc cứng): Hai người phải có ít nhất 1 khu vực chung muốn ở ghép
            $locTermsA = $userA->dia_diem_nhiem_ky ?? $loiSongA['dia_diem_nhiem_ky'] ?? [['dia_diem' => $userA->thanh_pho ?? 'Hà Nội', 'nhiem_ky' => 12]];
            $locTermsB = $userB->dia_diem_nhiem_ky ?? $loiSongB['dia_diem_nhiem_ky'] ?? [['dia_diem' => $userB->thanh_pho ?? 'Hà Nội', 'nhiem_ky' => 12]];

            $locationsA = [];
            foreach ($locTermsA as $item) {
                if (!empty($item['dia_diem'])) {
                    $locationsA[mb_strtolower(trim($item['dia_diem']))] = (int)($item['nhiem_ky'] ?? 6);
                }
            }
            $locationsB = [];
            foreach ($locTermsB as $item) {
                if (!empty($item['dia_diem'])) {
                    $locationsB[mb_strtolower(trim($item['dia_diem']))] = (int)($item['nhiem_ky'] ?? 6);
                }
            }

            $commonLocations = array_intersect_key($locationsA, $locationsB);
            if (empty($commonLocations)) {
                return 0.0; // Bị loại vì không cùng khu vực ở ghép khả thi
            }

            // Tính điểm độ tương đồng nhiệm kỳ (lease tenure) lớn nhất tại địa điểm chung
            $maxTermSim = 0.0;
            foreach ($commonLocations as $loc => $termA) {
                $termB = $locationsB[$loc];
                $maxVal = max($termA, $termB);
                $sim = $maxVal > 0 ? (1 - abs($termA - $termB) / $maxVal) : 1.0;
                if ($sim > $maxTermSim) {
                    $maxTermSim = $sim;
                }
            }

            // Tính tương đồng Tiền thuê (Price budget similarity)
            $tienThueA = $userA->tien_thue ?? $loiSongA['tien_thue'] ?? 2000000;
            $tienThueB = $userB->tien_thue ?? $loiSongB['tien_thue'] ?? 2000000;
            $maxPrice = max($tienThueA, $tienThueB);
            $simPrice = $maxPrice > 0 ? (1 - abs($tienThueA - $tienThueB) / $maxPrice) : 1.0;
            $simPrice = max(0.0, $simPrice);

            // Tính tương đồng Số người ở ghép tối đa (Max roommates similarity)
            $soNguoiA = $userA->so_nguoi_to_da ?? $loiSongA['so_nguoi_to_da'] ?? 2;
            $soNguoiB = $userB->so_nguoi_to_da ?? $loiSongB['so_nguoi_to_da'] ?? 2;
            $maxPeople = max($soNguoiA, $soNguoiB);
            $simPeople = $maxPeople > 0 ? (1 - abs($soNguoiA - $soNguoiB) / $maxPeople) : 1.0;
            $simPeople = max(0.0, $simPeople);

            // Tính tương đồng Cơ sở vật chất (Facilities similarity - Jaccard index)
            $csvcA = $userA->co_so_vat_chat ?? $loiSongA['co_so_vat_chat'] ?? [];
            $csvcB = $userB->co_so_vat_chat ?? $loiSongB['co_so_vat_chat'] ?? [];
            if (empty($csvcA) && empty($csvcB)) {
                $simFacilities = 1.0;
            } else {
                $intersect = count(array_intersect($csvcA, $csvcB));
                $union = count(array_unique(array_merge($csvcA, $csvcB)));
                $simFacilities = $union > 0 ? ($intersect / $union) : 1.0;
            }

            // 5. Tính điểm độ phù hợp lối sống (Original Lifestyle Score)
            $uuTienA = $loiSongA['uu_tien'] ?? [];
            $diemTongDatDuoc = 0.0;
            $tongDiemToiDaKhaThi = 0.0;

            foreach ($danhSachTrongSo as $tieuChi) {
                $maTieuChi = $tieuChi->ten_tieu_chi;

                if (in_array($maTieuChi, ['uu_tien', 'tien_thue', 'so_nguoi_to_da', 'co_so_vat_chat', 'dia_diem_nhiem_ky'])) {
                    continue;
                }

                if (isset($loiSongA[$maTieuChi])) {
                    $diemToiDaTieuChi = $tieuChi->trong_so_nen;
                    if (in_array($maTieuChi, $uuTienA)) {
                        $diemToiDaTieuChi = $tieuChi->trong_so_nen * $tieuChi->he_so_uu_tien;
                    }
                    
                    $tongDiemToiDaKhaThi += $diemToiDaTieuChi;

                    if (isset($loiSongB[$maTieuChi])) {
                        $giaTriA = (string) $loiSongA[$maTieuChi];
                        $giaTriB = (string) $loiSongB[$maTieuChi];

                        if ($giaTriA === $giaTriB) {
                            $diemTongDatDuoc += $diemToiDaTieuChi;
                        } elseif ($tieuChi->loai_input == 'scale5') {
                            $doLech = abs((float)$giaTriA - (float)$giaTriB);
                            if ($doLech == 1) {
                                $diemTongDatDuoc += ($diemToiDaTieuChi * 0.5);
                            }
                        }
                    }
                }
            }

            $phanTramKhopLifestyle = $tongDiemToiDaKhaThi > 0 ? ($diemTongDatDuoc / $tongDiemToiDaKhaThi) * 100 : 10.0;

            // 6. Tổng hợp điểm matching tích hợp mới (Integrated Matching Score)
            // Trọng số: Lối sống 40%, Vị trí & Nhiệm kỳ 20%, Giá phòng 20%, Số người tối đa 10%, Tiện nghi 10%
            $finalScore = 0.40 * $phanTramKhopLifestyle 
                        + 0.20 * ($maxTermSim * 100) 
                        + 0.20 * ($simPrice * 100) 
                        + 0.10 * ($simPeople * 100) 
                        + 0.10 * ($simFacilities * 100);

            return round($finalScore, 2);

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

            // Kiểm tra ràng buộc địa điểm ở ghép
            if (!$this->canConnect($idNguoiGui, $id)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Không thể kết nối: Hai bạn không cùng khu vực ở ghép khả thi, hoặc đã ở ghép với người khác tại địa điểm khác.'
                ]);
            }

            // Kiểm tra xem người kia đã gửi cho mình chưa (Tránh spam 2 chiều)
            $loiMoiNguocLai = \App\Models\LoiMoiOGhep::where('id', $id)
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
                // Kiểm tra ràng buộc địa điểm ở ghép trước khi chấp nhận
                if (!$this->canConnect($loiMoi->id_nguoi_gui, $loiMoi->id_nguoi_nhan)) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Không thể chấp nhận: Hai bạn không cùng khu vực ở ghép khả thi, hoặc đã ở ghép với người khác tại địa điểm khác.'
                    ]);
                }

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

    /**
     * AJAX: Hủy kết nối bạn cùng phòng
     */
    public function huyKetNoi($id)
    {
        try {
            $idNguoiDung = auth()->id();
            
            // Tìm lời mời kết nối giữa 2 người có trạng thái chap_nhan
            $loiMoi = \App\Models\LoiMoiOGhep::where(function($q) use ($idNguoiDung, $id) {
                $q->where('id_nguoi_gui', $idNguoiDung)->where('id_nguoi_nhan', $id);
            })->orWhere(function($q) use ($idNguoiDung, $id) {
                $q->where('id_nguoi_gui', $id)->where('id_nguoi_nhan', $idNguoiDung);
            })
            ->where('trang_thai', 'chap_nhan')
            ->first();

            if ($loiMoi) {
                // Gửi thông báo cho người kia trước khi xóa
                $otherId = $loiMoi->id_nguoi_gui == $idNguoiDung ? $loiMoi->id_nguoi_nhan : $loiMoi->id_nguoi_gui;
                $nguoiKia = NguoiDung::find($otherId);
                if ($nguoiKia) {
                    $msg = auth()->user()->ho_ten . ' đã hủy kết nối bạn cùng phòng với bạn.';
                    $nguoiKia->notify(new \App\Notifications\LoiMoiOGhepNotification($loiMoi, $msg, 'huy_ket_noi'));
                }

                $loiMoi->delete(); // Xóa hoàn toàn để reset trạng thái kết nối
                
                return response()->json(['success' => true, 'message' => 'Đã hủy kết nối bạn cùng phòng thành công.']);
            }
            return response()->json(['success' => false, 'message' => 'Không tìm thấy kết nối bạn cùng phòng giữa hai người.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi máy chủ.'], 500);
        }
    }

    /**
     * Lấy danh sách ID các thành viên trong nhóm ở ghép của người dùng (dùng thuật toán duyệt đồ thị BFS).
     */
    private function getRoommateGroupMembers($userId)
    {
        $group = [$userId];
        $queue = [$userId];
        
        while (!empty($queue)) {
            $currentId = array_shift($queue);
            
            $connections = \App\Models\LoiMoiOGhep::where('trang_thai', 'chap_nhan')
                ->where(function($q) use ($currentId) {
                    $q->where('id_nguoi_gui', $currentId)
                      ->orWhere('id_nguoi_nhan', $currentId);
                })
                ->get()
                ->map(function($lm) use ($currentId) {
                    return $lm->id_nguoi_gui == $currentId ? $lm->id_nguoi_nhan : $lm->id_nguoi_gui;
                })
                ->toArray();
                
            foreach ($connections as $connId) {
                if (!in_array($connId, $group)) {
                    $group[] = $connId;
                    $queue[] = $connId;
                }
            }
        }
        
        return $group;
    }

    /**
     * Lấy các địa điểm ở ghép hiện tại/khả thi chung của nhóm bạn ở ghép.
     */
    private function getUserActiveLocations($userId)
    {
        $groupMembers = $this->getRoommateGroupMembers($userId);
        $commonIntersection = null;
        
        foreach ($groupMembers as $memberId) {
            $user = \App\Models\NguoiDung::find($memberId);
            if (!$user) {
                continue;
            }
            
            $userLocs = collect($user->dia_diem_nhiem_ky ?? $user->khao_sat_loi_song['dia_diem_nhiem_ky'] ?? [])
                ->pluck('dia_diem')
                ->map(fn($l) => trim(mb_strtolower($l)))
                ->filter()
                ->toArray();
                
            if (empty($userLocs) && !empty($user->thanh_pho)) {
                $userLocs = [trim(mb_strtolower($user->thanh_pho))];
            }
            
            if ($commonIntersection === null) {
                $commonIntersection = $userLocs;
            } else {
                $commonIntersection = array_intersect($commonIntersection, $userLocs);
            }
        }
        
        return $commonIntersection ? array_values($commonIntersection) : [];
    }

    /**
     * Lấy sức chứa tối đa của nhóm ở ghép (là giá trị nhỏ nhất trong số các so_nguoi_to_da của từng thành viên).
     */
    private function getRoommateGroupCapacity($memberIds)
    {
        $minCapacity = 999;
        foreach ($memberIds as $id) {
            $user = \App\Models\NguoiDung::find($id);
            if ($user) {
                $cap = $user->so_nguoi_to_da ?? ($user->khao_sat_loi_song['so_nguoi_to_da'] ?? 2);
                if ($cap < $minCapacity) {
                    $minCapacity = $cap;
                }
            }
        }
        return $minCapacity;
    }

    /**
     * Kiểm tra xem 2 người dùng có thể kết nối ở ghép với nhau hay không.
     */
    private function canConnect($userAId, $userBId)
    {
        // Lấy danh sách thành viên của 2 nhóm hiện tại
        $groupA = $this->getRoommateGroupMembers($userAId);
        $groupB = $this->getRoommateGroupMembers($userBId);

        // Nếu đã cùng nhóm, không cần kết nối nữa
        if (in_array($userBId, $groupA)) {
            return false;
        }

        // Tính toán nhóm sau khi sát nhập
        $mergedGroup = array_unique(array_merge($groupA, $groupB));
        $mergedSize = count($mergedGroup);

        // Tính giới hạn tối đa của nhóm sau khi sát nhập
        $mergedCapacity = $this->getRoommateGroupCapacity($mergedGroup);

        // Nếu số người sau khi sát nhập vượt quá giới hạn tối đa của bất kỳ thành viên nào, không cho phép ghép
        if ($mergedSize > $mergedCapacity) {
            return false;
        }

        // Kiểm tra tính tương thích về khu vực ở ghép
        $locsA = $this->getUserActiveLocations($userAId);
        $locsB = $this->getUserActiveLocations($userBId);

        if (empty($locsA) || empty($locsB)) {
            return false;
        }

        $intersection = array_intersect($locsA, $locsB);
        return !empty($intersection);
    }
}
