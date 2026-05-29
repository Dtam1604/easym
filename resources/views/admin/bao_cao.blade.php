@extends('layouts.app')

@section('title', 'Quản lý Báo cáo Vi phạm')

@section('content')
<div class="min-h-screen bg-slate-50 p-4 sm:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-blue-600 mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Quay lại Dashboard</a>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <i class="fa-solid fa-flag text-red-600"></i>
                    Báo cáo Vi phạm
                </h1>
                <p class="text-gray-500 mt-2">Quản lý các báo cáo từ người dùng về tin đăng lừa đảo, sai thông tin.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                            <th class="p-4 font-bold">Ngày gửi</th>
                            <th class="p-4 font-bold">Người báo cáo</th>
                            <th class="p-4 font-bold">Phòng bị báo cáo</th>
                            <th class="p-4 font-bold">Lý do & Chi tiết</th>
                            <th class="p-4 font-bold">Trạng thái</th>
                            <th class="p-4 font-bold">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($baocaos as $bc)
                            <tr class="hover:bg-gray-50/50 transition-colors">
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mb-1">
                                        {{ $bc->ly_do }}
                                    </span>
                                    @if($bc->chi_tiet)
                                        <p class="text-gray-600 text-xs mt-1 italic">"{{ $bc->chi_tiet }}"</p>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($bc->trang_thai == 'chua_xu_ly')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-red-50 text-red-600 border border-red-200">
                                            <i class="fa-solid fa-circle-exclamation mr-1.5"></i> Chưa xử lý
                                        </span>
                                    @elseif($bc->trang_thai == 'dang_xem_xet')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">
                                            <i class="fa-solid fa-hourglass-half mr-1.5"></i> Đang xem xét
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200">
                                            <i class="fa-solid fa-check mr-1.5"></i> Đã giải quyết
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($bc->trang_thai != 'da_giai_quyet')
                                        <form action="{{ route('admin.baocao.xuly', $bc->id) }}" method="POST" class="flex flex-col gap-2">
                                            @csrf
                                            <select name="hanh_dong" class="text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500" required>
                                                <option value="">-- Chọn xử lý --</option>
                                                @if($bc->trang_thai == 'chua_xu_ly')
                                                    <option value="dang_xem_xet">Đánh dấu Đang xem xét</option>
                                                @endif
                                                <option value="da_giai_quyet">Đánh dấu Đã giải quyết (Không vi phạm)</option>
                                                <option value="xoa_phong">Xóa phòng vi phạm này</option>
                                            </select>
                                            <button type="submit" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow transition-colors">
                                                Cập nhật
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
