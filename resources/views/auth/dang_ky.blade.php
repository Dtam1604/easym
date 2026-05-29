@extends('layouts.app')

@section('title', 'Đăng ký tài khoản - EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-black text-gray-900 tracking-tight">
            Tạo tài khoản EasyM
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 font-medium">
            Đã có tài khoản?
            <a href="{{ url('/dang-nhap') }}" class="font-bold text-blue-600 hover:text-blue-500 transition-colors">
                Đăng nhập
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-gray-100">
            <form class="space-y-5" action="{{ url('/dang-ky') }}" method="POST">
                @csrf
                
                <div>
                    <label for="ho_ten" class="block text-sm font-bold text-gray-700">Họ và Tên</label>
                    <div class="mt-1">
                        <input id="ho_ten" name="ho_ten" type="text" required value="{{ old('ho_ten') }}"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-medium @error('ho_ten') border-red-300 @enderror">
                    </div>
                    @error('ho_ten')
                        <p class="mt-2 text-sm text-red-600 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700">Địa chỉ Email</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-medium @error('email') border-red-300 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vai_tro" class="block text-sm font-bold text-gray-700">Bạn là ai?</label>
                    <div class="mt-1">
                        <select id="vai_tro" name="vai_tro" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-medium">
                            <option value="nguoi_tim_tro" {{ old('vai_tro') == 'nguoi_tim_tro' ? 'selected' : '' }}>Tôi đang tìm phòng trọ/ở ghép</option>
                            <option value="chu_tro" {{ old('vai_tro') == 'chu_tro' ? 'selected' : '' }}>Tôi là Chủ trọ</option>
                        </select>
                    </div>
                    @error('vai_tro')
                        <p class="mt-2 text-sm text-red-600 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mat_khau" class="block text-sm font-bold text-gray-700">Mật khẩu</label>
                    <div class="mt-1">
                        <input id="mat_khau" name="mat_khau" type="password" required 
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-medium @error('mat_khau') border-red-300 @enderror">
                    </div>
                    @error('mat_khau')
                        <p class="mt-2 text-sm text-red-600 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mat_khau_confirmation" class="block text-sm font-bold text-gray-700">Xác nhận Mật khẩu</label>
                    <div class="mt-1">
                        <input id="mat_khau_confirmation" name="mat_khau_confirmation" type="password" required 
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-medium">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Tạo tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
