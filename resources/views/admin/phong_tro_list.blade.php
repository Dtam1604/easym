@extends('layouts.app')

@section('title', 'Quản lý Tất cả Phòng trọ - Admin EasyM')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .sidebar-dark { background-color: #0f172a; color: #f8fafc; }
    .sidebar-item:hover { background-color: #1e293b; color: #38bdf8; }
    .sidebar-item.active { background-color: #1e293b; border-left: 4px solid #38bdf8; color: #38bdf8; }
</style>
@endpush

@section('content')
<div class="flex h-screen bg-gray-50 font-sans">

    <!-- SIDEBAR -->
    <aside class="w-64 sidebar-dark hidden md:flex flex-col shadow-2xl z-20">
        <div class="h-16 flex items-center justify-center border-b border-slate-800">
            <h1 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-sky-300 tracking-wider">
                EasyM <span class="font-light text-slate-300 text-sm">ADMIN</span>
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
            </ul>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col overflow-hidden h-screen overflow-y-auto scroll-smooth">
        <!-- Header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10 sticky top-0">
            <div class="flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-blue-600 mr-4">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-800">Quản lý Tất cả Phòng trọ</h2>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-600">Xin chào, <span class="font-bold text-blue-600">Admin Tâm</span></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="w-8 h-8 rounded-full border-2 border-blue-200">
            </div>
        </header>

        <div class="p-8 space-y-6 max-w-7xl mx-auto w-full">
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded shadow-sm">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-check text-emerald-500 mr-3"></i>
                        <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-xmark text-red-500 mr-3"></i>
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-lg"><i class="fa-solid fa-list mr-2 text-blue-500"></i> Danh sách phòng trọ hiện có</h3>
                    <span class="text-sm text-gray-500">Tổng cộng: <span class="font-bold text-blue-600">{{ $phongTros->total() }}</span> phòng</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                                <th class="p-4 border-b font-bold">ID</th>
                                <th class="p-4 border-b font-bold">Phòng trọ / Địa chỉ</th>
                                <th class="p-4 border-b font-bold">Giá & Diện tích</th>
                                <th class="p-4 border-b font-bold">Chủ trọ</th>
                                <th class="p-4 border-b font-bold text-center">Trạng thái</th>
                                <th class="p-4 border-b font-bold text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($phongTros as $phong)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="p-4 align-top">
                                    <span class="text-gray-500 font-bold text-sm">#{{ $phong->id }}</span>
                                </td>
                                <td class="p-4 align-top max-w-xs">
                                    <div class="flex gap-3">
                                        <div class="w-16 h-16 rounded bg-gray-200 flex-shrink-0 overflow-hidden border border-gray-300">
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
                                    <div class="font-bold text-red-600">{{ number_format($phong->gia_phong, 0, ',', '.') }} đ</div>
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
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            Còn trống
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-600 border border-gray-300">
                                            Đã cho thuê
                                        </span>
                                    @endif
                                    
                                    <div class="mt-2">
                                        @if($phong->muc_do_xac_thuc == 2)
                                            <span class="text-[10px] uppercase font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200"><i class="fa-solid fa-shield-halved mr-1"></i> Đã XT</span>
                                        @elseif($phong->muc_do_xac_thuc == 1)
                                            <span class="text-[10px] uppercase font-bold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded border border-yellow-200">Chờ duyệt</span>
                                        @else
                                            <span class="text-[10px] uppercase font-bold text-gray-500 bg-gray-50 px-2 py-0.5 rounded border border-gray-200">Chưa XT</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="p-4 align-top text-center">
                                    <div class="flex justify-center gap-2 flex-col">
                                        <a href="/phong-tro/{{ $phong->id }}" target="_blank" class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded text-xs font-bold transition-colors">
                                            <i class="fa-solid fa-eye mr-1"></i> Xem
                                        </a>
                                        <form action="{{ route('admin.phongtro.destroy', $phong->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng trọ này vĩnh viễn khỏi hệ thống không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded text-xs font-bold transition-colors border border-red-200 hover:border-red-600">
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
