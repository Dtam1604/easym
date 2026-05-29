@extends('layouts.app')

@section('title', 'Quản lý phòng trọ - EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <i class="fa-solid fa-house-user text-blue-600"></i> Quản lý phòng đã đăng
                </h1>
                <p class="text-gray-500 mt-2">Theo dõi trạng thái và yêu cầu xác thực các phòng trọ của bạn.</p>
            </div>
            <a href="{{ route('chutro.phong.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 hover:shadow-blue-200 transition-all focus:ring-4 focus:ring-blue-100">
                <i class="fa-solid fa-plus mr-2"></i> Đăng phòng mới
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- List/Grid of Rooms -->
        @if($danhSachPhong->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($danhSachPhong as $phong)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row transition-shadow hover:shadow-md">
                        <!-- Thumbnail -->
                        <div class="w-full md:w-48 h-48 md:h-auto bg-gray-200 relative shrink-0">
                            @php
                                $anhDauTien = is_array($phong->anh_phong) && count($phong->anh_phong) > 0 ? $phong->anh_phong[0] : null;
                            @endphp
                            @if($anhDauTien)
                                <img src="{{ $anhDauTien }}" class="w-full h-full object-cover" alt="Ảnh phòng">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100">
                                    <i class="fa-solid fa-image text-3xl mb-2"></i>
                                    <span class="text-xs font-medium">Chưa có ảnh</span>
                                </div>
                            @endif

                            <!-- Status Badge Float -->
                            <div class="absolute top-3 left-3 flex flex-col gap-2">
                                <button onclick="toggleTrangThaiThue({{ $phong->id }})" id="badge-status-{{ $phong->id }}" title="Click để thay đổi trạng thái" class="px-3 py-1 text-xs font-bold rounded-full shadow-md transition-colors border {{ $phong->trang_thai_thue == 1 ? 'bg-emerald-100 text-emerald-700 border-emerald-200 hover:bg-emerald-200' : 'bg-red-100 text-red-700 border-red-200 hover:bg-red-200' }}">
                                    @if($phong->trang_thai_thue == 1)
                                        <i class="fa-solid fa-door-open mr-1"></i> Đang trống (Nhấn để đổi)
                                    @else
                                        <i class="fa-solid fa-door-closed mr-1"></i> Đã cho thuê (Nhấn để đổi)
                                    @endif
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start gap-2">
                                    <h3 class="text-lg font-bold text-gray-900 leading-snug line-clamp-2">
                                        {{ $phong->tieu_de }}
                                    </h3>
                                    <div class="text-lg font-black text-blue-600 whitespace-nowrap">
                                        {{ number_format($phong->gia_phong, 0, ',', '.') }}<span class="text-sm text-gray-500 font-medium">đ</span>
                                    </div>
                                </div>
                                <div class="mt-2 space-y-1.5">
                                    <p class="text-sm text-gray-600 flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot w-4 text-gray-400"></i>
                                        <span class="truncate" title="{{ $phong->dia_chi_chi_tiet }}">{{ $phong->dia_chi_chi_tiet ?? 'Chưa cập nhật địa chỉ' }}</span>
                                    </p>
                                    <p class="text-sm text-gray-600 flex items-center gap-2">
                                        <i class="fa-solid fa-maximize w-4 text-gray-400"></i>
                                        <span>{{ $phong->dien_tich ? $phong->dien_tich . ' m²' : 'Chưa cập nhật diện tích' }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Actions & Verification Status -->
                            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between gap-2" id="verification-container-{{ $phong->id }}">
                                <a href="{{ route('chutro.phong.edit', $phong->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 rounded-lg text-sm font-bold transition-colors">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Sửa
                                </a>

                                <div class="flex-1 flex justify-end">
                                    @if($phong->muc_do_xac_thuc == 0)
                                        <button onclick="yeuCauXacThuc({{ $phong->id }})" class="text-sm font-bold text-amber-600 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition-colors border border-amber-200 w-full sm:w-auto text-center">
                                            <i class="fa-solid fa-paper-plane mr-1"></i> Yêu cầu xác thực
                                        </button>
                                    @elseif($phong->muc_do_xac_thuc == 1)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-amber-50 text-amber-600 border border-amber-200 justify-center">
                                            <i class="fa-solid fa-clock-rotate-left mr-2"></i> Đang chờ duyệt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 justify-center">
                                            <i class="fa-solid fa-shield-check mr-2"></i> Đã xác thực
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $danhSachPhong->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-house-circle-xmark text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Chưa có phòng trọ nào</h3>
                <p class="text-gray-500 mb-6">Bạn chưa đăng tin cho thuê phòng nào trên hệ thống.</p>
                <a href="{{ route('chutro.phong.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg hover:bg-blue-700 transition-all">
                    Đăng tin ngay
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
    /**
     * AJAX: Thay đổi trạng thái thuê của phòng
     */
    async function toggleTrangThaiThue(idPhong) {
        const btn = document.getElementById('badge-status-' + idPhong);
        const originalHtml = btn.innerHTML;
        const originalClass = btn.className;

        // Hiệu ứng loading
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            const response = await fetch(`/api/phong/${idPhong}/toggle-thue`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Đổi UI tương ứng với trạng thái mới
                if (data.trang_thai_moi == 1) {
                    btn.className = 'px-3 py-1 text-xs font-bold rounded-full shadow-md transition-colors border bg-emerald-100 text-emerald-700 border-emerald-200 hover:bg-emerald-200';
                    btn.innerHTML = '<i class="fa-solid fa-door-open mr-1"></i> Đang trống (Nhấn để đổi)';
                } else {
                    btn.className = 'px-3 py-1 text-xs font-bold rounded-full shadow-md transition-colors border bg-red-100 text-red-700 border-red-200 hover:bg-red-200';
                    btn.innerHTML = '<i class="fa-solid fa-door-closed mr-1"></i> Đã cho thuê (Nhấn để đổi)';
                }
            } else {
                alert(data.message);
                btn.className = originalClass;
                btn.innerHTML = originalHtml;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Lỗi kết nối. Vui lòng thử lại.');
            btn.className = originalClass;
            btn.innerHTML = originalHtml;
        } finally {
            btn.disabled = false;
        }
    }

    /**
     * AJAX: Gửi yêu cầu xác thực phòng
     */
    async function yeuCauXacThuc(idPhong) {
        if(!confirm('Bạn muốn gửi yêu cầu cho CTV đến kiểm tra thực địa phòng này?')) return;

        const container = document.getElementById('verification-container-' + idPhong);
        const originalHtml = container.innerHTML;

        container.innerHTML = `<span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-gray-50 text-gray-500 border border-gray-200 w-full justify-center">
            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang gửi yêu cầu...
        </span>`;

        try {
            const response = await fetch(`/api/phong/${idPhong}/yeu-cau-xac-thuc`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Chuyển sang Badge màu Vàng
                container.innerHTML = `<span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-amber-50 text-amber-600 border border-amber-200 w-full justify-center transition-all duration-500 ease-in-out" style="animation: pulse 2s infinite;">
                    <i class="fa-solid fa-clock-rotate-left mr-2"></i> Đang chờ CTV kiểm tra
                </span>`;
                // Có thể hiển thị thêm toast thông báo
            } else {
                alert(data.message);
                container.innerHTML = originalHtml;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Lỗi mạng. Vui lòng thử lại.');
            container.innerHTML = originalHtml;
        }
    }
</script>
@endpush
