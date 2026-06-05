@extends('layouts.app')

@section('title', 'Kết quả gợi ý thông minh - EasyM')

@push('styles')
<!-- Nhúng Leaflet CSS qua CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
    /* Tuỳ chỉnh thanh cuộn cho Sidebar để trông hiện đại hơn */
    #sidebar::-webkit-scrollbar { width: 6px; }
    #sidebar::-webkit-scrollbar-track { background: #f1f1f1; }
    #sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    #sidebar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* Hiệu ứng Active cho Card khi được chọn */
    .card-active {
        border-color: #3b82f6 !important; /* Xanh dương của EasyM */
        background-color: #eff6ff !important; /* Xanh nhạt */
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.2), 0 4px 6px -2px rgba(59, 130, 246, 0.1) !important;
    }
</style>
@endpush

@section('content')
<!-- Split View Layout -->
<div class="flex flex-col md:flex-row h-[calc(100vh-64px)] bg-white overflow-hidden relative">
    
    <!-- Nút Toggle Mobile (Chỉ hiện trên điện thoại) -->
    <div class="md:hidden p-4 bg-white border-b flex justify-between items-center z-20 shadow-sm relative">
        <h2 class="font-bold text-gray-800 text-lg">Kết quả gợi ý</h2>
        <button id="mobile-toggle-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition-colors shadow-md shadow-blue-200">
            Xem bản đồ GIS
        </button>
    </div>

    <!-- SIDEBAR TRÁI: Danh sách Card (40%) -->
    <div id="sidebar" class="w-full md:w-[40%] lg:w-[35%] h-full bg-gray-50/50 overflow-y-auto flex flex-col transition-all duration-300 relative z-10 border-r border-gray-200">
        <!-- Header Sidebar -->
        <div class="p-6 sticky top-0 bg-white/80 backdrop-blur-md z-20 border-b border-gray-100 shadow-sm">
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Gợi ý thông minh</h1>
            <div class="flex items-center gap-2 mt-2 mb-4">
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <p class="text-gray-600 text-sm">Tìm thấy <span id="room-count" class="font-bold text-blue-600 text-base">{{ count($ds_goi_y ?? []) }}</span> phòng phù hợp</p>
            </div>

            <!-- Thanh Lọc (Filter Bar) -->
            <form id="filter-form" onsubmit="return false;" class="flex flex-col gap-3 text-sm">
                <!-- Trick to prevent implicit form submit on Enter -->
                <input type="text" style="display:none">

                <!-- Thanh tìm kiếm địa điểm -->
                <div class="flex gap-2">
                    <input type="text" id="diadiem" placeholder="Nhập địa điểm, trường đại học..." class="flex-1 bg-gray-50 border border-gray-200 text-gray-700 py-2 px-3 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <button type="button" id="btn-search-location" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors shadow-sm">
                        Tìm
                    </button>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <select name="ban_kinh" class="bg-gray-50 border border-gray-200 text-gray-700 py-1.5 px-3 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <option value="2000">Bán kính: 2km</option>
                        <option value="5000">Bán kính: 5km</option>
                        <option value="10000">Bán kính: 10km</option>
                        <option value="50000">Bán kính: 50km</option>
                        <option value="99999999">Toàn quốc</option>
                    </select>
                    <select name="gia" class="bg-gray-50 border border-gray-200 text-gray-700 py-1.5 px-3 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <option value="">Giá: Tất cả</option>
                        <option value="0-2000000">Dưới 2 triệu</option>
                        <option value="2000000-5000000">2 - 5 triệu</option>
                        <option value="5000000-99999999">Trên 5 triệu</option>
                    </select>

                    <select name="dien_tich" class="bg-gray-50 border border-gray-200 text-gray-700 py-1.5 px-3 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <option value="">Diện tích: Tất cả</option>
                        <option value="0-20">Dưới 20m²</option>
                        <option value="20-40">20 - 40m²</option>
                        <option value="40-999">Trên 40m²</option>
                    </select>

                    <select name="gioi_tinh" class="bg-gray-50 border border-gray-200 text-gray-700 py-1.5 px-3 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                        <option value="">Giới tính: Tất cả</option>
                        <option value="Nam">Chỉ Nam</option>
                        <option value="Nu">Chỉ Nữ</option>
                    </select>

                    <label class="flex items-center gap-2 cursor-pointer bg-blue-50 text-blue-700 border border-blue-200 py-1.5 px-3 rounded-lg hover:bg-blue-100 transition-colors">
                        <input type="checkbox" name="chi_xac_thuc" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <span class="font-medium">Đã xác thực</span>
                    </label>
                </div>
            </form>
        </div>

        <!-- Danh sách Phòng trọ -->
        <div class="p-6 space-y-5" id="room-list">
            
            <!-- Skeleton Loading State -->
            <div id="loading-skeleton" class="space-y-5">
                @for($i = 0; $i < 3; $i++)
                <div class="animate-pulse flex space-x-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="rounded-full bg-slate-200 h-16 w-16 flex-shrink-0"></div>
                    <div class="flex-1 space-y-4 py-1">
                        <div class="h-4 bg-slate-200 rounded w-3/4"></div>
                        <div class="space-y-2">
                            <div class="h-4 bg-slate-200 rounded"></div>
                            <div class="h-4 bg-slate-200 rounded w-5/6"></div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            <!-- Render Cards -->
            <div id="cards-container">
                @include('pro_search.partials.room_list', ['ds_goi_y' => $ds_goi_y ?? []])
            </div>
        </div>
    </div>

    <!-- BẢN ĐỒ PHẢI: Leaflet Map (60%) -->
    <div id="map-container" class="w-full md:w-[60%] lg:w-[65%] h-full relative z-0">
        <!-- Vùng render map -->
        <div id="map" class="w-full h-full bg-gray-200"></div>
        
        <!-- Nút định vị lại -->
        <button id="recenter-btn" class="absolute bottom-6 right-6 z-[400] bg-white p-3 rounded-full shadow-lg hover:bg-gray-50 transition-colors border border-gray-200 text-gray-700" title="Về vị trí trung tâm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<!-- Nhúng Leaflet JS qua CDN -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
