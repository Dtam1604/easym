@extends('layouts.app')

@section('title', 'Admin Dashboard - EasyM')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Toastify for AJAX notifications -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<style>
    /* Dark Sidebar Theme Tailwind Utilities */
    .sidebar-dark { background-color: #0f172a; color: #f8fafc; } /* slate-900 */
    .sidebar-item:hover { background-color: #1e293b; color: #38bdf8; }
    .sidebar-item.active { background-color: #1e293b; border-left: 4px solid #38bdf8; color: #38bdf8; }
</style>
@endpush

@section('content')
<div class="flex h-screen bg-gray-100 font-sans" x-data="adminDashboard()">

    <!-- 1. SIDEBAR (Dark Mode Corporate Theme) -->
    <aside class="w-64 sidebar-dark hidden md:flex flex-col shadow-2xl z-20">
        <div class="h-16 flex items-center justify-center border-b border-slate-800">
            <h1 class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-sky-300 tracking-wider">
                EasyM <span class="font-light text-slate-300 text-sm">ADMIN</span>
            </h1>
        </div>
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <li>
                    <a href="#" class="sidebar-item active flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-chart-pie w-6"></i>
                        <span class="font-medium text-sm">Tổng quan & Thống kê</span>
                    </a>
                </li>
                <li>
                    <a href="#weights-config" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-sliders w-6"></i>
                        <span class="font-medium text-sm">Cấu hình Thuật toán</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tieuchi.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-layer-group w-6"></i>
                        <span class="font-medium text-sm">Thêm Tiêu chí mới</span>
                    </a>
                </li>
                <li>
                    <a href="#verification" class="sidebar-item flex items-center px-6 py-3 transition-colors relative">
                        <i class="fa-solid fa-house-circle-check w-6"></i>
                        <span class="font-medium text-sm">Duyệt Tin đăng</span>
                        @if(isset($phongChoDuyet) && count($phongChoDuyet) > 0)
                            <span class="absolute right-4 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ count($phongChoDuyet) }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kyc.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors relative">
                        <i class="fa-solid fa-id-card-clip w-6"></i>
                        <span class="font-medium text-sm">Duyệt Hồ sơ KYC</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.baocao.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors relative">
                        <i class="fa-solid fa-flag w-6 text-red-400"></i>
                        <span class="font-medium text-sm text-red-100">Báo cáo vi phạm</span>
                        @php
                            $baoCaoCount = \App\Models\BaoCaoPhong::where('trang_thai', 'chua_xu_ly')->count();
                        @endphp
                        @if($baoCaoCount > 0)
                            <span class="absolute right-4 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $baoCaoCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.phongtro.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-building w-6"></i>
                        <span class="font-medium text-sm">Quản lý Tất cả phòng trọ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.nguoidung.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-users w-6"></i>
                        <span class="font-medium text-sm">Quản lý Tài khoản</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.ctv.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-user-gear w-6"></i>
                        <span class="font-medium text-sm">Quản lý CTV</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-4 border-t border-slate-800 text-xs text-slate-500 text-center">
            &copy; 2026 EasyM Platform
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col overflow-hidden h-screen overflow-y-auto scroll-smooth pb-20">
        
        <!-- Header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10 sticky top-0">
            <h2 class="text-xl font-bold text-gray-800">Bảng điều khiển Trung tâm</h2>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-600">Xin chào, <span class="font-bold text-blue-600">Admin Tâm</span></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="w-8 h-8 rounded-full border-2 border-blue-200">
            </div>
        </header>

        <div class="p-8 space-y-8 max-w-7xl mx-auto w-full">

            <!-- 2. THỐNG KÊ TRỰC QUAN (Chart.js Analytics) -->
            <section>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-gray-800 uppercase tracking-wide"><i class="fa-solid fa-chart-line text-blue-500 mr-2"></i> Phân tích Hệ thống</h3>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Bar Chart -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h4 class="text-sm font-bold text-gray-500 mb-4">MẬT ĐỘ PHÒNG TRỌ THEO KHU VỰC</h4>
                        <div class="h-64">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                    <!-- Pie Chart -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h4 class="text-sm font-bold text-gray-500 mb-4">CƠ CẤU NGƯỜI DÙNG</h4>
                        <div class="h-64 flex justify-center">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. QUẢN LÝ TRỌNG SỐ THUẬT TOÁN (Dynamic Configuration) -->
            <section id="weights-config" class="scroll-mt-20">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-slate-800 p-5 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-white"><i class="fa-solid fa-code-branch text-yellow-400 mr-2"></i> Cấu hình Thuật toán Hybrid Matching</h3>
                            <p class="text-slate-300 text-xs mt-1">Điều chỉnh trọng số nền và hệ số ưu tiên. Áp dụng theo thời gian thực (Real-time) cho toàn bộ Client.</p>
                        </div>
                    </div>
                    
                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tiêu chí (Khóa)</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Trọng số nền (Base)</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Hệ số Ưu tiên (Boost)</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác AJAX</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($danhSachTrongSo ?? [] as $ts)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $ts->ten_tieu_chi }}</div>
                                        <div class="text-xs text-gray-500">Key: {{ $ts->ten_tieu_chi }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" id="base_{{ $ts->id }}" value="{{ $ts->trong_so_nen }}" step="0.5" min="0.1" max="10" 
                                               class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-semibold">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" id="boost_{{ $ts->id }}" value="{{ $ts->he_so_uu_tien }}" step="0.1" min="1.0" max="5.0" 
                                               class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-semibold">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button @click="updateWeight({{ $ts->id }})" class="bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white px-3 py-1.5 rounded-md text-xs font-bold transition-colors">
                                            <i class="fa-solid fa-floppy-disk mr-1"></i> Cập nhật
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Chưa có dữ liệu cấu hình thuật toán. Vui lòng chạy Migration.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- 4. QUẢN LÝ KIỂM DUYỆT (Verification Management) -->
            <section id="verification" class="scroll-mt-20">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-blue-600 p-5 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white"><i class="fa-solid fa-list-check mr-2"></i> Yêu cầu Phê duyệt Báo cáo Thực địa</h3>
                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-inner">{{ count($baoCaoChoDuyet ?? []) }} chờ duyệt</span>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-50 text-green-700 p-4 border-b border-green-100 text-sm font-medium flex items-center">
                            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <div class="p-0">
                        <ul class="divide-y divide-gray-100">
                            @forelse($baoCaoChoDuyet ?? [] as $baoCao)
                            @php $phong = $baoCao->phongTro; @endphp
                            <li class="p-6 hover:bg-slate-50 transition-colors flex flex-col md:flex-row gap-6 items-start md:items-center">
                                <div class="flex-shrink-0 w-24 h-24 bg-gray-200 rounded-lg overflow-hidden border border-gray-300">
                                    <img src="{{ isset($phong->anh_phong[0]) ? $phong->anh_phong[0] : 'https://via.placeholder.com/150?text=No+Image' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="text-lg font-bold text-gray-900">{{ $phong->tieu_de }}</h4>
                                        <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider border border-yellow-200">Báo cáo từ CTV</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2"><i class="fa-solid fa-location-dot text-red-500 mr-1"></i> {{ $phong->dia_chi_chi_tiet }}</p>
                                    
                                    <div class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm">
                                        <p class="font-bold text-blue-800 mb-1"><i class="fa-solid fa-user-check mr-1"></i> CTV Khảo sát: {{ $baoCao->congTacVien->ho_ten ?? 'Không rõ' }}</p>
                                        @php $chiTiet = is_array($baoCao->bao_cao_chi_tiet) ? $baoCao->bao_cao_chi_tiet : json_decode($baoCao->bao_cao_chi_tiet, true); @endphp
                                        <div class="flex gap-4 mt-2">
                                            <span class="{{ isset($chiTiet['phong_giong_anh']) && $chiTiet['phong_giong_anh'] ? 'text-emerald-600' : 'text-red-600' }}"><i class="fa-solid {{ isset($chiTiet['phong_giong_anh']) && $chiTiet['phong_giong_anh'] ? 'fa-check' : 'fa-xmark' }}"></i> Giống ảnh</span>
                                            <span class="{{ isset($chiTiet['nuoc_sach']) && $chiTiet['nuoc_sach'] ? 'text-emerald-600' : 'text-red-600' }}"><i class="fa-solid {{ isset($chiTiet['nuoc_sach']) && $chiTiet['nuoc_sach'] ? 'fa-check' : 'fa-xmark' }}"></i> Nước sạch</span>
                                            <span class="{{ isset($chiTiet['an_ninh']) && $chiTiet['an_ninh'] ? 'text-emerald-600' : 'text-red-600' }}"><i class="fa-solid {{ isset($chiTiet['an_ninh']) && $chiTiet['an_ninh'] ? 'fa-check' : 'fa-xmark' }}"></i> An ninh</span>
                                        </div>
                                        @if(!empty($chiTiet['ghi_chu']))
                                            <div class="mt-3 pt-3 border-t border-blue-100">
                                                <p class="font-bold text-gray-700 mb-1 flex items-center gap-1">
                                                    <i class="fa-solid fa-note-sticky text-yellow-500"></i> Ghi chú chi tiết của CTV:
                                                </p>
                                                <p class="text-gray-600 text-xs bg-white p-2.5 rounded-lg border border-gray-200 leading-relaxed font-medium shadow-sm">
                                                    {{ $chiTiet['ghi_chu'] }}
                                                </p>
                                            </div>
                                        @endif
                                        @if(isset($chiTiet['hinh_anh']) && is_array($chiTiet['hinh_anh']) && count($chiTiet['hinh_anh']) > 0)
                                            <div class="mt-3 pt-3 border-t border-blue-100">
                                                <p class="font-bold text-gray-700 mb-1.5 flex items-center gap-1"><i class="fa-solid fa-camera text-sky-500"></i> Ảnh chụp thực địa:</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($chiTiet['hinh_anh'] as $anh)
                                                        <div class="relative group cursor-zoom-in">
                                                            <img src="{{ $anh }}" class="w-16 h-16 rounded-lg object-cover border border-gray-200 transition-transform duration-200 group-hover:scale-105 shadow-sm"
                                                                 @click="openLightbox('{{ $anh }}')">
                                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity rounded-lg"></div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 w-full md:w-auto">
                                    <!-- Nút mở Modal xem Ảnh pháp lý -->
                                    <button @click="openModal({{ $phong->id }}, '{{ json_encode($phong->anh_phap_ly ?? []) }}')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-bold transition-colors w-full text-center">
                                        <i class="fa-regular fa-folder-open text-yellow-500 mr-1"></i> Hồ sơ Pháp lý
                                    </button>
                                    
                                    <div class="flex gap-2 w-full">
                                        <form action="{{ route('admin.room.approve', $baoCao->id) }}" method="POST" class="w-1/2">
                                            @csrf
                                            <button type="submit" class="bg-emerald-600 text-white hover:bg-emerald-700 w-full px-2 py-2 rounded-lg text-sm font-bold transition-all shadow-sm">
                                                <i class="fa-solid fa-check mr-1"></i> Duyệt
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.room.reject', $baoCao->id) }}" method="POST" class="w-1/2">
                                            @csrf
                                            <button type="submit" class="bg-red-500 text-white hover:bg-red-600 w-full px-2 py-2 rounded-lg text-sm font-bold transition-all shadow-sm" onclick="return confirm('Bạn có chắc chắn muốn từ chối báo cáo này và giáng cấp phòng?');">
                                                <i class="fa-solid fa-xmark mr-1"></i> Từ chối
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="p-10 text-center text-gray-500">
                                <i class="fa-solid fa-clipboard-check text-4xl text-emerald-300 mb-3"></i>
                                <p class="font-medium text-lg">Tuyệt vời! Không có phòng nào đang chờ duyệt.</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- 5. MODAL XEM ẢNH PHÁP LÝ (Alpine.js) -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="isModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="isModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa-solid fa-file-contract text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Hồ sơ Giấy tờ Pháp lý</h3>
                            <div class="mt-2 text-sm text-gray-500">Sổ đỏ, Hợp đồng thuê nhà, CCCD chủ trọ (JSONB Data). Hãy đối chiếu kỹ trước khi Duyệt.</div>
                        </div>
                    </div>
                </div>
                <!-- Images Container -->
                <div class="bg-gray-50 p-6 flex flex-wrap gap-4 justify-center max-h-96 overflow-y-auto" id="legal-images-container">
                    <template x-for="img in currentLegalImages">
                        <img :src="img" class="max-w-xs rounded shadow-sm border border-gray-200">
                    </template>
                    <div x-show="currentLegalImages.length === 0" class="text-gray-400 font-medium italic">Không có giấy tờ đính kèm.</div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" @click="isModalOpen = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Lightbox Modal for Field Photos -->
    <div x-show="isLightboxOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black bg-opacity-90" @click="isLightboxOpen = false">
        <button class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <img :src="lightboxImage" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js & Alpine.js & Toastify -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminDashboard', () => ({
            isModalOpen: false,
            currentLegalImages: [],
            lightboxImage: '',
            isLightboxOpen: false,
            openLightbox(url) {
                this.lightboxImage = url;
                this.isLightboxOpen = true;
            },
            
            // Xử lý Modal hiển thị JSONB ảnh
            openModal(roomId, imagesJsonString) {
                try {
                    // Parse chuỗi JSON từ CSDL
                    this.currentLegalImages = JSON.parse(imagesJsonString || '[]');
                } catch(e) {
                    this.currentLegalImages = [];
                }
                this.isModalOpen = true;
            },

            // Xử lý AJAX Cập nhật Trọng số (Hybrid Weighted Scoring Configuration)
            async updateWeight(id) {
                const baseVal = document.getElementById(`base_${id}`).value;
                const boostVal = document.getElementById(`boost_${id}`).value;
                
                // Giả lập CSRF Token (Thường nằm trong meta tag)
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                try {
                    const response = await fetch('/api/admin/weights/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            id: id,
                            trong_so_nen: parseFloat(baseVal),
                            he_so_uu_tien: parseFloat(boostVal)
                        })
                    });

                    const data = await response.json();
                    
                    if(response.ok) {
                        Toastify({
                            text: data.message || "Cập nhật thành công!",
                            duration: 3000,
                            close: true,
                            gravity: "top", position: "right",
                            backgroundColor: "linear-gradient(to right, #10b981, #059669)",
                        }).showToast();
                    } else {
                        throw new Error(data.message || 'Lỗi dữ liệu đầu vào');
                    }
                } catch(err) {
                    Toastify({
                        text: "Lỗi: " + err.message,
                        duration: 3000,
                        backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                    }).showToast();
                }
            }
        }));
    });

    // Khoi tao Chart.js sau khi DOM load
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            // Lấy dữ liệu thống kê qua API
            const response = await fetch('/api/admin/stats');
            const statsData = await response.json();

            // 1. Vẽ Bar Chart (Mật độ phòng theo khu vực)
            const ctxBar = document.getElementById('barChart').getContext('2d');
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: statsData.rooms.labels,
                    datasets: [{
                        label: 'Số lượng phòng',
                        data: statsData.rooms.data,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    },
                    plugins: { legend: { display: false } }
                }
            });

            // 2. Vẽ Pie Chart (Tỷ lệ người dùng)
            const ctxPie = document.getElementById('pieChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: statsData.users.labels,
                    datasets: [{
                        data: statsData.users.data,
                        backgroundColor: [
                            '#3b82f6', // Xanh blue
                            '#10b981', // Xanh emerald
                            '#f59e0b', // Vàng amber
                            '#8b5cf6'  // Tím violet
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { family: 'Inter', weight: 'bold' } } }
                    },
                    cutout: '65%' // Biến thành hình Doughnut (Vòng xuyến) cho hiện đại
                }
            });
        } catch(e) {
            console.error("Không lấy được dữ liệu Chart: ", e);
        }
    });
</script>
@endpush
