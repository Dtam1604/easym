@extends('layouts.app')

@section('title', 'Tạo tài khoản - EasyM')

@section('content')
<div class="min-h-[100dvh] bg-zinc-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Decorative background element -->
    <div class="absolute top-0 left-0 -translate-y-1/2 -translate-x-1/2 w-96 h-96 bg-blue-50/50 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 translate-y-1/2 translate-x-1/2 w-96 h-96 bg-zinc-100 rounded-full blur-3xl pointer-events-none"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div class="flex justify-center mb-6">
            <a href="/" class="text-3xl font-black tracking-tighter text-zinc-900">
                <span class="text-blue-600">Easy</span>M.
            </a>
        </div>
        <h2 class="text-center text-3xl font-black text-zinc-900 tracking-tight">
            Tạo tài khoản mới
        </h2>
        <p class="mt-3 text-center text-sm text-zinc-500 font-medium">
            Đã có tài khoản?
            <a href="{{ url('/dang-nhap') }}" class="font-bold text-blue-600 hover:text-blue-700 transition-colors underline decoration-blue-100 underline-offset-4">
                Đăng nhập ngay
            </a>
        </p>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div class="bg-white py-10 px-6 shadow-2xl shadow-zinc-200/50 sm:rounded-3xl sm:px-12 border border-zinc-100">
            <form class="space-y-5" action="{{ url('/dang-ky') }}" method="POST">
                @csrf
                
                <div class="space-y-2">
                    <label for="ho_ten" class="block text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Họ và Tên</label>
                    <input id="ho_ten" name="ho_ten" type="text" required value="{{ old('ho_ten') }}"
                        placeholder="Nguyễn Văn A"
                        class="appearance-none block w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl text-zinc-900 placeholder-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-600 sm:text-sm font-medium transition-all @error('ho_ten') border-red-200 bg-red-50/30 @enderror">
                    @error('ho_ten')
                        <p class="mt-1.5 text-xs text-red-600 font-bold flex items-center gap-1.5 ml-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Địa chỉ Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                        placeholder="ten@vi-du.vn"
                        class="appearance-none block w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl text-zinc-900 placeholder-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-600 sm:text-sm font-medium transition-all @error('email') border-red-200 bg-red-50/30 @enderror">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 font-bold flex items-center gap-1.5 ml-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="vai_tro" class="block text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Bạn là ai?</label>
                    <select id="vai_tro" name="vai_tro" required 
                        class="appearance-none block w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl text-zinc-900 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-600 sm:text-sm font-medium transition-all cursor-pointer">
                        <option value="nguoi_tim_tro" {{ old('vai_tro') == 'nguoi_tim_tro' ? 'selected' : '' }}>Tôi đang tìm phòng trọ hoặc ở ghép</option>
                        <option value="chu_tro" {{ old('vai_tro') == 'chu_tro' ? 'selected' : '' }}>Tôi là Chủ trọ</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="mat_khau" class="block text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Mật khẩu</label>
                        <input id="mat_khau" name="mat_khau" type="password" required 
                            placeholder="••••••••"
                            class="appearance-none block w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl text-zinc-900 placeholder-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-600 sm:text-sm font-medium transition-all @error('mat_khau') border-red-200 bg-red-50/30 @enderror">
                        @error('mat_khau')
                            <p class="mt-1.5 text-xs text-red-600 font-bold flex items-center gap-1.5 ml-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="mat_khau_confirmation" class="block text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Xác nhận</label>
                        <input id="mat_khau_confirmation" name="mat_khau_confirmation" type="password" required 
                            placeholder="••••••••"
                            class="appearance-none block w-full px-4 py-3.5 bg-zinc-50 border border-zinc-200 rounded-2xl text-zinc-900 placeholder-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-600 sm:text-sm font-medium transition-all">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-2xl shadow-lg shadow-blue-200/50 text-sm font-black text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        Tạo tài khoản EasyM
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer-style text -->
    <div class="mt-12 text-center text-xs text-zinc-400 font-medium relative z-10">
        Bằng cách đăng ký, bạn đồng ý với Điều khoản và Chính sách của chúng tôi.
    </div>
</div>
@endsection