/**
 * Truyền dữ liệu an toàn từ Blade sang biến Javascript
 * Sử dụng \Illuminate\Support\Js::from() hoặc hàm json_encode
 */
let roomsData = {!! json_encode($ds_goi_y ?? []) !!};

document.addEventListener('DOMContentLoaded', function() {
    // 1. Cấu hình cơ bản (Tọa độ ĐH Lâm Nghiệp)
    const VNUF_LAT = 20.941;
    const VNUF_LNG = 105.558;
    const DEFAULT_ZOOM = 15;

    // Tắt Skeleton Loading (Simulate delay nếu cần)
    setTimeout(() => {
        const skeleton = document.getElementById('loading-skeleton');
        if(skeleton) skeleton.style.display = 'none';
    }, 500);

    // 2. Khởi tạo Bản đồ Leaflet
    const map = L.map('map', {
        zoomControl: false // Tắt nút zoom mặc định để tự custom vị trí
    }).setView([VNUF_LAT, VNUF_LNG], DEFAULT_ZOOM);

    // Thêm nút Zoom góc trên phải
    L.control.zoom({ position: 'topright' }).addTo(map);

    // Lớp Bản đồ chuẩn từ OpenStreetMap
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // 3. Khai báo các Icon Marker (Bình thường vs Nổi bật)
    const defaultIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
    });

    const activeIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [30, 48], // Phóng to nhẹ khi active
        iconAnchor: [15, 48],
        popupAnchor: [1, -38],
    });

    // 4. Vẽ Marker và thiết lập tương tác hai chiều
    let markersMap = {}; // Lưu trữ marker theo ID phòng

    function renderMarkers(data) {
        // Xóa marker cũ
        Object.values(markersMap).forEach(m => map.removeLayer(m));
        markersMap = {};

        data.forEach(room => {
            // Giả lập tọa độ nếu dữ liệu vi_tri bị null do chưa map PostGIS
            const lat = room.lat || (VNUF_LAT + (Math.random() - 0.5) * 0.02);
            const lng = room.lng || (VNUF_LNG + (Math.random() - 0.5) * 0.02);

            // Cắm Marker
            const marker = L.marker([lat, lng], {icon: defaultIcon, title: room.tieu_de}).addTo(map);
            
            // Thiết kế Popup HTML sang trọng
            const priceFmt = new Intl.NumberFormat('vi-VN').format(room.gia_phong) + ' đ';
            const popupHTML = `
                <div class="w-56 font-sans">
                    <div class="h-28 bg-gray-200 rounded-t-lg mb-3 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=400')"></div>
                    <h4 class="font-bold text-gray-900 text-[15px] leading-tight mb-1">${room.tieu_de}</h4>
                    <p class="text-blue-600 font-extrabold text-base mb-2">${priceFmt}</p>
                    <div class="flex gap-2">
                        <a href="/phong-tro/${room.id}" target="_blank" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-xs font-bold transition-colors text-center inline-block">Xem chi tiết</a>
                    </div>
                </div>
            `;
            
            marker.bindPopup(popupHTML, {
                className: 'custom-popup',
                minWidth: 224
            });

            markersMap[room.id] = marker;

            // Tương tác 1: Click Marker -> Đổi màu Marker & Cuộn Card tương ứng trên Sidebar
            marker.on('click', function() {
                highlightRoom(room.id);
                
                // Tự động cuộn Sidebar đến Card
                const cardEl = document.querySelector(`.room-card[data-id="${room.id}"]`);
                if(cardEl) {
                    cardEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        });
        bindCardEvents();
    }

    renderMarkers(roomsData);

    // Xử lý Lọc & Tìm kiếm địa điểm (AJAX)
    const filterForm = document.getElementById('filter-form');
    let currentLat = VNUF_LAT;
    let currentLng = VNUF_LNG;

    if (filterForm) {
        // Xử lý khi nhấn nút TÌM địa điểm
        const btnSearch = document.getElementById('btn-search-location');
        if (btnSearch) {
            btnSearch.addEventListener('click', async function() {
                const diaDiem = document.getElementById('diadiem').value.trim();
                if (!diaDiem) {
                    currentLat = null;
                    currentLng = null;
                    filterForm.dispatchEvent(new Event('change')); // Bỏ lọc vị trí
                    return;
                }
                
                // Hiển thị trạng thái loading ở nút Tìm
                btnSearch.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                btnSearch.disabled = true;
                
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(diaDiem + ', Việt Nam')}`);
                    const data = await res.json();
                    if (data && data.length > 0) {
                        currentLat = data[0].lat;
                        currentLng = data[0].lon;
                        
                        // Cập nhật lại bản đồ tới vị trí mới
                        map.setView([currentLat, currentLng], 14);
                    } else {
                        alert('Không tìm thấy tọa độ địa điểm này! Vui lòng nhập rõ hơn.');
                    }
                } catch(e) {
                    console.error("Lỗi Geocode", e);
                }
                
                btnSearch.innerHTML = 'Tìm';
                btnSearch.disabled = false;
                // Kích hoạt load lại danh sách phòng sau khi có tọa độ
                filterForm.dispatchEvent(new Event('change')); 
            });

            // Bắt sự kiện nhấn Enter trong ô nhập liệu địa điểm
            document.getElementById('diadiem').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    btnSearch.click();
                }
            });
        }

        // Xử lý khi thay đổi các thẻ Select/Checkbox
        filterForm.addEventListener('change', function(e) {
            // Tránh trigger khi gõ phím vào ô input địa điểm
            if (e.target && e.target.id === 'diadiem') return;

            const formData = new FormData(filterForm);
            const params = new URLSearchParams();
            
            // Nếu đã tìm thấy tọa độ thì truyền lên server
            if (currentLat && currentLng) {
                params.append('lat', currentLat);
                params.append('lng', currentLng);
            }
            
            if (formData.get('ban_kinh')) params.append('ban_kinh', formData.get('ban_kinh'));
            if (formData.get('gia')) {
                const parts = formData.get('gia').split('-');
                params.append('gia_min', parts[0]);
                params.append('gia_max', parts[1]);
            }
            if (formData.get('dien_tich')) {
                const parts = formData.get('dien_tich').split('-');
                params.append('dien_tich_min', parts[0]);
                params.append('dien_tich_max', parts[1]);
            }
            if (formData.get('gioi_tinh')) params.append('gioi_tinh', formData.get('gioi_tinh'));
            if (formData.get('chi_xac_thuc')) params.append('chi_xac_thuc', '1');

            // Hiển thị loading
            document.getElementById('cards-container').innerHTML = '<div class="text-center py-5 text-gray-500"><i class="fa-solid fa-circle-notch fa-spin text-2xl text-blue-500 mb-2"></i><br>Đang tìm kiếm...</div>';

            fetch('/tim-kiem-goi-y?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    roomsData = res.data;
                    document.getElementById('cards-container').innerHTML = res.html;
                    document.getElementById('room-count').innerText = roomsData.length;
                    renderMarkers(roomsData);
                }
            });
        });
    }

    // Tương tác 2: Hover/Click Card -> Nhảy Marker & Đổi màu Card
    function bindCardEvents() {
        const cards = document.querySelectorAll('.room-card');
        cards.forEach(card => {
            const roomId = card.getAttribute('data-id');
            const lat = parseFloat(card.getAttribute('data-lat'));
            const lng = parseFloat(card.getAttribute('data-lng'));

            card.addEventListener('click', function() {
                map.flyTo([lat, lng], 17, { animate: true, duration: 1.5 });
                if(markersMap[roomId]) markersMap[roomId].openPopup();
                highlightRoom(roomId);
            });

            card.addEventListener('mouseenter', function() {
                if(markersMap[roomId]) markersMap[roomId].setIcon(activeIcon);
            });

            card.addEventListener('mouseleave', function() {
                if(markersMap[roomId] && !markersMap[roomId].isPopupOpen()) {
                    markersMap[roomId].setIcon(defaultIcon);
                }
            });
        });
    }



    // Reset lại toàn bộ màu khi tắt popup
    map.on('popupclose', function() {
        highlightRoom(null); // Gỡ active toàn bộ
    });

    // Nút định vị lại trung tâm
    document.getElementById('recenter-btn').addEventListener('click', () => {
        map.flyTo([VNUF_LAT, VNUF_LNG], DEFAULT_ZOOM);
    });

    // Hàm xử lý Highlight dùng chung
    function highlightRoom(roomId) {
        // Gỡ active tất cả thẻ Card
        document.querySelectorAll('.room-card').forEach(c => c.classList.remove('card-active'));
        
        // Reset tất cả Marker về màu xanh
        Object.values(markersMap).forEach(m => m.setIcon(defaultIcon));

        // Nếu có roomId truyền vào -> Active nó lên
        if(roomId) {
            const targetCard = document.querySelector(`.room-card[data-id="${roomId}"]`);
            if(targetCard) targetCard.classList.add('card-active');
            
            if(markersMap[roomId]) markersMap[roomId].setIcon(activeIcon);
        }
    }

    // 5. Logic Responsive: Chuyển đổi List/Map trên Mobile
    const mobileBtn = document.getElementById('mobile-toggle-btn');
    const sidebarEl = document.getElementById('sidebar');
    const mapContainerEl = document.getElementById('map-container');
    let isMapVisible = false;

    if(mobileBtn) {
        // Mặc định giấu map trên màn hình nhỏ
        if(window.innerWidth < 768) {
            mapContainerEl.classList.add('hidden', 'absolute', 'inset-0', 'z-50');
        }

        mobileBtn.addEventListener('click', function() {
            isMapVisible = !isMapVisible;
            
            if(isMapVisible) {
                // Mở Map toàn màn hình
                mapContainerEl.classList.remove('hidden');
                sidebarEl.classList.add('hidden');
                mobileBtn.innerHTML = `
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        Xem danh sách
                    </span>
                `;
                // Kích hoạt Leaflet tính toán lại kích thước hiển thị
                setTimeout(() => map.invalidateSize(), 100);
            } else {
                // Về lại List
                mapContainerEl.classList.add('hidden');
                sidebarEl.classList.remove('hidden');
                mobileBtn.innerHTML = `
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        Xem bản đồ GIS
                    </span>
                `;
            }
        });
    }
});
</script>

<style>
    /* Chỉnh lại style cho popup Leaflet để loại bỏ viền trắng mặc định */
    .custom-popup .leaflet-popup-content-wrapper {
        padding: 0;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }
    .custom-popup .leaflet-popup-content {
        margin: 0;
        padding: 12px;
    }
    /* Đảm bảo chữ "Xem chi tiết" không bị mờ (trùng màu link mặc định của Leaflet) */
    .custom-popup .leaflet-popup-content a {
        color: #ffffff !important;
    }
    .custom-popup .leaflet-popup-tip-container {
        display: none; /* Giấu mũi tên nhọn bên dưới cho hiện đại */
    }
</style>
@endpush
