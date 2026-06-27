@extends('layouts.app')

@section('title', 'Tìm Bạn Ở Ghép Thông Minh - EasyM')

@push('styles')
<style>
    .group-connected:hover .group-connected-default { display: none; }
    .group-connected:hover .group-connected-hover { display: inline; }

    .roommate-cover {
        background:
            radial-gradient(circle at 20% 20%, rgb(23 105 224 / 0.18), transparent 32%),
            linear-gradient(135deg, #f8fbff 0%, #eef4fb 100%);
    }

    .match-ring {
        background: conic-gradient(var(--easym-accent) var(--match), #e6edf5 0);
    }

    .roommate-card:hover {
        transform: translateY(-4px);
    }
</style>
@endpush

@section('content')
<div class="min-h-[100dvh] bg-slate-50 py-8 sm:py-10">
    <div class="easym-shell">
        
        <!-- Header -->
        <div class="mb-6 grid gap-5 lg:grid-cols-[1fr_auto] lg:items-end">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600">Tìm bạn ở ghép</p>
                <h1 class="mt-3 max-w-3xl text-4xl font-black leading-tight tracking-tight text-slate-950 md:text-5xl">
                    Gợi ý người ở cùng phù hợp với nhịp sống của bạn
                </h1>
                <p class="mt-4 max-w-2xl text-sm leading-6 text-slate-600 md:text-base">
                    EasyM so sánh ngân sách, khu vực, tiện ích và thói quen sinh hoạt để xếp hạng các hồ sơ có độ phù hợp cao nhất.
                </p>
            </div>
            <a href="/khao-sat-loi-song" class="easym-btn easym-btn-secondary px-5 py-3 text-sm">
                <i class="fa-solid fa-sliders"></i>
                Cập nhật khảo sát
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('tim-ban.index') }}" class="flex flex-col gap-4">
                <div class="grid gap-3 lg:grid-cols-[1fr_180px_180px_220px_auto]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="tu_khoa" value="{{ request('tu_khoa') }}" placeholder="Tìm theo tên, trường học, nghề nghiệp..." class="w-full py-3.5 pl-11 pr-4 text-sm font-semibold">
                    </div>
                    
                    <div>
                        @php $reqGioiTinh = request('gioi_tinh', $nguoiDungHienTai->gioi_tinh ?? 'tat_ca'); @endphp
                        <select name="gioi_tinh" class="w-full px-4 py-3.5 text-sm font-semibold">
                            <option value="tat_ca" {{ $reqGioiTinh == 'tat_ca' ? 'selected' : '' }}>Giới tính: Tất cả</option>
                            <option value="nam" {{ $reqGioiTinh == 'nam' ? 'selected' : '' }}>Nam</option>
                            <option value="nu" {{ $reqGioiTinh == 'nu' ? 'selected' : '' }}>Nữ</option>
                        </select>
                    </div>

                    <div>
                        <select name="khoang_tuoi" class="w-full px-4 py-3.5 text-sm font-semibold">
                            <option value="">Độ tuổi: Tất cả</option>
                            <option value="18-22" {{ request('khoang_tuoi') == '18-22' ? 'selected' : '' }}>18 - 22 tuổi</option>
                            <option value="23-26" {{ request('khoang_tuoi') == '23-26' ? 'selected' : '' }}>23 - 26 tuổi</option>
                            <option value=">26" {{ request('khoang_tuoi') == '>26' ? 'selected' : '' }}>Trên 26 tuổi</option>
                        </select>
                    </div>

                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="thanh_pho" value="{{ request('thanh_pho') }}" placeholder="Khu vực, ví dụ Hà Nội" class="w-full py-3.5 pl-10 pr-4 text-sm font-semibold">
                    </div>
                    
                    <button type="submit" class="easym-btn easym-btn-primary px-6 py-3.5 text-sm">
                        <i class="fa-solid fa-filter"></i>
                        Áp dụng
                    </button>
                </div>
            </form>
        </div>

        <!-- Panel Lời mời chờ duyệt -->
        @if(isset($loiMoiChoDuyet) && count($loiMoiChoDuyet) > 0)
        <div class="mb-8 rounded-2xl border border-blue-100 bg-blue-50/70 p-5">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-black text-blue-950">
                <span class="grid h-8 w-8 place-items-center rounded-full bg-white text-blue-600"><i class="fa-solid fa-bell"></i></span>
                Lời mời kết nối chờ duyệt ({{ count($loiMoiChoDuyet) }})
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($loiMoiChoDuyet as $loiMoi)
                <div class="flex items-center justify-between rounded-2xl border border-blue-100 bg-white p-4 shadow-sm" id="invite-{{ $loiMoi->id }}">
                    <div class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity" title="Xem chi tiết" data-roommate='@json($loiMoi->nguoiGui->roommate_data)' onclick="openRoommateDetails(JSON.parse(this.getAttribute('data-roommate')))">
                        <img src="{{ $loiMoi->nguoiGui->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($loiMoi->nguoiGui->ho_ten).'&background=f3f7fb&color=1769e0' }}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $loiMoi->nguoiGui->ho_ten }}</p>
                            <p class="text-xs text-gray-500">{{ $loiMoi->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button data-roommate='@json($loiMoi->nguoiGui->roommate_data)' onclick="openRoommateDetails(JSON.parse(this.getAttribute('data-roommate')))" class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 flex items-center justify-center transition-colors" title="Xem thông tin người gửi">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                        <button onclick="handleInvite({{ $loiMoi->id }}, 'chap-nhan')" class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 hover:bg-emerald-200 flex items-center justify-center transition-colors" title="Đồng ý">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        <button onclick="handleInvite({{ $loiMoi->id }}, 'tu-choi')" class="w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition-colors" title="Từ chối">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(count($ds_goi_y) === 0)
        <!-- Empty State -->
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
            <div class="mx-auto mb-6 grid h-24 w-24 place-items-center rounded-full bg-blue-50 text-blue-500">
                <i class="fa-solid fa-user-group text-4xl"></i>
            </div>
            @if(empty($nguoiDungHienTai->khao_sat_loi_song))
                <h3 class="mb-2 text-2xl font-black text-slate-950">Bạn chưa thực hiện khảo sát lối sống</h3>
                <p class="mx-auto mb-6 max-w-md text-sm leading-6 text-slate-600">Hoàn thành khảo sát để EasyM có đủ dữ liệu tìm người ở ghép phù hợp.</p>
                <a href="/khao-sat-loi-song" class="easym-btn easym-btn-primary px-6 py-3 text-sm">
                    <i class="fa-solid fa-clipboard-list mr-2"></i> Thực hiện Khảo sát
                </a>
            @else
                <h3 class="mb-2 text-2xl font-black text-slate-950">Chưa tìm thấy người phù hợp</h3>
                <p class="mx-auto mb-6 max-w-md text-sm leading-6 text-slate-600">Hãy thử mở rộng tiêu chí hoặc cập nhật lại khảo sát lối sống.</p>
                <a href="/khao-sat-loi-song" class="easym-btn easym-btn-primary px-6 py-3 text-sm">
                    <i class="fa-solid fa-clipboard-list mr-2"></i> Làm lại Khảo sát
                </a>
            @endif
        </div>
        @else
        <!-- Lưới User Cards -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($ds_goi_y as $nguoi)
            <!-- User Card -->
            <div class="roommate-card group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-200 hover:border-blue-100 hover:shadow-xl">
                @php $trangThai = $nguoi->roommate_data['trang_thai_ket_noi'] ?? null; @endphp
                
                <!-- Clickable area to view full roommate details panel -->
                <div class="cursor-pointer flex-1 flex flex-col" data-roommate='@json($nguoi->roommate_data)' onclick="openRoommateDetails(JSON.parse(this.getAttribute('data-roommate')))">
                    <!-- Card Header (Cover & Avatar) -->
                    <div class="roommate-cover relative h-28">
                        <div class="absolute right-4 top-4 z-10 flex items-center gap-2 rounded-full border border-blue-100 bg-white px-3 py-1.5 shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                            <span class="text-sm font-black text-slate-900">{{ $nguoi->matching_percentage }}% khớp</span>
                        </div>
                        
                        <!-- Circular Progress bar SVG wrapper for Avatar -->
                        <div class="absolute -bottom-10 left-6">
                            <div class="match-ring grid h-24 w-24 place-items-center rounded-full p-1.5 shadow-md" style="--match: {{ $nguoi->matching_percentage }}%;">
                                <div class="h-full w-full rounded-full bg-white p-1.5">
                                <img src="{{ $nguoi->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($nguoi->ho_ten).'&background=random&size=150' }}" 
                                     alt="{{ $nguoi->ho_ten }}" 
                                     class="w-full h-full rounded-full object-cover">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="flex flex-grow flex-col px-6 pb-3 pt-14">
                        <h3 class="mb-1 text-xl font-black text-slate-950 transition-colors group-hover:text-blue-600">{{ $nguoi->ho_ten }}</h3>
                        <p class="mb-4 text-sm font-medium text-slate-500">
                            @if($nguoi->nam_sinh)
                                <i class="fa-solid fa-cake-candles mr-1 text-slate-400"></i> {{ date('Y') - $nguoi->nam_sinh }} tuổi
                            @endif
                            <span class="mx-1 text-slate-300">/</span>
                            <i class="fa-solid fa-briefcase mr-1 text-slate-400"></i> {{ $nguoi->nghe_nghiep ?? 'Chưa cập nhật' }}
                        </p>

                        <!-- Roommate Requirements Box -->
                        <div class="mb-5 space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left text-xs">
                            <div class="flex justify-between items-center">
                                <span class="flex items-center gap-1.5 font-semibold text-slate-500"><i class="fa-solid fa-money-bill-wave text-blue-500 text-sm"></i> Ngân sách</span>
                                <span class="text-sm font-black text-slate-900">{{ number_format($nguoi->tien_thue ?? $nguoi->khao_sat_loi_song['tien_thue'] ?? 2000000) }} đ/tháng</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="flex items-center gap-1.5 font-semibold text-slate-500"><i class="fa-solid fa-users text-blue-500 text-sm"></i> Tối đa</span>
                                <span class="text-sm font-black text-slate-900">{{ $nguoi->so_nguoi_to_da ?? $nguoi->khao_sat_loi_song['so_nguoi_to_da'] ?? 2 }} người</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="flex items-center gap-1.5 font-semibold text-slate-500"><i class="fa-solid fa-couch text-blue-500 text-sm"></i> Tiện ích</span>
                                <span class="max-w-[170px] truncate font-bold text-slate-700" title="@php
                                    $csvc = $nguoi->co_so_vat_chat ?? $nguoi->khao_sat_loi_song['co_so_vat_chat'] ?? [];
                                    $labels = [
                                        'dieu_hoa' => 'Điều hòa', 'tu_lanh' => 'Tủ lạnh', 'may_giat' => 'Máy giặt',
                                        'nong_lanh' => 'Nóng lạnh', 'wifi' => 'Wifi', 'ban_cong' => 'Ban công',
                                        've_sinh_khiep_kin' => 'Khép kín', 'bep_nau_an' => 'Bếp riêng'
                                    ];
                                    $displayList = array_map(fn($item) => $labels[$item] ?? $item, $csvc);
                                    echo implode(', ', $displayList);
                                @endphp">
                                    {{ empty($displayList) ? 'Không yêu cầu' : implode(', ', $displayList) }}
                                </span>
                            </div>
                            <div class="border-t border-slate-200 pt-2">
                                <span class="mb-1.5 flex items-center gap-1.5 text-slate-500 font-semibold"><i class="fa-solid fa-location-dot text-blue-500 text-sm"></i> Địa điểm và nhiệm kỳ</span>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $diaDiemNhiemKy = $nguoi->dia_diem_nhiem_ky ?? $nguoi->khao_sat_loi_song['dia_diem_nhiem_ky'] ?? [['dia_diem' => $nguoi->thanh_pho ?? 'Hà Nội', 'nhiem_ky' => 12]];
                                    @endphp
                                    @foreach($diaDiemNhiemKy as $item)
                                        @if(!empty($item['dia_diem']))
                                            <span class="whitespace-nowrap rounded-full border border-blue-100 bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-700">
                                                {{ $item['dia_diem'] }} ({{ $item['nhiem_ky'] }}T)
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Lifestyle Tags -->
                        <div class="flex flex-wrap justify-center gap-2 mb-4 mt-auto">
                            @php
                                $loiSong = $nguoi->khao_sat_loi_song ?? [];
                                $dem = 0;
                            @endphp
                            
                            @if(empty($loiSong))
                                <span class="px-3 py-1.5 bg-gray-100 text-gray-500 border border-gray-200 text-xs font-bold rounded-md uppercase tracking-wider">
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i> Chưa cập nhật khảo sát
                                </span>
                            @else
                                @foreach($loiSong as $key => $value)
                                    @if($key !== 'uu_tien' && $dem < 3)
                                        @php
                                            // Logic tùy chỉnh hiển thị Tag theo từ khóa
                                            $icon = 'fa-check';
                                            $color = 'blue';
                                            $text = str_replace('_', ' ', $key);
                                            
                                            if (is_bool($value) || $value === 'true' || $value === 'false' || in_array(strtolower($value), ['co', 'khong'])) {
                                                $isPositive = (is_bool($value) && $value) || $value === 'true' || strtolower($value) === 'co';
                                                
                                                // Nghịch đảo màu cho các thói quen "tiêu cực"
                                                if (in_array($key, ['hut_thuoc', 'nhau_nhet', 'nuoi_thu_cung'])) {
                                                    if ($isPositive) {
                                                        $color = 'orange'; // Có hút thuốc -> Cam cảnh báo
                                                        $text = 'Có ' . $text;
                                                        $icon = 'fa-fire';
                                                    } else {
                                                        $color = 'emerald'; // Không hút thuốc -> Xanh lá an toàn
                                                        $text = 'Không ' . $text;
                                                        $icon = 'fa-leaf';
                                                    }
                                                } else {
                                                    // Mặc định
                                                    if ($isPositive) {
                                                        $color = 'emerald';
                                                        $text = 'Có ' . $text;
                                                        $icon = 'fa-check';
                                                    } else {
                                                        $color = 'red';
                                                        $text = 'Không ' . $text;
                                                        $icon = 'fa-xmark';
                                                    }
                                                }
                                            } elseif (is_numeric($value)) {
                                                $color = 'blue';
                                                $icon = 'fa-star';
                                                $text = $text . ': ' . $value . '/5';
                                            }
                                        @endphp
                                        
                                        <span class="px-2 py-0.5 bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100 text-[10px] font-bold rounded-md uppercase tracking-wider">
                                            <i class="fa-solid {{ $icon }} mr-1"></i> {{ $text }}
                                        </span>
                                        @php $dem++; @endphp
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (Kept separate to avoid trigger) -->
                <div class="px-6 pb-6 pt-2">
                    <div class="grid grid-cols-2 gap-3 w-full">
                        @if($trangThai == 'connected')
                            @if(!empty($nguoi->so_dien_thoai))
                                <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $nguoi->so_dien_thoai) }}" target="_blank" class="easym-btn easym-btn-secondary w-full py-2.5 text-sm">
                                    <i class="fa-regular fa-comment-dots mr-1"></i> Zalo
                                </a>
                            @else
                                <button onclick="alert('Người dùng này chưa cập nhật số điện thoại!')" class="easym-btn easym-btn-secondary w-full py-2.5 text-sm">
                                    <i class="fa-regular fa-comment-dots mr-1"></i> Zalo
                                </button>
                            @endif
                        @else
                            <button onclick="alert('Bạn cần kết nối thành công với người này trước khi có thể nhắn tin!')" class="easym-btn w-full cursor-not-allowed border border-slate-200 bg-white py-2.5 text-sm font-bold text-slate-400">
                                <i class="fa-regular fa-comment-dots mr-1"></i> Zalo
                            </button>
                        @endif
                        
                        @if($trangThai == 'connected')
                            <button onclick="huyKetNoiRoommate({{ $nguoi->id }}, '{{ addslashes($nguoi->ho_ten) }}')" class="easym-btn group-connected w-full border border-emerald-200 bg-emerald-50 py-2.5 text-sm font-bold text-emerald-700 hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                <span class="group-connected-default"><i class="fa-solid fa-user-group mr-1"></i> Bạn</span>
                                <span class="group-connected-hover hidden"><i class="fa-solid fa-user-slash mr-1"></i> Hủy</span>
                            </button>
                        @elseif($trangThai == 'sent')
                            <button disabled class="easym-btn w-full cursor-not-allowed border border-slate-200 bg-slate-100 py-2.5 text-sm font-bold text-slate-500">
                                <i class="fa-solid fa-clock mr-1"></i> Đang chờ...
                            </button>
                        @elseif($trangThai == 'received')
                            <button disabled class="easym-btn w-full cursor-not-allowed border border-amber-200 bg-amber-50 py-2.5 text-sm font-bold text-amber-600">
                                <i class="fa-solid fa-bell mr-1"></i> Nhận
                            </button>
                        @else
                            <button id="btn-connect-{{ $nguoi->id }}" onclick="ketNoiRoommate({{ $nguoi->id }})" class="easym-btn easym-btn-primary w-full py-2.5 text-sm">
                                <i class="fa-solid fa-handshake-angle mr-1"></i> Kết nối
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

