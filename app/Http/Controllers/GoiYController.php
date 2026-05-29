<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung;
use App\Models\PhongTro;
use App\Models\TrongSoThuatToan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class GoiYController extends Controller
{
    /**
     * @var TrongSoThuatToan
     */
    protected $trongSoModel;

    /**
     * Tiem phu thuoc (Dependency Injection) de truy xuat bang trong_so_thuat_toan.
     */
    public function __construct(TrongSoThuatToan $trongSoModel)
    {
        $this->trongSoModel = $trongSoModel;
    }

    /**
     * API Endpoint: Lay danh sach phong tro quanh vi tri va goi y ghep doi
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // 1. Lay toa do va ban kinh tu request (Mac dinh: Dai hoc Lam Nghiep, ban kinh 2km)
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            $ban_kinh = $request->input('ban_kinh', 2000); // Tinh bang met

            // Tham so Bo loc (Filters)
            $gia_min = $request->input('gia_min');
            $gia_max = $request->input('gia_max');
            $dien_tich_min = $request->input('dien_tich_min');
            $dien_tich_max = $request->input('dien_tich_max');
            $chi_xac_thuc = $request->boolean('chi_xac_thuc', false);
            $gioi_tinh = $request->input('gioi_tinh');

            // Gia lap: Lay ID nguoi dang dang nhap (Nguoi tim tro)
            $id_nguoi_dung = auth()->id() ?? 4; // Gia su user 4 la Pham Sinh Vien
            $nguoiTimTro = NguoiDung::find($id_nguoi_dung);

            if (!$nguoiTimTro) {
                return response()->json(['message' => 'Nguoi dung khong ton tai.'], 404);
            }

            // 2. Truy van PostGIS loc ban kinh & Eager Loading de chong N+1
            // Ap dung Local Scopes de loc du lieu ngay tu Database
            $danhSachPhong = PhongTro::with('chuTro')
                ->select('*', DB::raw('ST_Y(vi_tri::geometry) as lat'), DB::raw('ST_X(vi_tri::geometry) as lng'))
                ->when($lat && $lng, function ($query) use ($lat, $lng, $ban_kinh) {
                    return $query->whereRaw(
                        "ST_DistanceSphere(vi_tri, ST_SetSRID(ST_MakePoint(?, ?), 4326)) <= ?", 
                        [$lng, $lat, $ban_kinh]
                    );
                })
                // Chi lay phong chua thue (trang_thai_thue = 1) va muc do xac thuc bat ky (>= 0)
                ->where('muc_do_xac_thuc', '>=', 0)
                ->where('trang_thai_thue', 1)
                // Filter Scopes
                ->khoangGia($gia_min, $gia_max)
                ->khoangDienTich($dien_tich_min, $dien_tich_max)
                ->xacThuc($chi_xac_thuc)
                ->gioiTinh($gioi_tinh)
                ->get();

            // Thay vi tinh diem matching, ta chi sap xep theo muc_do_xac_thuc (uu tien phong da xac thuc)
            // va thoi gian tao (uu tien phong moi)
            $ketQuaGoiY = $danhSachPhong->sortByDesc(function ($phong) {
                return $phong->muc_do_xac_thuc * 1000 + $phong->id; // Giam bot query
            })->values()->all();

            // Nếu là AJAX (từ chức năng Lọc), trả về JSON
            if ($request->ajax()) {
                // Render view danh sách phòng thành chuỗi HTML để trả về
                $html = view('pro_search.partials.room_list', ['ds_goi_y' => $ketQuaGoiY])->render();
                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'data' => $ketQuaGoiY,
                ]);
            }

            // Trả về View cho lần load đầu tiên
            $ds_goi_y = $ketQuaGoiY;
            return view('pro_search.results', compact('ds_goi_y'));

        } catch (Exception $e) {
            // Bắt lỗi an toàn, tránh trang web bị Crash màn hình trắng
            Log::error('Loi tai GoiYController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Co loi xay ra he thong.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal Server Error'
            ], 500);
        }
    }

    /**
     * Hien thi trang chi tiet phong tro
     */
    public function show($id)
    {
        $phong = PhongTro::with('chuTro')->findOrFail($id);
        
        return view('pro_search.detail', compact('phong'));
    }

    /**
     * API Endpoint: Ghi nhận báo cáo vi phạm phòng trọ
     */
    public function baoCaoPhong(Request $request, $id)
    {
        $request->validate([
            'ly_do' => 'required|string|max:255',
            'chi_tiet' => 'nullable|string|max:1000',
        ]);

        $phong = PhongTro::findOrFail($id);

        \App\Models\BaoCaoPhong::create([
            'id_nguoi_bao_cao' => auth()->id(),
            'id_phong' => $phong->id,
            'ly_do' => $request->ly_do,
            'chi_tiet' => $request->chi_tiet,
            'trang_thai' => 'chua_xu_ly'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã báo cáo. Chúng tôi sẽ xử lý trong thời gian sớm nhất!'
        ]);
    }
}
