<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EasyM - Tìm bạn ở ghép')</title>
    <!-- Tailwind CSS qua CDN de demo nhanh -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased overflow-x-hidden">
    <!-- Navbar Header -->
    <nav class="bg-white shadow-sm border-b border-gray-100 h-16 flex items-center px-4 md:px-8 sticky top-0 z-[100]">
        <a href="/" class="text-2xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-sky-400">
            EasyM.
        </a>
        <div class="ml-auto flex items-center gap-6 hidden md:flex">
            @if(!auth()->check() || auth()->user()->vai_tro === 'nguoi_tim_tro' || auth()->user()->vai_tro === 'admin')
                <a href="{{ route('survey.show') }}" class="text-gray-600 hover:text-blue-600 font-bold transition-colors text-sm">Khảo sát</a>
                <a href="{{ route('tim-ban.index') }}" class="text-gray-600 hover:text-blue-600 font-bold transition-colors text-sm">Tìm bạn</a>
            @endif
            <a href="{{ route('search.results') }}" class="text-gray-600 hover:text-blue-600 font-bold transition-colors text-sm">Tìm phòng</a>
            
            @auth
                @if(auth()->user()->vai_tro === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-blue-600 font-bold transition-colors text-sm">Admin</a>
                @endif
                @if(auth()->user()->vai_tro === 'cong_tac_vien')
                    <a href="{{ route('ctv.index') }}" class="text-gray-600 hover:text-blue-600 font-bold transition-colors text-sm">Dashboard CTV</a>
                @endif
                @if(auth()->user()->vai_tro === 'chu_tro')
                    <a href="{{ route('chutro.phong') }}" class="text-gray-600 hover:text-blue-600 font-bold transition-colors text-sm">Quản lý phòng</a>
                    <a href="{{ route('chutro.lich_hen') }}" class="text-gray-600 hover:text-blue-600 font-bold transition-colors text-sm">Lịch hẹn</a>
                @endif
                <div class="h-6 w-px bg-gray-200"></div>
                <div class="flex items-center gap-4">
                    
                    <!-- Nút Thông báo -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-gray-500 hover:text-blue-600 transition-colors relative mt-1">
                            <i class="fa-solid fa-bell text-xl"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>

                        <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                <h3 class="font-bold text-gray-800">Thông báo</h3>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('thong_bao.doc_tat_ca') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium bg-blue-50 px-2 py-1 rounded-md">Đánh dấu đã đọc</button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse(auth()->user()->notifications()->limit(5)->get() as $notification)
                                    <a href="{{ route('thong_bao.doc', $notification->id) }}" class="block p-3 border-b border-gray-50 hover:bg-slate-50 transition-colors {{ $notification->read_at ? 'opacity-70' : 'bg-blue-50/30' }}">
                                        <div class="flex gap-3">
                                            <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                                <i class="fa-solid {{ $notification->data['icon'] ?? 'fa-bell' }} {{ $notification->data['color'] ?? 'text-gray-500' }} text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-800 {{ $notification->read_at ? '' : 'font-bold' }}">
                                                    {{ $notification->data['message'] ?? 'Bạn có thông báo mới' }}
                                                </p>
                                                <p class="text-[11px] text-gray-500 mt-1 font-medium">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-8 text-center text-gray-400 text-sm">
                                        <i class="fa-regular fa-bell-slash text-3xl mb-3 text-gray-300"></i><br>
                                        Bạn chưa có thông báo nào
                                    </div>
                                @endforelse
                            </div>
                            <div class="p-2 text-center border-t border-gray-100 bg-gray-50">
                                <a href="{{ route('thong_bao.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 block py-1">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('kyc.form') }}" title="Xác thực danh tính">
                        @if(auth()->user()->da_xac_thuc_cccd)
                            <i class="fa-solid fa-shield-check text-emerald-500 text-lg hover:text-emerald-600 transition-colors"></i>
                        @elseif(auth()->user()->thong_tin_cccd)
                            <i class="fa-solid fa-clock-rotate-left text-amber-500 text-lg hover:text-amber-600 transition-colors"></i>
                        @else
                            <i class="fa-solid fa-shield-halved text-gray-400 text-lg hover:text-blue-500 transition-colors"></i>
                        @endif
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:bg-gray-100 py-1.5 px-3 rounded-xl transition-colors cursor-pointer">
                        <img src="{{ auth()->user()->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->ho_ten).'&background=e0f2fe&color=0369a1' }}" class="w-8 h-8 rounded-full border border-gray-200 object-cover">
                        <span class="text-sm font-bold text-gray-700 flex items-center gap-1">
                            {{ auth()->user()->ho_ten }}
                            @if(auth()->user()->da_xac_thuc_cccd)
                                <i class="fa-solid fa-circle-check text-blue-500 text-[12px]" title="Tài khoản đã xác thực"></i>
                            @endif
                        </span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="text-sm font-bold text-red-500 hover:text-red-700 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Đăng xuất
                        </button>
                    </form>
                </div>
            @else
                <div class="h-6 w-px bg-gray-200"></div>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-bold transition-colors text-sm">Đăng nhập</a>
                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold transition-colors text-sm shadow-sm">Đăng ký</a>
            @endauth
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