<!-- Roommate Detail Drawer (Slide-out panel) -->
<div id="roommate-drawer-backdrop" class="fixed inset-0 z-50 bg-slate-900/50 opacity-0 backdrop-blur-sm transition-opacity duration-300 pointer-events-none" onclick="closeRoommateDrawer()"></div>

<div id="roommate-drawer" class="fixed right-0 top-[72px] z-50 flex h-[calc(100dvh-72px)] w-full max-w-lg translate-x-full transform flex-col overflow-y-auto border-l border-slate-200 bg-white shadow-2xl transition-transform duration-300 ease-out md:max-w-xl">
    <!-- Drawer Header -->
    <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white/95 p-6 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-full bg-blue-50 text-blue-600">
                <i class="fa-solid fa-user-group text-lg"></i>
            </span>
            <div>
                <h3 class="text-lg font-black text-slate-950">Chi tiết bạn ở ghép</h3>
                <p class="text-xs font-semibold text-slate-500">Hồ sơ lối sống và nhu cầu phòng</p>
            </div>
        </div>
        <button onclick="closeRoommateDrawer()" class="grid h-10 w-10 place-items-center rounded-full text-slate-400 hover:bg-slate-50 hover:text-slate-700">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Drawer Content -->
    <div class="flex-grow p-6 space-y-6">
        <!-- Top Section: Avatar & Basic Info -->
        <div class="flex flex-col items-center rounded-2xl border border-blue-100 bg-blue-50/50 p-6 text-center">
            <div class="relative w-28 h-28 mb-4">
                <img id="drawer-avatar" class="w-full h-full rounded-full object-cover ring-4 ring-white shadow-md" src="" alt="Avatar">
                <div class="absolute -bottom-2 -right-2 flex items-center gap-1 rounded-full border-2 border-white bg-blue-600 px-2.5 py-1 text-xs font-black text-white shadow-md">
                    <span id="drawer-match-percent">90%</span>
                </div>
            </div>
            <h4 id="drawer-name" class="mb-1 text-2xl font-black text-slate-950"></h4>
            <p id="drawer-meta" class="mb-3 text-sm font-medium text-slate-500"></p>
            
            <div id="drawer-contact-box" class="w-full mt-4 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex flex-col items-center justify-center gap-1 hidden">
                <span class="text-emerald-800 font-bold text-sm flex items-center gap-1.5"><i class="fa-solid fa-shield-halved text-emerald-500"></i> Đã kết nối bạn cùng phòng</span>
                <p id="drawer-phone-text" class="text-xs text-emerald-600 font-semibold"></p>
                <p id="drawer-email-text" class="text-xs text-emerald-600 font-semibold"></p>
            </div>
        </div>

        <!-- Section: Roommate Requirements (SQL Fields) -->
        <div class="space-y-3.5">
            <h5 class="text-xs font-black text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <span class="w-1.5 h-3 bg-blue-600 rounded-full"></span> Nhu cầu ở ghép
            </h5>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3.5 bg-slate-50 border border-gray-100 rounded-2xl shadow-sm">
                    <span class="text-[10px] text-gray-400 font-bold block mb-1 uppercase tracking-wider">Ngân sách tối đa</span>
                    <span id="drawer-budget" class="text-sm font-extrabold text-gray-800"></span>
                </div>
                <div class="p-3.5 bg-slate-50 border border-gray-100 rounded-2xl shadow-sm">
                    <span class="text-[10px] text-gray-400 font-bold block mb-1 uppercase tracking-wider">Số người ở tối đa</span>
                    <span id="drawer-roommates-limit" class="text-sm font-extrabold text-gray-800"></span>
                </div>
            </div>
            
            <div class="p-4 bg-slate-50 border border-gray-100 rounded-2xl shadow-sm space-y-3">
                <div>
                    <span class="text-[10px] text-gray-400 font-bold block mb-1.5 uppercase tracking-wider">Địa điểm & Nhiệm kỳ</span>
                    <div id="drawer-locations-list" class="flex flex-wrap gap-1.5"></div>
                </div>
                <div class="pt-3 border-t border-gray-200/60">
                    <span class="text-[10px] text-gray-400 font-bold block mb-1.5 uppercase tracking-wider">Tiện ích cơ sở vật chất yêu cầu</span>
                    <div id="drawer-amenities-list" class="flex flex-wrap gap-1.5"></div>
                </div>
            </div>
        </div>

        <!-- Section: Lifestyle Survey Detailed Answers -->
        <div class="space-y-3.5">
            <h5 class="text-xs font-black text-gray-400 uppercase tracking-wider flex items-center gap-2">
                <span class="w-1.5 h-3 bg-blue-600 rounded-full"></span> Chi tiết lối sống & thói quen
            </h5>
            <div id="drawer-lifestyle-list" class="space-y-3"></div>
        </div>
    </div>

    <!-- Drawer Footer -->
    <div id="drawer-footer-actions" class="p-6 border-t border-gray-100 bg-slate-50 sticky bottom-0 z-10 flex gap-3">
        <!-- Will be filled dynamically -->
    </div>
