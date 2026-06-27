@extends('layouts.app')

@section('title', 'Quản lý Tài khoản - Admin EasyM')

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
                    <a href="{{ route('admin.phongtro.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-building w-6"></i>
                        <span class="font-medium text-sm">Quản lý Tất cả phòng trọ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.nguoidung.index') }}" class="sidebar-item active flex items-center px-6 py-3 transition-colors">
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
                    <p class="ops-kicker">Tài khoản hệ thống</p>
                    <h2 class="text-xl font-black text-gray-900">Quản lý người dùng</h2>
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
                        <h3 class="font-black text-gray-900 text-lg mt-1"><i class="fa-solid fa-users mr-2 text-blue-500"></i> Tài khoản hệ thống</h3>
                    </div>
                    <span class="ops-badge ops-badge-blue">{{ $nguoiDungs->total() }} tài khoản</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="ops-table">
                        <thead>
                            <tr>
                                <th class="p-4 text-left">ID</th>
                                <th class="p-4 text-left">Người dùng</th>
                                <th class="p-4 text-left">Vai trò</th>
                                <th class="p-4 text-center">KYC</th>
                                <th class="p-4 text-center">Ngày tạo</th>
                                <th class="p-4 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($nguoiDungs as $user)
                            <tr>
                                <td class="p-4 align-middle">
                                    <span class="text-gray-500 font-bold text-sm">#{{ $user->id }}</span>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex gap-3 items-center">
                                        <img src="{{ $user->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($user->ho_ten).'&background=e0f2fe&color=0369a1' }}" class="w-10 h-10 rounded-full border border-gray-200 object-cover">
                                        <div>
                                            <div class="font-bold text-gray-900 flex items-center gap-1">
                                                {{ $user->ho_ten }}
                                                @if($user->da_xac_thuc_cccd)
                                                    <i class="fa-solid fa-circle-check text-blue-500 text-xs" title="Đã xác thực"></i>
                                                @endif
                                                @if($user->trang_thai_khoa)
                                                    <span class="ops-badge ops-badge-red py-0.5 px-2 text-[9px] uppercase ml-1">
                                                        Bị khóa
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            @if($user->so_dien_thoai)
                                                <div class="text-xs text-gray-400 mt-0.5"><i class="fa-solid fa-phone mr-1"></i>{{ $user->so_dien_thoai }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    @if($user->vai_tro === 'admin')
                                        <span class="ops-badge ops-badge-red py-1">Admin</span>
                                    @elseif($user->vai_tro === 'chu_tro')
                                        <span class="ops-badge ops-badge-blue py-1">Chủ trọ</span>
                                    @elseif($user->vai_tro === 'cong_tac_vien')
                                        <span class="ops-badge ops-badge-amber py-1">CTV</span>
                                    @else
                                        <span class="ops-badge py-1">Người tìm trọ</span>
                                    @endif
                                </td>
                                <td class="p-4 align-middle text-center">
                                    @if($user->da_xac_thuc_cccd)
                                        <span class="ops-badge ops-badge-green py-1 text-[10px] uppercase">
                                            <i class="fa-solid fa-shield-check mr-1"></i> Đã xác thực
                                        </span>
                                    @elseif($user->thong_tin_cccd)
                                        <span class="ops-badge ops-badge-amber py-1 text-[10px] uppercase">
                                            Chờ duyệt
                                        </span>
                                    @else
                                        <span class="ops-badge py-1 text-[10px] uppercase">
                                            Chưa xác thực
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 align-middle text-center text-sm text-gray-500">
                                    {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="p-4 align-middle text-center">
                                    @if($user->id !== auth()->id())
                                    <div class="flex justify-center gap-2">
                                        <!-- Khóa / Mở khóa -->
                                        @if($user->trang_thai_khoa)
                                            <form action="{{ route('admin.nguoidung.toggle_lock', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn mở khóa tài khoản {{ $user->ho_ten }}? Người dùng này sẽ có thể tiếp tục truy cập hệ thống.');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-full text-xs font-bold transition-colors border border-emerald-200 hover:border-emerald-600 shadow-sm flex items-center gap-1">
                                                    <i class="fa-solid fa-unlock"></i> Mở khóa
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.nguoidung.toggle_lock', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn khóa tài khoản {{ $user->ho_ten }}? Người dùng này sẽ không thể tiếp tục truy cập hệ thống.');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-yellow-50 text-yellow-600 hover:bg-yellow-600 hover:text-white rounded-full text-xs font-bold transition-colors border border-yellow-200 hover:border-yellow-600 shadow-sm flex items-center gap-1">
                                                    <i class="fa-solid fa-user-lock"></i> Khóa
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.nguoidung.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này vĩnh viễn không? Các dữ liệu liên quan cũng có thể bị mất.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-full text-xs font-bold transition-colors border border-red-200 hover:border-red-600">
                                                <i class="fa-solid fa-trash-can mr-1"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400 italic">Tài khoản của bạn</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">
                                    <div class="text-4xl text-gray-300 mb-3"><i class="fa-solid fa-users-slash"></i></div>
                                    <p>Chưa có tài khoản nào trong hệ thống.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 border-t border-gray-200 bg-white">
                    {{ $nguoiDungs->links() }}
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
