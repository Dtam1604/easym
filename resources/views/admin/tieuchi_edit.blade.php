@extends('layouts.app')

@section('title', 'Chỉnh sửa Tiêu chí - Admin')

@section('content')
<div class="flex min-h-[calc(100dvh-72px)] ops-page font-sans">
    @include('admin.partials.sidebar', ['active' => 'tieuchi'])

    <main class="flex-1 flex flex-col ops-main">
        <header class="ops-header flex items-center justify-between px-6 lg:px-8 z-10 sticky top-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.tieuchi.index') }}" class="ops-action-secondary min-h-0 w-10 h-10 p-0" aria-label="Quay lại danh sách">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <p class="ops-kicker">Cấu hình khảo sát</p>
                    <h1 class="text-xl font-black text-gray-900">Chỉnh sửa tiêu chí</h1>
                </div>
            </div>
        </header>

        <div class="p-6 lg:p-8 max-w-4xl mx-auto w-full space-y-6">
            @if($errors->any())
                <div class="ops-card bg-red-50 border-red-200 p-4 text-red-700">
                    <p class="font-black mb-2">Vui lòng kiểm tra lại thông tin:</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="ops-card overflow-hidden">
                <div class="ops-card-header p-5">
                    <p class="ops-kicker">Thông tin cấu hình</p>
                    <h2 class="text-lg font-black text-gray-900 mt-1">{{ $tieuChi->tieu_de_hien_thi ?? $tieuChi->ten_tieu_chi }}</h2>
                    <p class="ops-muted text-sm mt-1">Thay đổi key có thể ảnh hưởng đến dữ liệu khảo sát cũ.</p>
                </div>

                <form action="{{ route('admin.tieuchi.update', $tieuChi->id) }}" method="POST" class="p-5 sm:p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="ten_tieu_chi" class="block text-sm font-bold text-gray-700 mb-1">Tên tiêu chí (key)</label>
                            <input type="text" name="ten_tieu_chi" id="ten_tieu_chi" value="{{ $tieuChi->ten_tieu_chi }}" required class="w-full">
                            <p class="text-xs text-gray-500 mt-1">Không dấu, dùng làm key trong JSON.</p>
                        </div>

                        <div>
                            <label for="tieu_de_hien_thi" class="block text-sm font-bold text-gray-700 mb-1">Câu hỏi hiển thị</label>
                            <input type="text" name="tieu_de_hien_thi" id="tieu_de_hien_thi" value="{{ $tieuChi->tieu_de_hien_thi }}" required class="w-full">
                        </div>

                        <div>
                            <label for="loai_input" class="block text-sm font-bold text-gray-700 mb-1">Loại input</label>
                            <select name="loai_input" id="loai_input" class="w-full">
                                <option value="boolean" {{ $tieuChi->loai_input == 'boolean' ? 'selected' : '' }}>Đúng / Sai (Có / Không)</option>
                                <option value="scale5" {{ $tieuChi->loai_input == 'scale5' ? 'selected' : '' }}>Thang điểm 1-5 (Mức độ)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="trong_so_nen" class="block text-sm font-bold text-gray-700 mb-1">Trọng số nền</label>
                                <input type="number" step="0.1" name="trong_so_nen" id="trong_so_nen" value="{{ $tieuChi->trong_so_nen }}" required class="w-full">
                            </div>
                            <div>
                                <label for="he_so_uu_tien" class="block text-sm font-bold text-gray-700 mb-1">Hệ số ưu tiên</label>
                                <input type="number" step="0.1" name="he_so_uu_tien" id="he_so_uu_tien" value="{{ $tieuChi->he_so_uu_tien }}" required class="w-full">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                        <a href="{{ route('admin.tieuchi.index') }}" class="ops-action-secondary">Hủy</a>
                        <button type="submit" class="ops-action-primary">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</div>
@endsection
