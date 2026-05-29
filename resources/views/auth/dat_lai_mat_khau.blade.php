@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-black text-gray-900 tracking-tight">
            Đặt lại mật khẩu mới
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 font-medium">
            Thiết lập mật khẩu mới cho tài khoản: <span class="font-bold text-gray-900">{{ $email }}</span>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-gray-100">
            
            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-medium rounded-r-xl">
                {{ session('error') }}
            </div>
            @endif

            <form class="space-y-6" action="{{ route('password.update') }}" method="POST">
                @csrf
                
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="mat_khau" class="block text-sm font-bold text-gray-700">Mật khẩu mới</label>
                    <div class="mt-1">
                        <input id="mat_khau" name="mat_khau" type="password" required 
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-medium @error('mat_khau') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    </div>
                    @error('mat_khau')
                        <p class="mt-2 text-sm text-red-600 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mat_khau_confirmation" class="block text-sm font-bold text-gray-700">Xác nhận mật khẩu mới</label>
                    <div class="mt-1">
                        <input id="mat_khau_confirmation" name="mat_khau_confirmation" type="password" required 
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-medium">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Đặt lại mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
