<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EasyM - Tìm bạn ở ghép')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body class="text-zinc-900 antialiased overflow-x-hidden">
    <nav class="easym-nav sticky top-0 z-[100]" x-data="{ mobileOpen: false, notificationsOpen: false }">
        <div class="easym-shell h-full flex items-center gap-5">
            <a href="/" class="easym-logo" aria-label="EasyM">
                <span>Easy</span>M.
            </a>

            <div class="hidden lg:flex items-center gap-1 ml-3">
                @if(!auth()->check() || auth()->user()->vai_tro === 'nguoi_tim_tro' || auth()->user()->vai_tro === 'admin')
                    <a href="{{ route('survey.show') }}" class="easym-nav-link px-3">Khảo sát</a>
                    <a href="{{ route('tim-ban.index') }}" class="easym-nav-link px-3">Tìm bạn</a>
                @endif
                <a href="{{ route('search.results') }}" class="easym-nav-link px-3">Tìm phòng</a>

                @auth
                    @if(auth()->user()->vai_tro === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="easym-nav-link px-3">Admin</a>
                    @endif
                    @if(auth()->user()->vai_tro === 'cong_tac_vien')
                        <a href="{{ route('ctv.index') }}" class="easym-nav-link px-3">Dashboard CTV</a>
                    @endif
                    @if(auth()->user()->vai_tro === 'chu_tro')
                        <a href="{{ route('chutro.phong') }}" class="easym-nav-link px-3">Quản lý phòng</a>
                        <a href="{{ route('chutro.lich_hen') }}" class="easym-nav-link px-3">Lịch hẹn</a>
                    @endif
                @endauth
            </div>

            <div class="ml-auto hidden lg:flex items-center gap-4">
                @auth
                    <div class="relative">
                        <button @click="notificationsOpen = !notificationsOpen"
                            class="relative grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 hover:text-blue-600 hover:border-blue-200"
                            aria-label="Thông báo">
                            <i class="fa-regular fa-bell"></i>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-blue-600 ring-2 ring-white"></span>
                            @endif
                        </button>

                        <div x-show="notificationsOpen"
                             x-transition:enter="transition ease-out duration-180"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             @click.away="notificationsOpen = false"
                             style="display: none;"
                             class="absolute right-0 mt-3 w-96 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
                                <h3 class="text-sm font-extrabold text-slate-900">Thông báo</h3>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('thong_bao.doc_tat_ca') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-full border border-blue-100 bg-white px-3 py-1.5 text-xs font-bold text-blue-600 hover:bg-blue-50">
                                            Đọc tất cả
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse(auth()->user()->notifications()->limit(5)->get() as $notification)
                                    <a href="{{ route('thong_bao.doc', $notification->id) }}" class="block border-b border-slate-100 p-4 hover:bg-slate-50 {{ $notification->read_at ? 'opacity-65' : 'bg-blue-50/20' }}">
                                        <div class="flex gap-3">
                                            <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-slate-200 bg-white text-slate-500">
                                                <i class="fa-solid {{ $notification->data['icon'] ?? 'fa-bell' }} {{ $notification->data['color'] ?? 'text-zinc-500' }} text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs leading-relaxed text-slate-800 {{ $notification->read_at ? 'font-medium' : 'font-extrabold' }}">
                                                    {{ $notification->data['message'] ?? 'Bạn có thông báo mới' }}
                                                </p>
                                                <p class="mt-1 text-[11px] font-medium text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-6 py-10 text-center text-sm font-medium text-slate-500">
                                        Chưa có thông báo mới
                                    </div>
                                @endforelse
                            </div>
                            <div class="border-t border-slate-100 bg-slate-50 p-3 text-center">
                                <a href="{{ route('thong_bao.index') }}" class="text-xs font-extrabold text-slate-500 hover:text-blue-600">Xem tất cả</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('kyc.form') }}" class="grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white" title="Xác thực danh tính">
                        @if(auth()->user()->da_xac_thuc_cccd)
                            <i class="fa-solid fa-shield-check text-emerald-600"></i>
                        @elseif(auth()->user()->thong_tin_cccd)
                            <i class="fa-regular fa-clock text-amber-600"></i>
                        @else
                            <i class="fa-solid fa-shield-halved text-slate-400"></i>
                        @endif
                    </a>

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-full border border-slate-200 bg-white py-1 pl-1 pr-3 hover:border-blue-200">
                        <img src="{{ auth()->user()->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->ho_ten).'&background=f3f7fb&color=1769e0' }}" class="h-9 w-9 rounded-full object-cover">
                        <div class="leading-tight">
                            <span class="block max-w-36 truncate text-xs font-extrabold text-slate-900">{{ auth()->user()->ho_ten }}</span>
                            <span class="block text-[11px] font-medium capitalize text-slate-500">{{ str_replace('_', ' ', auth()->user()->vai_tro) }}</span>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-slate-500 hover:border-red-200 hover:text-red-600" aria-label="Đăng xuất">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="easym-nav-link px-3">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="easym-btn easym-btn-primary px-5 py-3 text-sm">Đăng ký</a>
                @endauth
            </div>

            <button @click="mobileOpen = !mobileOpen"
                    class="ml-auto grid h-10 w-10 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 lg:hidden"
                    aria-label="Mở menu">
                <i class="fa-solid" :class="mobileOpen ? 'fa-xmark' : 'fa-bars'"></i>
            </button>
        </div>

        <div x-show="mobileOpen"
             x-transition
             style="display: none;"
             class="border-t border-slate-200 bg-white lg:hidden">
            <div class="easym-shell py-4">
                <div class="grid gap-1">
                    @if(!auth()->check() || auth()->user()->vai_tro === 'nguoi_tim_tro' || auth()->user()->vai_tro === 'admin')
                        <a href="{{ route('survey.show') }}" class="easym-nav-link rounded-xl px-3">Khảo sát</a>
                        <a href="{{ route('tim-ban.index') }}" class="easym-nav-link rounded-xl px-3">Tìm bạn</a>
                    @endif
                    <a href="{{ route('search.results') }}" class="easym-nav-link rounded-xl px-3">Tìm phòng</a>

                    @auth
                        @if(auth()->user()->vai_tro === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="easym-nav-link rounded-xl px-3">Admin</a>
                        @endif
                        @if(auth()->user()->vai_tro === 'cong_tac_vien')
                            <a href="{{ route('ctv.index') }}" class="easym-nav-link rounded-xl px-3">Dashboard CTV</a>
                        @endif
                        @if(auth()->user()->vai_tro === 'chu_tro')
                            <a href="{{ route('chutro.phong') }}" class="easym-nav-link rounded-xl px-3">Quản lý phòng</a>
                            <a href="{{ route('chutro.lich_hen') }}" class="easym-nav-link rounded-xl px-3">Lịch hẹn</a>
                        @endif
                        <a href="{{ route('thong_bao.index') }}" class="easym-nav-link rounded-xl px-3">Thông báo</a>
                        <a href="{{ route('profile.edit') }}" class="easym-nav-link rounded-xl px-3">Hồ sơ</a>
                        <form method="POST" action="{{ route('logout') }}" class="pt-2">
                            @csrf
                            <button type="submit" class="easym-btn easym-btn-secondary w-full px-4 py-3 text-sm">Đăng xuất</button>
                        </form>
                    @else
                        <div class="grid grid-cols-2 gap-3 pt-3">
                            <a href="{{ route('login') }}" class="easym-btn easym-btn-secondary px-4 py-3 text-sm">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="easym-btn easym-btn-primary px-4 py-3 text-sm">Đăng ký</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