</div>

<script>
    function ketNoiRoommate(idNguoiNhan) {
        const btn = document.getElementById('btn-connect-' + idNguoiNhan);
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang xử lý...';
            btn.disabled = true;
        }

        fetch(`/tim-ban-o-ghep/ket-noi/${idNguoiNhan}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Đổi nút thành Đang chờ...
                if (btn) {
                    btn.className = 'w-full bg-gray-100 text-gray-500 font-bold py-2.5 rounded-xl border border-gray-200 cursor-not-allowed';
                    btn.innerHTML = '<i class="fa-solid fa-clock mr-1"></i> Đang chờ...';
                }
                // Tùy chọn hiển thị thông báo toast thành công ở đây
            } else {
                alert(data.message || 'Có lỗi xảy ra.');
                if (btn) {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi mạng. Vui lòng thử lại.');
            if (btn) {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        });
    }

    function handleInvite(idLoiMoi, action) {
        const row = document.getElementById('invite-' + idLoiMoi);
        row.style.opacity = '0.5';

        fetch(`/tim-ban-o-ghep/${action}/${idLoiMoi}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Hiển thị thông báo thành công cho người dùng
                // Xóa khỏi giao diện bằng hiệu ứng
                row.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    row.remove();
                    // Nếu không còn lời mời nào trong danh sách thì load lại trang để ẩn thẻ Panel
                    if (document.querySelectorAll('[id^="invite-"]').length === 0) {
                        window.location.reload();
                    }
                }, 200);
            } else {
                alert(data.message || 'Có lỗi xảy ra.');
                row.style.opacity = '1';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi mạng. Vui lòng thử lại.');
            row.style.opacity = '1';
        });
    }

    function huyKetNoiRoommate(idRoommate, name) {
        if (!confirm(`Bạn có chắc chắn muốn hủy kết nối bạn cùng phòng với ${name} không?`)) {
            return;
        }

        fetch(`/tim-ban-o-ghep/huy-ket-noi/${idRoommate}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi mạng. Vui lòng thử lại.');
        });
    }

    const byId = (id) => document.getElementById(id);

    function appendBadge(parent, text, className = 'px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg font-bold text-xs shadow-sm') {
        const badge = document.createElement('span');
        badge.className = className;
        badge.textContent = text;
        parent.appendChild(badge);
    }

    function actionElement(tag, className, html, props = {}) {
        const el = document.createElement(tag);
        el.className = className;
        el.innerHTML = html;
        Object.assign(el, props);
        return el;
    }

    function openRoommateDetails(nguoiData) {
        // 1. Fill basic details
        byId('drawer-avatar').src = nguoiData.anh_dai_dien || `https://ui-avatars.com/api/?name=${encodeURIComponent(nguoiData.ho_ten)}&background=random&size=150`;
        byId('drawer-match-percent').textContent = `${nguoiData.matching_percentage}% Khớp`;
        byId('drawer-name').textContent = nguoiData.ho_ten;
        
        let ageText = nguoiData.nam_sinh ? `${new Date().getFullYear() - nguoiData.nam_sinh} tuổi` : '';
        let jobText = nguoiData.nghe_nghiep || 'Chưa cập nhật';
        let metaText = [ageText, jobText].filter(Boolean).join(' • ');
        byId('drawer-meta').textContent = metaText;

        // 2. Fill Budget & Max Roommates
        const budgetVal = nguoiData.tien_thue || (nguoiData.khao_sat_loi_song && nguoiData.khao_sat_loi_song.tien_thue) || 2000000;
        byId('drawer-budget').textContent = `${new Intl.NumberFormat('vi-VN').format(budgetVal)} đ/tháng`;
        
        const limitVal = nguoiData.so_nguoi_to_da || (nguoiData.khao_sat_loi_song && nguoiData.khao_sat_loi_song.so_nguoi_to_da) || 2;
        byId('drawer-roommates-limit').textContent = `${limitVal} người`;

        // 3. Render Locations & Terms
        const locationsList = byId('drawer-locations-list');
        locationsList.innerHTML = '';
        const locTerms = nguoiData.dia_diem_nhiem_ky || (nguoiData.khao_sat_loi_song && nguoiData.khao_sat_loi_song.dia_diem_nhiem_ky) || [{'dia_diem': nguoiData.thanh_pho || 'Hà Nội', 'nhiem_ky': 12}];
        locTerms.forEach(item => {
            if (item.dia_diem) {
                appendBadge(locationsList, `${item.dia_diem} (${item.nhiem_ky}T)`);
            }
        });

        // 4. Render Amenities
        const amenitiesList = byId('drawer-amenities-list');
        amenitiesList.innerHTML = '';
        const csvc = nguoiData.co_so_vat_chat || (nguoiData.khao_sat_loi_song && nguoiData.khao_sat_loi_song.co_so_vat_chat) || [];
        const labels = {
            'dieu_hoa': 'Điều hòa', 'tu_lanh': 'Tủ lạnh', 'may_giat': 'Máy giặt',
            'nong_lanh': 'Nóng lạnh', 'wifi': 'Wifi', 'ban_cong': 'Ban công',
            've_sinh_khiep_kin': 'Khép kín', 'bep_nau_an': 'Bếp riêng'
        };
        if (csvc.length === 0) {
            amenitiesList.innerHTML = '<span class="text-xs text-gray-500 italic">Không có yêu cầu đặc biệt</span>';
        } else {
            csvc.forEach(item => {
                appendBadge(amenitiesList, labels[item] || item);
            });
        }

        // 5. Render contact box if connected
        const contactBox = byId('drawer-contact-box');
        const isConnected = nguoiData.trang_thai_ket_noi === 'connected';
        if (isConnected) {
            contactBox.classList.remove('hidden');
            byId('drawer-phone-text').innerHTML = `<strong>Số điện thoại:</strong> ${nguoiData.so_dien_thoai || 'Chưa cập nhật'}`;
            byId('drawer-email-text').innerHTML = `<strong>Email:</strong> ${nguoiData.email}`;
        } else {
            contactBox.classList.add('hidden');
        }

        // 6. Detailed Lifestyle survey answers
        const lifestyleList = byId('drawer-lifestyle-list');
        lifestyleList.innerHTML = '';
        
        // Map of titles
        const surveyTitles = {
            'gio_giac': 'Thói quen giờ giấc sinh hoạt',
            'do_sach_se': 'Mức độ sạch sẽ, ngăn nắp',
            'hut_thuoc': 'Mức độ hút thuốc lá',
            'nuoi_thu_cung': 'Mức độ yêu thích/nuôi thú cưng',
            'ban_be_den_choi': 'Tần suất dẫn bạn bè về phòng chơi',
            'ton_giao': 'Tôn giáo',
            'van_hoa': 'Vùng miền gốc'
        };

        const valueLabels = {
            'gio_giac': {
                'chim_som': 'Dậy sớm (Early bird)',
                'cu_dem': 'Cú đêm (Night owl)',
                'linh_hoat': 'Linh hoạt'
            },
            'ton_giao': {
                'khong': 'Không tôn giáo',
                'phat_giao': 'Phật giáo',
                'thien_chua': 'Thiên Chúa giáo',
                'tin_lanh': 'Tin Lành',
                'khac': 'Khác'
            },
            'van_hoa': {
                'mien_bac': 'Miền Bắc',
                'mien_trung': 'Miền Trung',
                'mien_nam': 'Miền Nam'
            }
        };

        const lSong = nguoiData.khao_sat_loi_song || {};
        Object.keys(surveyTitles).forEach(key => {
            const value = lSong[key];
            if (value !== undefined) {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex justify-between items-center p-3 bg-slate-50/50 border border-gray-100 rounded-xl text-sm';
                
                // Left: Title
                const titleSpan = document.createElement('span');
                titleSpan.className = 'text-gray-500 font-semibold';
                titleSpan.textContent = surveyTitles[key];
                itemDiv.appendChild(titleSpan);

                // Right: Value badge
                const valSpan = document.createElement('span');
                valSpan.className = 'font-bold px-2.5 py-0.5 rounded-lg text-xs';
                
                let displayVal = value;
                if (valueLabels[key] && valueLabels[key][value]) {
                    displayVal = valueLabels[key][value];
                }

                if (value === true || value === 'true' || value === 1 || value === '1') {
                    valSpan.className += ' bg-emerald-50 text-emerald-700 border border-emerald-100';
                    valSpan.textContent = typeof value === 'boolean' || key === 'ton_giao_loc_cung' || key === 'van_hoa_loc_cung' ? 'Có' : displayVal;
                } else if (value === false || value === 'false' || value === 0 || value === '0') {
                    valSpan.className += ' bg-red-50 text-red-700 border border-red-100';
                    valSpan.textContent = typeof value === 'boolean' || key === 'ton_giao_loc_cung' || key === 'van_hoa_loc_cung' ? 'Không' : displayVal;
                } else if (typeof value === 'number' || !isNaN(value)) {
                    // scale 5
                    valSpan.className += ' bg-blue-50 text-blue-700 border border-blue-100';
                    valSpan.textContent = `${value}/5`;
                } else {
                    valSpan.className += ' bg-slate-100 text-slate-700 border border-slate-200';
                    valSpan.textContent = displayVal;
                }
                
                itemDiv.appendChild(valSpan);
                lifestyleList.appendChild(itemDiv);
            }
        });

        // 7. Render dynamic footer actions
        const footer = byId('drawer-footer-actions');
        footer.innerHTML = '';
        
        // Zalo Button
        if (isConnected) {
            if (nguoiData.so_dien_thoai) {
                footer.appendChild(actionElement('a', 'flex-1 text-center bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-colors shadow-md', '<i class="fa-regular fa-comment-dots mr-1.5"></i> Chat Zalo', {
                    href: `https://zalo.me/${nguoiData.so_dien_thoai.replace(/[^0-9]/g, '')}`,
                    target: '_blank',
                }));
            } else {
                const noSdtBtn = actionElement('button', 'flex-1 bg-slate-100 text-slate-500 font-bold py-3 rounded-xl cursor-not-allowed', '<i class="fa-regular fa-comment-dots mr-1.5"></i> Chưa có SĐT');
                noSdtBtn.onclick = () => alert('Người dùng này chưa cập nhật số điện thoại!');
                footer.appendChild(noSdtBtn);
            }

            const disconnectBtn = actionElement('button', 'px-4 bg-red-50 text-red-600 font-bold py-3 rounded-xl hover:bg-red-100 transition-colors border border-red-200', '<i class="fa-solid fa-user-slash"></i>');
            disconnectBtn.onclick = () => {
                closeRoommateDrawer();
                huyKetNoiRoommate(nguoiData.id, nguoiData.ho_ten);
            };
            footer.appendChild(disconnectBtn);
        } else {
            const disabledZaloBtn = actionElement('button', 'flex-1 bg-white border border-gray-200 text-gray-400 font-bold py-3 rounded-xl cursor-not-allowed', '<i class="fa-regular fa-comment-dots mr-1.5"></i> Nhắn tin Zalo');
            disabledZaloBtn.onclick = () => alert('Bạn cần kết nối thành công với người này trước khi có thể nhắn tin!');
            footer.appendChild(disabledZaloBtn);

            // Connection Action Button
            if (nguoiData.trang_thai_ket_noi === 'sent') {
                footer.appendChild(actionElement('button', 'flex-1 bg-slate-100 text-slate-500 font-bold py-3 rounded-xl border border-slate-200 cursor-not-allowed', '<i class="fa-solid fa-clock mr-1.5"></i> Đang chờ...', { disabled: true }));
            } else if (nguoiData.trang_thai_ket_noi === 'received') {
                footer.appendChild(actionElement('button', 'flex-1 bg-amber-50 text-amber-600 font-bold py-3 rounded-xl border border-amber-200 cursor-not-allowed', '<i class="fa-solid fa-bell mr-1.5"></i> Chờ bạn duyệt', { disabled: true }));
            } else {
                const connectBtn = actionElement('button', 'flex-1 bg-blue-600 text-white font-bold py-3 rounded-xl shadow-md hover:bg-blue-700 transition-all', '<i class="fa-solid fa-handshake-angle mr-1.5"></i> Kết nối', {
                    id: `drawer-btn-connect-${nguoiData.id}`,
                });
                connectBtn.onclick = () => {
                    // Call connection helper
                    ketNoiRoommate(nguoiData.id);
                    // Also update this button inside drawer
                    connectBtn.disabled = true;
                    connectBtn.className = 'flex-1 bg-slate-100 text-slate-500 font-bold py-3 rounded-xl border border-slate-200 cursor-not-allowed';
                    connectBtn.innerHTML = '<i class="fa-solid fa-clock mr-1.5"></i> Đang chờ...';
                };
                footer.appendChild(connectBtn);
            }
        }

        // 8. Open drawer & overlay
        byId('roommate-drawer-backdrop').classList.remove('pointer-events-none');
        byId('roommate-drawer-backdrop').classList.remove('opacity-0');
        byId('roommate-drawer-backdrop').classList.add('opacity-100');
        
        byId('roommate-drawer').classList.remove('translate-x-full');
    }

    function closeRoommateDrawer() {
        byId('roommate-drawer-backdrop').classList.add('pointer-events-none');
        byId('roommate-drawer-backdrop').classList.remove('opacity-100');
        byId('roommate-drawer-backdrop').classList.add('opacity-0');
        
        byId('roommate-drawer').classList.add('translate-x-full');
    }
</script>
@endsection
