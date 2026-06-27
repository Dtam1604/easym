@extends('layouts.app')

@section('title', 'Kết quả gợi ý thông minh - EasyM')

@push('styles')
    <!-- Nhúng Leaflet CSS qua CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: #eef3f8;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #cbd8e6;
            border-radius: 10px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #9fb2c7;
        }

        .card-active {
            border-color: var(--easym-accent) !important;
            background: #f8fbff !important;
            box-shadow: 0 18px 38px rgb(23 105 224 / 0.16) !important;
        }

        .leaflet-control-zoom a {
            border-radius: 999px !important;
            border: 1px solid #dbe4ee !important;
            color: #273244 !important;
            box-shadow: 0 10px 30px rgb(15 23 42 / 0.10);
            margin-bottom: 8px;
        }

        .leaflet-control-zoom {
            border: 0 !important;
        }
    </style>
@endpush

@section('content')
    <!-- Split View Layout -->
    <div class="relative flex h-[calc(100dvh-72px)] flex-col overflow-hidden bg-slate-50 md:flex-row">

        <!-- Nút Toggle Mobile (Chỉ hiện trên điện thoại) -->
        <div class="relative z-20 flex items-center justify-between border-b border-slate-200 bg-white p-4 shadow-sm md:hidden">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-blue-600">Tìm phòng</p>
                <h2 class="text-lg font-black text-slate-950">Kết quả gợi ý</h2>
            </div>
            <button id="mobile-toggle-btn"
                class="easym-btn easym-btn-primary px-4 py-2.5 text-sm">
                <i class="fa-solid fa-map-location-dot"></i>
                Bản đồ
            </button>
        </div>

        <!-- SIDEBAR TRÁI: Danh sách Card (40%) -->
        <div id="sidebar"
            class="relative z-10 flex h-full w-full flex-col overflow-y-auto border-r border-slate-200 bg-slate-50 transition-all duration-300 md:w-[42%] lg:w-[38%] xl:w-[34%]">
            <!-- Header Sidebar -->
            <div class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 p-5 shadow-sm backdrop-blur-md">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-blue-600">EasyM search</p>
                        <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-950">Tìm phòng phù hợp</h1>
                        <p class="mt-1 text-xs font-medium text-slate-500">
                            <span id="room-count" class="font-black text-blue-600">{{ count($ds_goi_y ?? []) }}</span>
                            phòng trong phạm vi gợi ý
                        </p>
                    </div>
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full border border-blue-100 bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </span>
                </div>

                <!-- Thanh Lọc (Filter Bar) -->
                <form id="filter-form" onsubmit="return false;" class="flex flex-col gap-3 text-sm">
                    <!-- Trick to prevent implicit form submit on Enter -->
                    <input type="text" style="display:none">

                    <!-- Thanh tìm kiếm địa điểm -->
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" id="diadiem" placeholder="Nhập trường đại học, địa điểm..."
                                class="w-full py-3 pl-10 pr-3 text-sm font-semibold">
                        </div>
                        <button type="button" id="btn-search-location"
                            class="easym-btn easym-btn-primary px-4 py-3 text-sm">
                            Tìm
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <select name="ban_kinh"
                            class="cursor-pointer px-3 py-2.5 text-xs font-bold">
                            <option value="2000">Bán kính: 2 km</option>
                            <option value="5000">Bán kính: 5 km</option>
                            <option value="10000">Bán kính: 10 km</option>
                            <option value="50000">Bán kính: 50 km</option>
                            <option value="99999999">Toàn quốc</option>
                        </select>
                        <select name="gia"
                            class="cursor-pointer px-3 py-2.5 text-xs font-bold">
                            <option value="">Giá: Tất cả</option>
                            <option value="0-2000000">Dưới 2 triệu</option>
                            <option value="2000000-5000000">2 - 5 triệu</option>
                            <option value="5000000-99999999">Trên 5 triệu</option>
                        </select>

                        <select name="dien_tich"
                            class="cursor-pointer px-3 py-2.5 text-xs font-bold">
                            <option value="">Diện tích: Tất cả</option>
                            <option value="0-20">Dưới 20 m²</option>
                            <option value="20-40">20 - 40 m²</option>
                            <option value="40-999">Trên 40 m²</option>
                        </select>

                        <label
                            class="flex cursor-pointer items-center justify-center gap-2 rounded-[14px] border border-blue-100 bg-blue-50/70 px-3 py-2.5 text-blue-700 transition-colors hover:bg-blue-100/70">
                            <input type="checkbox" name="chi_xac_thuc" value="1"
                                class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-100">
                            <span class="text-xs font-bold">Đã xác thực</span>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Danh sách Phòng trọ -->
            <div class="space-y-4 p-4 sm:p-5" id="room-list">

                <!-- Skeleton Loading State -->
                <div id="loading-skeleton" class="space-y-5">
                    @for($i = 0; $i < 3; $i++)
                        <div class="flex animate-pulse gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="h-24 w-28 shrink-0 rounded-2xl bg-slate-200"></div>
                            <div class="flex-1 space-y-4 py-1">
                                <div class="h-4 w-3/4 rounded bg-slate-200"></div>
                                <div class="space-y-2">
                                    <div class="h-4 rounded bg-slate-200"></div>
                                    <div class="h-4 w-5/6 rounded bg-slate-200"></div>
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
        <div id="map-container" class="relative z-0 h-full w-full md:w-[58%] lg:w-[62%] xl:w-[66%]">
            <!-- Vùng render map -->
            <div id="map" class="w-full h-full bg-zinc-100"></div>

            <div class="pointer-events-none absolute left-4 top-4 z-[400] hidden rounded-2xl border border-slate-200 bg-white/95 px-4 py-3 shadow-lg backdrop-blur-md md:block">
                <p class="text-xs font-extrabold uppercase tracking-[0.14em] text-blue-600">Bản đồ phòng</p>
                <p class="mt-1 text-sm font-bold text-slate-700">Chọn marker hoặc card để xem chi tiết</p>
            </div>

            <!-- Nút định vị lại -->
            <button id="recenter-btn"
                class="absolute bottom-6 right-6 z-[400] grid h-12 w-12 place-items-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-lg transition-all hover:bg-slate-50 active:scale-95"
                title="Về vị trí trung tâm">
                <i class="fa-solid fa-location-crosshairs"></i>
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
         */
        let roomsData = {!! json_encode($ds_goi_y ?? []) !!};

        document.addEventListener('DOMContentLoaded', function () {
            // 1. Cấu hình cơ bản (Tọa độ ĐH Lâm Nghiệp)
            const VNUF_LAT = 20.941;
            const VNUF_LNG = 105.558;
            const DEFAULT_ZOOM = 15;

            // Tắt Skeleton Loading
            setTimeout(() => {
                const skeleton = document.getElementById('loading-skeleton');
                if (skeleton) skeleton.style.display = 'none';
            }, 300);

            // 2. Khởi tạo Bản đồ Leaflet
            const map = L.map('map', {
                zoomControl: false 
            }).setView([VNUF_LAT, VNUF_LNG], DEFAULT_ZOOM);

            // Thêm nút Zoom góc trên phải
            L.control.zoom({ position: 'topright' }).addTo(map);

            // Lớp Bản đồ chuẩn từ CartoDB
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
                iconSize: [30, 48], 
                iconAnchor: [15, 48],
                popupAnchor: [1, -38],
            });

            // 4. Vẽ Marker và thiết lập tương tác hai chiều
            let markersMap = {}; 

            function renderMarkers(data) {
                // Xóa marker cũ
                Object.values(markersMap).forEach(m => map.removeLayer(m));
                markersMap = {};

                data.forEach(room => {
                    const lat = room.lat || (VNUF_LAT + (Math.random() - 0.5) * 0.02);
                    const lng = room.lng || (VNUF_LNG + (Math.random() - 0.5) * 0.02);

                    const marker = L.marker([lat, lng], { icon: defaultIcon, title: room.tieu_de }).addTo(map);

                    const priceFmt = new Intl.NumberFormat('vi-VN').format(room.gia_phong) + ' đ';
                    const popupHTML = `
                    <div class="w-56 font-sans">
                        <div class="h-28 bg-zinc-100 rounded-t-lg mb-3 bg-cover bg-center border border-zinc-100" style="background-image: url('https://picsum.photos/seed/room-${room.id}/400/300')"></div>
                        <h4 class="font-bold text-zinc-900 text-sm leading-tight mb-1">${room.tieu_de}</h4>
                        <p class="text-blue-600 font-extrabold text-sm mb-3">${priceFmt}</p>
                        <div class="flex gap-2">
                            <a href="/phong-tro/${room.id}" target="_blank" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl text-xs font-bold transition-all text-center inline-block shadow-sm">Xem chi tiết</a>
                        </div>
                    </div>
                `;

                    marker.bindPopup(popupHTML, {
                        className: 'custom-popup',
                        minWidth: 224
                    });

                    markersMap[room.id] = marker;

                    marker.on('click', function () {
                        highlightRoom(room.id);

                        const cardEl = document.querySelector(`.room-card[data-id="${room.id}"]`);
                        if (cardEl) {
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
                const btnSearch = document.getElementById('btn-search-location');
                if (btnSearch) {
                    btnSearch.addEventListener('click', async function () {
                        const diaDiem = document.getElementById('diadiem').value.trim();
                        if (!diaDiem) {
                            currentLat = null;
                            currentLng = null;
                            filterForm.dispatchEvent(new Event('change')); 
                            return;
                        }

                        btnSearch.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                        btnSearch.disabled = true;

                        try {
                            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(diaDiem + ', Việt Nam')}`);
                            const data = await res.json();
                            if (data && data.length > 0) {
                                currentLat = data[0].lat;
                                currentLng = data[0].lon;

                                map.setView([currentLat, currentLng], 14);
                            } else {
                                alert('Không tìm thấy tọa độ địa điểm này! Vui lòng nhập rõ hơn.');
                            }
                        } catch (e) {
                            console.error("Lỗi Geocode", e);
                        }

                        btnSearch.innerHTML = 'Tìm';
                        btnSearch.disabled = false;
                        filterForm.dispatchEvent(new Event('change'));
                    });

                    document.getElementById('diadiem').addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            btnSearch.click();
                        }
                    });
                }

                filterForm.addEventListener('change', function (e) {
                    if (e.target && e.target.id === 'diadiem') return;

                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams();

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
                    if (formData.get('chi_xac_thuc')) params.append('chi_xac_thuc', '1');

                    document.getElementById('cards-container').innerHTML = '<div class="rounded-2xl border border-slate-200 bg-white py-10 text-center text-slate-500"><i class="fa-solid fa-circle-notch fa-spin mb-2 text-xl text-blue-600"></i><br><span class="text-xs font-bold">Đang tải kết quả...</span></div>';

                    fetch('/tim-kiem-goi-y?' + params.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                roomsData = res.data;
                                document.getElementById('cards-container').innerHTML = res.html;
                                document.getElementById('room-count').innerText = roomsData.length;
                                renderMarkers(roomsData);
                            }
                        });
                });
            }

            function bindCardEvents() {
                const cards = document.querySelectorAll('.room-card');
                cards.forEach(card => {
                    const roomId = card.getAttribute('data-id');
                    const lat = parseFloat(card.getAttribute('data-lat'));
                    const lng = parseFloat(card.getAttribute('data-lng'));

                    card.addEventListener('click', function () {
                        map.flyTo([lat, lng], 17, { animate: true, duration: 1.5 });
                        if (markersMap[roomId]) markersMap[roomId].openPopup();
                        highlightRoom(roomId);
                    });

                    card.addEventListener('mouseenter', function () {
                        if (markersMap[roomId]) markersMap[roomId].setIcon(activeIcon);
                    });

                    card.addEventListener('mouseleave', function () {
                        if (markersMap[roomId] && !markersMap[roomId].isPopupOpen()) {
                            markersMap[roomId].setIcon(defaultIcon);
                        }
                    });
                });
            }

            map.on('popupclose', function () {
                highlightRoom(null); 
            });

            document.getElementById('recenter-btn').addEventListener('click', () => {
                map.flyTo([VNUF_LAT, VNUF_LNG], DEFAULT_ZOOM);
            });

            function highlightRoom(roomId) {
                document.querySelectorAll('.room-card').forEach(c => c.classList.remove('card-active'));
                Object.values(markersMap).forEach(m => m.setIcon(defaultIcon));

                if (roomId) {
                    const targetCard = document.querySelector(`.room-card[data-id="${roomId}"]`);
                    if (targetCard) targetCard.classList.add('card-active');
                    if (markersMap[roomId]) markersMap[roomId].setIcon(activeIcon);
                }
            }

            const mobileBtn = document.getElementById('mobile-toggle-btn');
            const sidebarEl = document.getElementById('sidebar');
            const mapContainerEl = document.getElementById('map-container');
            let isMapVisible = false;

            if (mobileBtn) {
                if (window.innerWidth < 768) {
                    mapContainerEl.classList.add('hidden', 'absolute', 'inset-0', 'z-50');
                }

                mobileBtn.addEventListener('click', function () {
                    isMapVisible = !isMapVisible;

                    if (isMapVisible) {
                        mapContainerEl.classList.remove('hidden');
                        sidebarEl.classList.add('hidden');
                        mobileBtn.innerHTML = `
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-list"></i>
                            Xem danh sách
                        </span>
                    `;
                        setTimeout(() => map.invalidateSize(), 100);
                    } else {
                        mapContainerEl.classList.add('hidden');
                        sidebarEl.classList.remove('hidden');
                        mobileBtn.innerHTML = `
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-map-location-dot"></i>
                            Xem bản đồ GIS
                        </span>
                    `;
                    }
                });
            }
        });
    </script>

    <style>
        .custom-popup .leaflet-popup-content-wrapper {
            padding: 0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #e4e4e7;
        }

        .custom-popup .leaflet-popup-content {
            margin: 0;
            padding: 12px;
        }

        .custom-popup .leaflet-popup-content a {
            color: #ffffff !important;
        }

        .custom-popup .leaflet-popup-tip-container {
            display: none;
        }
    </style>
@endpush
