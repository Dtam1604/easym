@extends('layouts.app')

@section('title', 'Đăng nhập - EasyM')

@section('content')
<div class="min-h-[100dvh] bg-zinc-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Decorative background element (restrained, no AI purple) -->
    <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-blue-50/50 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-96 h-96 bg-zinc-100 rounded-full blur-3xl pointer-events-none"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div class="flex justify-center mb-6">
            <a href="/" class="text-3xl font-black tracking-tighter text-zinc-900">
                <span class="text-blue-600">Easy</span>M.
            </a>
        </div>
        <h2 class="text-center text-3xl font-black text-zinc-900 tracking-tight">
            Chào mừng trở lại
        </h2>
        <p class="mt-3 text-center text-sm text-zinc-500 font-medium">
            Chưa có tài khoản?
            <a href="{{ url('/dang-ky') }}" class="font-bold text-blue-600 hover:text-blue-700 transition-colors underline decoration-blue-100 underline-offset-4">
                Đăng ký ngay
            </a>
        </p>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div class="bg-white py-10 px-6 shadow-2xl shadow-zinc-200/50 sm:rounded-3xl sm:px-12 border border-zinc-100">
            
            @if(session('error'))
            <div class="mb-8 p-4 bg-red-50 border border-red-100 rounded-2xl text-red-700 text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            <form class="space-y-6" action="{{ url('/dang-nhap') }}" method="POST">
                @csrf
                
                <div class="space-y-2">
                    <label for="email" class="block text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Email của bạn</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                        placeholder="ten@vi-du.vn"
                        class="appearance-none block w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl text-zinc-900 placeholder-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-600 sm:text-sm font-medium transition-all @error('email') border-red-200 bg-red-50/30 @enderror">
                    @error('email')
                        <p class="mt-2 text-xs text-red-600 font-bold flex items-center gap-1.5 ml-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between ml-1">
                        <label for="mat_khau" class="block text-xs font-bold text-zinc-400 uppercase tracking-widest">Mật khẩu</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                            Quên mật khẩu?
                        </a>
                    </div>
                    <input id="mat_khau" name="mat_khau" type="password" autocomplete="current-password" required 
                        placeholder="••••••••"
                        class="appearance-none block w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl text-zinc-900 placeholder-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-600 sm:text-sm font-medium transition-all @error('mat_khau') border-red-200 bg-red-50/30 @enderror">
                    @error('mat_khau')
                        <p class="mt-2 text-xs text-red-600 font-bold flex items-center gap-1.5 ml-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex items-center ml-1">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-100 border-zinc-300 rounded-md transition-all cursor-pointer">
                    <label for="remember-me" class="ml-2.5 block text-sm text-zinc-600 font-bold cursor-pointer">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg shadow-blue-200/50 text-sm font-black text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        Đăng nhập vào hệ thống
                    </button>
                </div>

                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-zinc-100"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-4 bg-white text-zinc-400 font-bold uppercase tracking-widest">Hoặc đăng nhập nhanh</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('auth.google') }}" class="w-full flex justify-center items-center py-3.5 px-4 border border-zinc-200 rounded-2xl bg-white text-sm font-bold text-zinc-700 hover:bg-zinc-50 transition-all active:scale-[0.98] shadow-sm">
                            <img class="h-5 w-5 mr-3" src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google logo">
                            Tiếp tục với Google
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer-style text (optional) -->
    <div class="mt-12 text-center text-xs text-zinc-400 font-medium relative z-10">
        &copy; {{ date('Y') }} EasyM. Hệ thống tìm phòng trọ sinh viên thông minh.
    </div>
</div>
@endsection
