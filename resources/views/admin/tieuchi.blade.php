@extends('layouts.app')

@section('title', 'Quản lý Tiêu chí Gợi ý - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quản lý Tiêu chí Lối sống (Dynamic Criteria)</h1>
                <p class="mt-1 text-sm text-gray-500">Thêm mới và cấu hình trọng số cho các tiêu chí ghép đôi phòng trọ.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại Dashboard
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
            <p class="font-medium">{{ session('success') }}</p>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">Thêm tiêu chí mới</h3>
            </div>
            
            <form action="{{ route('admin.tieuchi.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tên tiêu chí -->
                    <div>
                        <label for="ten_tieu_chi" class="block text-sm font-medium text-gray-700 mb-1">Tên tiêu chí (không dấu, cách nhau bởi `_`)</label>
                        <input type="text" name="ten_tieu_chi" id="ten_tieu_chi" required placeholder="vd: nuoi_thu_cung" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Dùng làm key trong JSON (ví dụ: thoi_gian_ve_dem).</p>
                    </div>

                    <!-- Tiêu đề hiển thị -->
                    <div>
                        <label for="tieu_de_hien_thi" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề hiển thị (Dành cho người dùng)</label>
                        <input type="text" name="tieu_de_hien_thi" id="tieu_de_hien_thi" required placeholder="vd: Bạn có nuôi thú cưng không?" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Sẽ hiển thị thành câu hỏi ở trang Khảo sát lối sống.</p>
                    </div>
                    
                    <!-- Loại Input -->
                    <div>
                        <label for="loai_input" class="block text-sm font-medium text-gray-700 mb-1">Loại câu trả lời (Dạng nhập liệu)</label>
                        <select name="loai_input" id="loai_input" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="boolean">Đúng / Sai (Có / Không)</option>
                            <option value="scale5">Thang điểm 1-5 (Mức độ)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Định hình giao diện UI trên trang Khảo sát.</p>
                    </div>

                    <!-- Trọng số và Hệ số ưu tiên -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="trong_so_nen" class="block text-sm font-medium text-gray-700 mb-1">Trọng số nền</label>
                            <input type="number" step="0.1" name="trong_so_nen" id="trong_so_nen" value="1.0" required 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="he_so_uu_tien" class="block text-sm font-medium text-gray-700 mb-1">Hệ số ưu tiên</label>
                            <input type="number" step="0.1" name="he_so_uu_tien" id="he_so_uu_tien" value="1.5" required 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Thêm mới tiêu chí
                    </button>
                </div>
            </form>
        </div>

        <!-- Danh sách tiêu chí hiện tại -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">Các tiêu chí đang hoạt động</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tên tiêu chí (Key)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Câu hỏi hiển thị</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Loại Input</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trọng số nền</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hệ số ưu tiên</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($ds_tieu_chi as $tc)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tc->ten_tieu_chi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tc->tieu_de_hien_thi ?? 'Chưa cài đặt' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $tc->loai_input == 'boolean' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $tc->loai_input ?? 'scale5' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">{{ $tc->trong_so_nen }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-blue-600">{{ $tc->he_so_uu_tien }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end gap-2">
                                <a href="{{ route('admin.tieuchi.edit', $tc->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition-colors">Sửa</a>
                                <form action="{{ route('admin.tieuchi.destroy', $tc->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tiêu chí này? Việc này có thể ảnh hưởng đến khảo sát của User.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
