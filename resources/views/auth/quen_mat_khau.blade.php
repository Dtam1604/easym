@extends('layouts.app')

@section('title', 'Quên mật khẩu - EasyM')

@section('content')
<div class="ops-page flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-black text-gray-900 tracking-tight">
            Quên mật khẩu?
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 font-medium">
            Nhập email của bạn và chúng tôi sẽ gửi liên kết đặt lại mật khẩu.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="ops-card py-8 px-4 sm:px-10">
            
            @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-2xl">
                <p class="font-bold mb-2"><i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}</p>
                @if(session('demo_email') && session('demo_token'))
                <div class="mt-3 p-3 bg-white border border-emerald-200 rounded-lg shadow-sm">
                    <span class="text-xs text-slate-500 font-bold block mb-1">MÔ PHỎNG EMAIL RESET (DEMO):</span>
                    <a href="{{ route('password.reset', ['email' => session('demo_email'), 'token' => session('demo_token')]) }}" 
                       class="text-blue-600 hover:text-blue-800 break-all font-bold underline text-xs">
                       Nhấp vào đây để đổi mật khẩu mới (Mô phỏng link gửi qua email)
                    </a>
                </div>
                @endif
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-2xl">
                {{ session('error') }}
            </div>
            @endif

            <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
                @csrf
                
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700">Email của bạn</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-medium @error('email') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                            placeholder="username@example.com">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 font-medium" id="email-error"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="ops-action-primary w-full min-h-[2.75rem]">
                        Gửi liên kết đặt lại mật khẩu
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="font-bold text-sm text-blue-600 hover:text-blue-500">
                        Quay lại Đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
