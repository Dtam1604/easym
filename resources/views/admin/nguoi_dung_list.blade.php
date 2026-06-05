@extends('layouts.app')

@section('title', 'Quản lý Tài khoản - Admin EasyM')

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
    <main class="flex-1 flex flex-col overflow-hidden h-screen overflow-y-auto scroll-smooth">
        <!-- Header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10 sticky top-0">
            <div class="flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-blue-600 mr-4">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-800">Quản lý Tài khoản Người dùng</h2>
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
                    <h3 class="font-bold text-gray-800 text-lg"><i class="fa-solid fa-users mr-2 text-blue-500"></i> Danh sách tài khoản hệ thống</h3>
                    <span class="text-sm text-gray-500">Tổng cộng: <span class="font-bold text-blue-600">{{ $nguoiDungs->total() }}</span> tài khoản</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                                <th class="p-4 border-b font-bold">ID</th>
                                <th class="p-4 border-b font-bold">Người dùng</th>
                                <th class="p-4 border-b font-bold">Vai trò</th>
                                <th class="p-4 border-b font-bold text-center">Trạng thái KYC</th>
                                <th class="p-4 border-b font-bold text-center">Ngày tạo</th>
                                <th class="p-4 border-b font-bold text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($nguoiDungs as $user)
                            <tr class="hover:bg-blue-50/50 transition-colors">
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
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 border border-red-200 uppercase ml-1">
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
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">Admin</span>
                                    @elseif($user->vai_tro === 'chu_tro')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Chủ trọ</span>
                                    @elseif($user->vai_tro === 'cong_tac_vien')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">CTV</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">Người tìm trọ</span>
                                    @endif
                                </td>
                                <td class="p-4 align-middle text-center">
                                    @if($user->da_xac_thuc_cccd)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase">
                                            <i class="fa-solid fa-shield-check mr-1"></i> Đã xác thực
                                        </span>
                                    @elseif($user->thong_tin_cccd)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200 uppercase">
                                            Chờ duyệt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 uppercase">
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
                                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded text-xs font-bold transition-colors border border-emerald-200 hover:border-emerald-600 shadow-sm flex items-center gap-1">
                                                    <i class="fa-solid fa-unlock"></i> Mở khóa
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.nguoidung.toggle_lock', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn khóa tài khoản {{ $user->ho_ten }}? Người dùng này sẽ không thể tiếp tục truy cập hệ thống.');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-yellow-50 text-yellow-600 hover:bg-yellow-600 hover:text-white rounded text-xs font-bold transition-colors border border-yellow-200 hover:border-yellow-600 shadow-sm flex items-center gap-1">
                                                    <i class="fa-solid fa-user-lock"></i> Khóa
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.nguoidung.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này vĩnh viễn không? Các dữ liệu liên quan cũng có thể bị mất.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded text-xs font-bold transition-colors border border-red-200 hover:border-red-600">
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
