@extends('layouts.app')

@section('title', 'Quản lý Báo cáo Vi phạm')

@section('content')
<div class="ops-page p-4 sm:p-8">
    <div class="ops-shell space-y-6">
        <div class="ops-card overflow-hidden">
            <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="ops-action-secondary min-h-0 py-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        Quay lại dashboard
                    </a>
                    <div>
                        <p class="ops-kicker">Kiểm soát rủi ro</p>
                        <h1 class="ops-title text-2xl sm:text-3xl mt-1 flex items-center gap-3">
                            <i class="fa-solid fa-flag text-red-600"></i>
                            Báo cáo vi phạm
                        </h1>
                        <p class="text-gray-500 mt-2">Quản lý các báo cáo từ người dùng về tin đăng lừa đảo hoặc sai thông tin.</p>
                    </div>
                </div>
                <div class="ops-badge ops-badge-red">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ $baocaos->count() }} báo cáo
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="ops-card p-4 bg-emerald-50 text-emerald-700 border-emerald-200 font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="ops-card overflow-hidden">
            <div class="ops-card-header p-5">
                <p class="ops-kicker">Danh sách xử lý</p>
                <h2 class="text-lg font-black text-gray-900 mt-1">Báo cáo từ người dùng</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="ops-table">
                    <thead>
                        <tr>
                            <th class="p-4 text-left">Ngày gửi</th>
                            <th class="p-4 text-left">Người báo cáo</th>
                            <th class="p-4 text-left">Phòng bị báo cáo</th>
                            <th class="p-4 text-left">Lý do & chi tiết</th>
                            <th class="p-4 text-left">Trạng thái</th>
                            <th class="p-4 text-left">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($baocaos as $bc)
                            <tr>
                                <td class="p-4 text-gray-500 whitespace-nowrap">
                                    {{ $bc->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $bc->nguoiBaoCao->ho_ten ?? 'Không rõ' }}</div>
                                    <div class="text-xs text-gray-500">{{ $bc->nguoiBaoCao->email ?? '' }}</div>
                                </td>
                                <td class="p-4">
                                    @if($bc->phong)
                                        <a href="{{ route('room.show', $bc->id_phong) }}" target="_blank" class="font-bold text-blue-600 hover:underline line-clamp-1">
                                            {{ $bc->phong->tieu_de }}
                                        </a>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Chủ trọ: {{ $bc->phong->chuTro->ho_ten ?? 'Không rõ' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Phòng đã bị xóa</span>
                                    @endif
                                </td>
                                <td class="p-4 min-w-[200px]">
                                    <span class="ops-badge ops-badge-red py-1 mb-1">
                                        {{ $bc->ly_do }}
                                    </span>
                                    @if($bc->chi_tiet)
                                        <p class="text-gray-600 text-xs mt-1 italic max-w-xs">"{{ $bc->chi_tiet }}"</p>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($bc->trang_thai == 'chua_xu_ly')
                                        <span class="ops-badge ops-badge-red">
                                            <i class="fa-solid fa-circle-exclamation mr-1.5"></i> Chưa xử lý
                                        </span>
                                    @elseif($bc->trang_thai == 'dang_xem_xet')
                                        <span class="ops-badge ops-badge-amber">
                                            <i class="fa-solid fa-hourglass-half mr-1.5"></i> Đang xem xét
                                        </span>
                                    @else
                                        <span class="ops-badge ops-badge-green">
                                            <i class="fa-solid fa-check mr-1.5"></i> Đã giải quyết
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($bc->trang_thai != 'da_giai_quyet')
                                        <form action="{{ route('admin.baocao.xuly', $bc->id) }}" method="POST" class="flex flex-col gap-2">
                                            @csrf
                                            <select name="hanh_dong" class="text-xs border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 min-w-48" required>
                                                <option value="">-- Chọn xử lý --</option>
                                                @if($bc->trang_thai == 'chua_xu_ly')
                                                    <option value="dang_xem_xet">Đánh dấu Đang xem xét</option>
                                                @endif
                                                <option value="da_giai_quyet">Đánh dấu Đã giải quyết (Không vi phạm)</option>
                                                <option value="xoa_phong">Xóa phòng vi phạm này</option>
                                            </select>
                                            <button type="submit" class="ops-action-primary min-h-0 py-1.5 text-xs">
                                                <i class="fa-solid fa-floppy-disk"></i> Cập nhật
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Hoàn tất</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-clipboard-check text-4xl text-gray-300 mb-3"></i>
                                        <p>Tuyệt vời! Hiện không có báo cáo vi phạm nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
