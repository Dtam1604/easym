@php($active = $active ?? '')

<aside class="w-64 sidebar-dark ops-sidebar hidden md:flex flex-col z-20">
    <div class="h-16 flex items-center justify-center border-b border-slate-800">
        <h1 class="ops-brand text-2xl">EasyM <span>ADMIN</span></h1>
    </div>
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ $active === 'dashboard' ? 'active' : '' }} flex items-center px-6 py-3 transition-colors">
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
                <a href="{{ route('admin.tieuchi.index') }}" class="sidebar-item {{ $active === 'tieuchi' ? 'active' : '' }} flex items-center px-6 py-3 transition-colors">
                    <i class="fa-solid fa-layer-group w-6"></i>
                    <span class="font-medium text-sm">Tiêu chí lối sống</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.kyc.index') }}" class="sidebar-item {{ $active === 'kyc' ? 'active' : '' }} flex items-center px-6 py-3 transition-colors">
                    <i class="fa-solid fa-id-card-clip w-6"></i>
                    <span class="font-medium text-sm">Duyệt Hồ sơ KYC</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.baocao.index') }}" class="sidebar-item {{ $active === 'baocao' ? 'active' : '' }} flex items-center px-6 py-3 transition-colors">
                    <i class="fa-solid fa-flag w-6 text-red-400"></i>
                    <span class="font-medium text-sm text-red-100">Báo cáo vi phạm</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.phongtro.index') }}" class="sidebar-item {{ $active === 'phongtro' ? 'active' : '' }} flex items-center px-6 py-3 transition-colors">
                    <i class="fa-solid fa-building w-6"></i>
                    <span class="font-medium text-sm">Quản lý phòng trọ</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.nguoidung.index') }}" class="sidebar-item {{ $active === 'nguoidung' ? 'active' : '' }} flex items-center px-6 py-3 transition-colors">
                    <i class="fa-solid fa-users w-6"></i>
                    <span class="font-medium text-sm">Quản lý Tài khoản</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.ctv.index') }}" class="sidebar-item {{ $active === 'ctv' ? 'active' : '' }} flex items-center px-6 py-3 transition-colors">
                    <i class="fa-solid fa-user-gear w-6"></i>
                    <span class="font-medium text-sm">Quản lý CTV</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
