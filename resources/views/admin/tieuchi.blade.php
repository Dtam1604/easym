@extends('layouts.app')

@section('title', 'Quản lý Tiêu chí Gợi ý - Admin')

@section('content')
<div class="flex min-h-[calc(100dvh-72px)] ops-page font-sans">
    @include('admin.partials.sidebar', ['active' => 'tieuchi'])

    <main class="flex-1 flex flex-col ops-main">
        <header class="ops-header flex items-center justify-between px-6 lg:px-8 z-10 sticky top-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.dashboard') }}" class="ops-action-secondary min-h-0 w-10 h-10 p-0" aria-label="Quay lại dashboard">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <p class="ops-kicker">Cấu hình khảo sát</p>
                    <h1 class="text-xl font-black text-gray-900">Tiêu chí lối sống</h1>
                </div>
            </div>
            <span class="ops-badge ops-badge-blue">{{ count($ds_tieu_chi) }} tiêu chí</span>
        </header>

        <div class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
            @if(session('success'))
                <div class="ops-card bg-emerald-50 border-emerald-200 p-4 text-emerald-800 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,0.9fr)_minmax(0,1.35fr)] gap-6 items-start">
                <section class="ops-card overflow-hidden">
                    <div class="ops-card-header p-5">
                        <p class="ops-kicker">Thêm mới</p>
                        <h2 class="text-lg font-black text-gray-900 mt-1">Tạo tiêu chí khảo sát</h2>
                        <p class="ops-muted text-sm mt-1">Các key này được dùng trong dữ liệu JSON của khảo sát lối sống.</p>
                    </div>

                    <form action="{{ route('admin.tieuchi.store') }}" method="POST" class="p-5 space-y-5">
                        @csrf
                        <div>
                            <label for="ten_tieu_chi" class="block text-sm font-bold text-gray-700 mb-1">Tên tiêu chí (key)</label>
                            <input type="text" name="ten_tieu_chi" id="ten_tieu_chi" required placeholder="vd: nuoi_thu_cung" class="w-full">
                            <p class="text-xs text-gray-500 mt-1">Không dấu, cách nhau bởi dấu gạch dưới.</p>
                        </div>

                        <div>
                            <label for="tieu_de_hien_thi" class="block text-sm font-bold text-gray-700 mb-1">Câu hỏi hiển thị</label>
                            <input type="text" name="tieu_de_hien_thi" id="tieu_de_hien_thi" required placeholder="vd: Bạn có nuôi thú cưng không?" class="w-full">
                        </div>

                        <div>
                            <label for="loai_input" class="block text-sm font-bold text-gray-700 mb-1">Loại câu trả lời</label>
                            <select name="loai_input" id="loai_input" class="w-full">
                                <option value="boolean">Đúng / Sai (Có / Không)</option>
                                <option value="scale5">Thang điểm 1-5 (Mức độ)</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="trong_so_nen" class="block text-sm font-bold text-gray-700 mb-1">Trọng số nền</label>
                                <input type="number" step="0.1" name="trong_so_nen" id="trong_so_nen" value="1.0" required class="w-full">
                            </div>
                            <div>
                                <label for="he_so_uu_tien" class="block text-sm font-bold text-gray-700 mb-1">Hệ số ưu tiên</label>
                                <input type="number" step="0.1" name="he_so_uu_tien" id="he_so_uu_tien" value="1.5" required class="w-full">
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="ops-action-primary">
                                <i class="fa-solid fa-plus"></i>
                                Thêm tiêu chí
                            </button>
                        </div>
                    </form>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-card-header p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <p class="ops-kicker">Đang hoạt động</p>
                            <h2 class="text-lg font-black text-gray-900 mt-1">Danh sách tiêu chí</h2>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="ops-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 text-left">Key</th>
                                    <th class="px-5 py-3 text-left">Câu hỏi</th>
                                    <th class="px-5 py-3 text-left">Loại</th>
                                    <th class="px-5 py-3 text-left">Base</th>
                                    <th class="px-5 py-3 text-left">Boost</th>
                                    <th class="px-5 py-3 text-right">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($ds_tieu_chi as $tc)
                                    <tr>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-black text-gray-900">{{ $tc->ten_tieu_chi }}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 min-w-64">{{ $tc->tieu_de_hien_thi ?? 'Chưa cài đặt' }}</td>
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <span class="ops-badge {{ $tc->loai_input == 'boolean' ? 'ops-badge-green' : 'ops-badge-blue' }} py-1">{{ $tc->loai_input ?? 'scale5' }}</span>
                                        </td>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-black text-gray-900">{{ $tc->trong_so_nen }}</td>
                                        <td class="px-5 py-4 whitespace-nowrap text-sm font-black text-blue-600">{{ $tc->he_so_uu_tien }}</td>
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.tieuchi.edit', $tc->id) }}" class="ops-action-secondary min-h-0 py-1.5 text-xs">Sửa</a>
                                                <form action="{{ route('admin.tieuchi.destroy', $tc->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tiêu chí này? Việc này có thể ảnh hưởng đến khảo sát của User.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="ops-action-danger min-h-0 py-1.5 text-xs">Xóa</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-12 text-center text-gray-500">
                                            <div class="w-14 h-14 rounded-full bg-gray-100 mx-auto mb-3 grid place-items-center">
                                                <i class="fa-solid fa-layer-group text-gray-400"></i>
                                            </div>
                                            <p class="font-bold">Chưa có tiêu chí nào.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>
@endsection
