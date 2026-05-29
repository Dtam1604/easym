@extends('layouts.app')

@section('title', 'Chỉnh sửa Tiêu chí - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa Tiêu chí: {{ $tieuChi->tieu_de_hien_thi ?? $tieuChi->ten_tieu_chi }}</h1>
            </div>
            <a href="{{ route('admin.tieuchi.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại Danh sách
            </a>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">Thông tin cấu hình</h3>
            </div>
            
            <form action="{{ route('admin.tieuchi.update', $tieuChi->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tên tiêu chí -->
                    <div>
                        <label for="ten_tieu_chi" class="block text-sm font-medium text-gray-700 mb-1">Tên tiêu chí (Key)</label>
                        <input type="text" name="ten_tieu_chi" id="ten_tieu_chi" value="{{ $tieuChi->ten_tieu_chi }}" required 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Lưu ý: Thay đổi key sẽ ảnh hưởng đến dữ liệu JSONB cũ.</p>
                    </div>

                    <!-- Tiêu đề hiển thị -->
                    <div>
                        <label for="tieu_de_hien_thi" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề hiển thị</label>
                        <input type="text" name="tieu_de_hien_thi" id="tieu_de_hien_thi" value="{{ $tieuChi->tieu_de_hien_thi }}" required 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <!-- Loại Input -->
                    <div>
                        <label for="loai_input" class="block text-sm font-medium text-gray-700 mb-1">Loại Input</label>
                        <select name="loai_input" id="loai_input" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="boolean" {{ $tieuChi->loai_input == 'boolean' ? 'selected' : '' }}>Đúng / Sai (Có / Không)</option>
                            <option value="scale5" {{ $tieuChi->loai_input == 'scale5' ? 'selected' : '' }}>Thang điểm 1-5 (Mức độ)</option>
                        </select>
                    </div>

                    <!-- Trọng số và Hệ số ưu tiên -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="trong_so_nen" class="block text-sm font-medium text-gray-700 mb-1">Trọng số nền</label>
                            <input type="number" step="0.1" name="trong_so_nen" id="trong_so_nen" value="{{ $tieuChi->trong_so_nen }}" required 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="he_so_uu_tien" class="block text-sm font-medium text-gray-700 mb-1">Hệ số ưu tiên</label>
                            <input type="number" step="0.1" name="he_so_uu_tien" id="he_so_uu_tien" value="{{ $tieuChi->he_so_uu_tien }}" required 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('admin.tieuchi.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50 transition-colors">Hủy</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Lưu Thay Đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
