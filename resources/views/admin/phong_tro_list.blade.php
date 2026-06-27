@extends('layouts.app')

@section('title', 'Quản lý Tất cả Phòng trọ - Admin EasyM')

@section('content')
<div class="flex min-h-[calc(100dvh-72px)] ops-page font-sans">

    <!-- SIDEBAR -->
    <aside class="w-64 sidebar-dark ops-sidebar hidden md:flex flex-col z-20">
        <div class="h-16 flex items-center justify-center border-b border-slate-800">
            <h1 class="ops-brand text-2xl">
                EasyM <span>ADMIN</span>
            </h1>
        </div>
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-chart-pie w-6"></i>
                        <span class="font-medium text-sm">Tổng quan & Thống kê</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.dashboard') }}#weights-config" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-sliders w-6"></i>
                        <span class="font-medium text-sm">Cấu hình Thuật toán</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tieuchi.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-layer-group w-6"></i>
                        <span class="font-medium text-sm">Thêm Tiêu chí mới</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kyc.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-id-card-clip w-6"></i>
                        <span class="font-medium text-sm">Duyệt Hồ sơ KYC</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.baocao.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-flag w-6 text-red-400"></i>
                        <span class="font-medium text-sm text-red-100">Báo cáo vi phạm</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.phongtro.index') }}" class="sidebar-item active flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-building w-6"></i>
                        <span class="font-medium text-sm">Quản lý Tất cả phòng trọ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.nguoidung.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-users w-6"></i>
                        <span class="font-medium text-sm">Quản lý Tài khoản</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.ctv.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-user-gear w-6"></i>
                        <span class="font-medium text-sm">Quản lý CTV (UC19)</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col ops-main">
        <!-- Header -->
        <header class="ops-header flex items-center justify-between px-6 lg:px-8 z-10 sticky top-0">
            <div class="flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="ops-action-secondary min-h-0 w-10 h-10 p-0 mr-4" aria-label="Quay lại dashboard">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <p class="ops-kicker">Kho tin đăng</p>
                    <h2 class="text-xl font-black text-gray-900">Quản lý phòng trọ</h2>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-600">Xin chào, <span class="font-bold text-blue-600">Admin Tâm</span></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=1769E0&color=fff" class="w-9 h-9 rounded-full border-2 border-blue-100">
            </div>
        </header>

        <div class="p-8 space-y-6 max-w-7xl mx-auto w-full">
            @if(session('success'))
                <div class="ops-card bg-emerald-50 border-emerald-200 p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-check text-emerald-500 mr-3"></i>
                        <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="ops-card bg-red-50 border-red-200 p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-xmark text-red-500 mr-3"></i>
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="ops-card overflow-hidden">
                <div class="ops-card-header p-5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <div>
                        <p class="ops-kicker">Danh sách</p>
                        <h3 class="font-black text-gray-900 text-lg mt-1"><i class="fa-solid fa-list mr-2 text-blue-500"></i> Phòng trọ hiện có</h3>
                    </div>
                    <span class="ops-badge ops-badge-blue">{{ $phongTros->total() }} phòng</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="ops-table">
                        <thead>
                            <tr>
                                <th class="p-4 text-left">ID</th>
                                <th class="p-4 text-left">Phòng trọ / địa chỉ</th>
                                <th class="p-4 text-left">Giá & diện tích</th>
                                <th class="p-4 text-left">Chủ trọ</th>
                                <th class="p-4 text-center">Trạng thái</th>
                                <th class="p-4 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($phongTros as $phong)
                            <tr>
                                <td class="p-4 align-top">
                                    <span class="text-gray-500 font-bold text-sm">#{{ $phong->id }}</span>
                                </td>
                                <td class="p-4 align-top max-w-xs">
                                    <div class="flex gap-3">
                                        <div class="w-16 h-16 rounded-xl bg-gray-200 flex-shrink-0 overflow-hidden border border-gray-200">
                                            @if(!empty($phong->anh_phong) && is_array($phong->anh_phong) && isset($phong->anh_phong[0]))
                                                <img src="{{ $phong->anh_phong[0] }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fa-solid fa-image"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="/phong-tro/{{ $phong->id }}" target="_blank" class="font-bold text-blue-600 hover:underline line-clamp-2 leading-tight mb-1">{{ $phong->tieu_de }}</a>
                                            <p class="text-xs text-gray-500 line-clamp-1"><i class="fa-solid fa-location-dot text-red-400 mr-1"></i> {{ $phong->dia_chi_chi_tiet }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 align-top whitespace-nowrap">
                                    <div class="font-black text-red-600">{{ number_format($phong->gia_phong, 0, ',', '.') }} đ</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $phong->dien_tich }} m²</div>
                                </td>
                                <td class="p-4 align-top">
                                    <div class="font-medium text-gray-900 flex items-center gap-1">
                                        {{ $phong->chuTro->ho_ten ?? 'N/A' }}
                                        @if(isset($phong->chuTro) && $phong->chuTro->da_xac_thuc_cccd)
                                            <i class="fa-solid fa-circle-check text-blue-500 text-[10px]" title="Đã xác thực"></i>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $phong->chuTro->email ?? '' }}</div>
                                </td>
                                <td class="p-4 align-top text-center">
                                    @if($phong->trang_thai_thue == 1 || $phong->trang_thai_thue === 'con_trong')
                                        <span class="ops-badge ops-badge-green py-1">
                                            Còn trống
                                        </span>
                                    @else
                                        <span class="ops-badge py-1">
                                            Đã cho thuê
                                        </span>
                                    @endif
                                    
                                    <div class="mt-2">
                                        @if($phong->muc_do_xac_thuc == 2)
                                             <span class="ops-badge ops-badge-blue py-1 text-[10px] uppercase"><i class="fa-solid fa-shield-halved mr-1"></i> Đã XT</span>
                                        @elseif($phong->muc_do_xac_thuc == 1)
                                             <span class="ops-badge ops-badge-amber py-1 text-[10px] uppercase">Chờ duyệt</span>
                                        @else
                                             <span class="ops-badge py-1 text-[10px] uppercase">Chưa XT</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 align-top text-center">
                                    <div class="flex justify-center gap-2 flex-col">
                                        <a href="/phong-tro/{{ $phong->id }}" target="_blank" class="ops-action-secondary min-h-0 py-1.5 text-xs">
                                            <i class="fa-solid fa-eye mr-1"></i> Xem
                                        </a>
                                        <form action="{{ route('admin.phongtro.destroy', $phong->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng trọ này vĩnh viễn khỏi hệ thống không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-full text-xs font-bold transition-colors border border-red-200 hover:border-red-600">
                                                <i class="fa-solid fa-trash-can mr-1"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">
                                    <div class="text-4xl text-gray-300 mb-3"><i class="fa-solid fa-box-open"></i></div>
                                    <p>Chưa có phòng trọ nào trong hệ thống.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t border-gray-200 bg-white">
                    {{ $phongTros->links() }}
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
