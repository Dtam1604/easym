@extends('layouts.app')

@section('title', 'Tìm Bạn Ở Ghép Thông Minh - EasyM')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .group-connected:hover .group-connected-default { display: none; }
    .group-connected:hover .group-connected-hover { display: inline; }
</style>
@endpush

@section('content')
<div class="bg-slate-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 mb-4 tracking-tight">
                Gợi Ý Bạn Ở Ghép Lý Tưởng
            </h1>
            <p class="text-gray-500 max-w-2xl mx-auto text-lg">
                Dựa trên thuật toán phân tích tương đồng lối sống, chúng tôi đã tìm thấy những người phù hợp nhất với bạn.
            </p>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-8">
            <form method="GET" action="{{ route('tim-ban.index') }}" class="flex flex-col gap-4">
                <!-- Hàng 1: Tìm kiếm & Lọc Cứng -->
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 w-full relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3.5 text-gray-400"></i>
                        <input type="text" name="tu_khoa" value="{{ request('tu_khoa') }}" placeholder="Tìm theo tên hoặc trường học, nghề nghiệp..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors bg-gray-50 text-sm">
                    </div>
                    
                    <div class="w-full md:w-40">
                        @php $reqGioiTinh = request('gioi_tinh', $nguoiDungHienTai->gioi_tinh ?? 'tat_ca'); @endphp
                        <select name="gioi_tinh" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-gray-50 text-sm text-gray-700 appearance-none">
                            <option value="tat_ca" {{ $reqGioiTinh == 'tat_ca' ? 'selected' : '' }}>⚧ Giới tính (Tất cả)</option>
                            <option value="nam" {{ $reqGioiTinh == 'nam' ? 'selected' : '' }}>👦 Nam</option>
                            <option value="nu" {{ $reqGioiTinh == 'nu' ? 'selected' : '' }}>👧 Nữ</option>
                        </select>
                    </div>

                    <div class="w-full md:w-40">
                        <select name="khoang_tuoi" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 bg-gray-50 text-sm text-gray-700 appearance-none">
                            <option value="">🎂 Độ tuổi (Tất cả)</option>
                            <option value="18-22" {{ request('khoang_tuoi') == '18-22' ? 'selected' : '' }}>18 - 22 tuổi</option>
                            <option value="23-26" {{ request('khoang_tuoi') == '23-26' ? 'selected' : '' }}>23 - 26 tuổi</option>
                            <option value=">26" {{ request('khoang_tuoi') == '>26' ? 'selected' : '' }}>Trên 26 tuổi</option>
                        </select>
                    </div>

                    <div class="w-full md:w-48 relative">
                        <i class="fa-solid fa-location-dot absolute left-4 top-3.5 text-gray-400"></i>
                        <input type="text" name="thanh_pho" value="{{ request('thanh_pho') }}" placeholder="Khu vực (VD: Hà Nội)" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors bg-gray-50 text-sm">
                    </div>
                    
                    <button type="submit" class="w-full md:w-auto px-6 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl transition-colors shadow-sm text-sm whitespace-nowrap">
                        <i class="fa-solid fa-filter mr-1"></i> Áp dụng
                    </button>
                </div>
            </form>
        </div>

        <!-- Panel Lời mời chờ duyệt -->
        @if(isset($loiMoiChoDuyet) && count($loiMoiChoDuyet) > 0)
        <div class="mb-8 bg-blue-50/50 rounded-2xl border border-blue-100 p-5">
            <h3 class="font-bold text-blue-900 mb-4 flex items-center"><i class="fa-solid fa-bell text-blue-500 mr-2"></i> Lời mời kết nối chờ duyệt ({{ count($loiMoiChoDuyet) }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($loiMoiChoDuyet as $loiMoi)
                @php
                    $senderData = [
                        'id' => $loiMoi->nguoiGui->id,
                        'ho_ten' => $loiMoi->nguoiGui->ho_ten,
                        'nam_sinh' => $loiMoi->nguoiGui->nam_sinh,
                        'nghe_nghiep' => $loiMoi->nguoiGui->nghe_nghiep ?? 'Sinh viên',
                        'thanh_pho' => $loiMoi->nguoiGui->thanh_pho ?? '',
                        'anh_dai_dien' => $loiMoi->nguoiGui->anh_dai_dien ?? '',
                        'so_dien_thoai' => $loiMoi->nguoiGui->so_dien_thoai ?? '',
                        'email' => $loiMoi->nguoiGui->email ?? '',
                        'matching_percentage' => $loiMoi->nguoiGui->matching_percentage ?? 0,
                        'trang_thai_ket_noi' => 'received',
                        'tien_thue' => $loiMoi->nguoiGui->tien_thue,
                        'so_nguoi_to_da' => $loiMoi->nguoiGui->so_nguoi_to_da,
                        'co_so_vat_chat' => $loiMoi->nguoiGui->co_so_vat_chat ?? [],
                        'dia_diem_nhiem_ky' => $loiMoi->nguoiGui->dia_diem_nhiem_ky ?? [],
                        'khao_sat_loi_song' => $loiMoi->nguoiGui->khao_sat_loi_song ?? []
                    ];
                @endphp
                <div class="bg-white rounded-xl p-4 shadow-sm border border-blue-50 flex items-center justify-between" id="invite-{{ $loiMoi->id }}">
                    <div class="flex items-center gap-3 cursor-pointer hover:opacity-80 transition-opacity" title="Xem chi tiết" data-roommate="{{ json_encode($senderData) }}" onclick="openRoommateDetails(JSON.parse(this.getAttribute('data-roommate')))">
                        <img src="{{ $loiMoi->nguoiGui->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($loiMoi->nguoiGui->ho_ten).'&background=random' }}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $loiMoi->nguoiGui->ho_ten }}</p>
                            <p class="text-xs text-gray-500">{{ $loiMoi->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button data-roommate="{{ json_encode($senderData) }}" onclick="openRoommateDetails(JSON.parse(this.getAttribute('data-roommate')))" class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 flex items-center justify-center transition-colors" title="Xem thông tin người gửi">
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
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-gray-100">
            <div class="w-32 h-32 mx-auto bg-blue-50 rounded-full flex items-center justify-center mb-6">
                <i class="fa-solid fa-user-group text-5xl text-blue-300"></i>
            </div>
            @if(empty($nguoiDungHienTai->khao_sat_loi_song))
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Bạn chưa thực hiện khảo sát lối sống</h3>
                <p class="text-gray-500 mb-6">Hãy thực hiện khảo sát lối sống để hệ thống tìm kiếm bạn ở ghép phù hợp nhất.</p>
                <a href="/khao-sat-loi-song" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform hover:-translate-y-1">
                    <i class="fa-solid fa-clipboard-list mr-2"></i> Thực hiện Khảo sát
                </a>
            @else
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Chưa tìm thấy người phù hợp</h3>
                <p class="text-gray-500 mb-6">Bạn hãy thử mở rộng tiêu chí hoặc cập nhật lại bài Khảo sát lối sống nhé.</p>
                <a href="/khao-sat-loi-song" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform hover:-translate-y-1">
                    <i class="fa-solid fa-clipboard-list mr-2"></i> Làm lại Khảo sát
                </a>
            @endif
        </div>
        @else
        <!-- Lưới User Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($ds_goi_y as $nguoi)
            <!-- User Card -->
            <div class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full transform hover:-translate-y-2">
                @php
                    $trangThai = $trangThaiKetNoi[$nguoi->id] ?? null;
                    $nguoiData = [
                        'id' => $nguoi->id,
                        'ho_ten' => $nguoi->ho_ten,
                        'nam_sinh' => $nguoi->nam_sinh,
                        'nghe_nghiep' => $nguoi->nghe_nghiep ?? 'Sinh viên',
                        'thanh_pho' => $nguoi->thanh_pho ?? '',
                        'anh_dai_dien' => $nguoi->anh_dai_dien ?? '',
                        'so_dien_thoai' => $nguoi->so_dien_thoai ?? '',
                        'email' => $nguoi->email ?? '',
                        'matching_percentage' => $nguoi->matching_percentage ?? 0,
                        'trang_thai_ket_noi' => $trangThai,
                        'tien_thue' => $nguoi->tien_thue,
                        'so_nguoi_to_da' => $nguoi->so_nguoi_to_da,
                        'co_so_vat_chat' => $nguoi->co_so_vat_chat ?? [],
                        'dia_diem_nhiem_ky' => $nguoi->dia_diem_nhiem_ky ?? [],
                        'khao_sat_loi_song' => $nguoi->khao_sat_loi_song ?? []
                    ];
                @endphp
                
                <!-- Clickable area to view full roommate details panel -->
                <div class="cursor-pointer flex-1 flex flex-col" data-roommate="{{ json_encode($nguoiData) }}" onclick="openRoommateDetails(JSON.parse(this.getAttribute('data-roommate')))">
                    <!-- Card Header (Cover & Avatar) -->
                    <div class="relative h-32 bg-gradient-to-r from-blue-100 to-indigo-50">
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 z-10 border border-white/50">
                            <i class="fa-solid fa-bolt text-yellow-500 text-sm"></i>
                            <span class="font-bold text-gray-800 text-sm">{{ $nguoi->matching_percentage }}% Khớp</span>
                        </div>
                        
                        <!-- Circular Progress bar SVG wrapper for Avatar -->
                        <div class="absolute -bottom-12 left-1/2 -translate-x-1/2">
                            <div class="relative w-28 h-28 bg-white rounded-full p-1.5 shadow-md">
                                <!-- Avatar Image -->
                                <img src="{{ $nguoi->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($nguoi->ho_ten).'&background=random&size=150' }}" 
                                     alt="{{ $nguoi->ho_ten }}" 
                                     class="w-full h-full rounded-full object-cover">
                                     
                                <!-- Mức độ hợp nhau Ring SVG -->
                                <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none" viewBox="0 0 100 100">
                                    <circle class="text-gray-100 stroke-current" stroke-width="6" cx="50" cy="50" r="47" fill="transparent"></circle>
                                    <circle class="{{ $nguoi->matching_percentage >= 80 ? 'text-emerald-500' : ($nguoi->matching_percentage >= 50 ? 'text-blue-500' : 'text-orange-400') }} stroke-current drop-shadow-sm transition-all duration-1000 ease-out" 
                                            stroke-width="6" 
                                            stroke-linecap="round" 
                                            cx="50" cy="50" r="47" 
                                            fill="transparent" 
                                            stroke-dasharray="{{ 2 * 3.14159 * 47 }}" 
                                            stroke-dashoffset="{{ 2 * 3.14159 * 47 * (1 - $nguoi->matching_percentage / 100) }}"></circle>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="pt-16 pb-3 px-6 text-center flex-grow flex flex-col">
                        <h3 class="text-xl font-extrabold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">{{ $nguoi->ho_ten }}</h3>
                        <p class="text-sm font-medium text-gray-500 mb-4">
                            @if($nguoi->nam_sinh)
                                <i class="fa-solid fa-cake-candles mr-1 text-gray-400"></i> {{ date('Y') - $nguoi->nam_sinh }} tuổi &bull;
                            @endif
                            <i class="fa-solid fa-briefcase ml-1 mr-1 text-gray-400"></i> {{ $nguoi->nghe_nghiep ?? 'Chưa cập nhật' }}
                        </p>

                        <!-- Roommate Requirements Box -->
                        <div class="bg-slate-50 rounded-2xl p-4 mb-5 text-left text-xs space-y-2.5 border border-gray-100/80 shadow-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-money-bill-wave text-blue-500 text-sm"></i> Ngân sách:</span>
                                <span class="font-extrabold text-gray-800 text-sm">{{ number_format($nguoi->tien_thue ?? $nguoi->khao_sat_loi_song['tien_thue'] ?? 2000000) }} đ/tháng</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-users text-blue-500 text-sm"></i> Số người ở tối đa:</span>
                                <span class="font-extrabold text-gray-800 text-sm">{{ $nguoi->so_nguoi_to_da ?? $nguoi->khao_sat_loi_song['so_nguoi_to_da'] ?? 2 }} người</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-couch text-blue-500 text-sm"></i> Tiện ích yêu cầu:</span>
                                <span class="font-bold text-gray-700 truncate max-w-[140px]" title="@php
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
                            <div class="pt-2 border-t border-gray-200">
                                <span class="text-gray-500 font-semibold block mb-1.5 flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-rose-500 text-sm"></i> Địa điểm & Nhiệm kỳ:</span>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $diaDiemNhiemKy = $nguoi->dia_diem_nhiem_ky ?? $nguoi->khao_sat_loi_song['dia_diem_nhiem_ky'] ?? [['dia_diem' => $nguoi->thanh_pho ?? 'Hà Nội', 'nhiem_ky' => 12]];
                                    @endphp
                                    @foreach($diaDiemNhiemKy as $item)
                                        @if(!empty($item['dia_diem']))
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-md font-bold text-[10px] whitespace-nowrap shadow-sm">
                                                📍 {{ $item['dia_diem'] }} ({{ $item['nhiem_ky'] }}T)
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
                                <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $nguoi->so_dien_thoai) }}" target="_blank" class="w-full block text-center bg-blue-50 border-2 border-blue-200 text-blue-700 font-bold py-2.5 rounded-xl hover:bg-blue-100 transition-colors focus:ring-4 focus:ring-blue-100">
                                    <i class="fa-regular fa-comment-dots mr-1"></i> Zalo
                                </a>
                            @else
                                <button onclick="alert('Người dùng này chưa cập nhật số điện thoại!')" class="w-full bg-blue-50 border-2 border-blue-200 text-blue-700 font-bold py-2.5 rounded-xl hover:bg-blue-100 transition-colors focus:ring-4 focus:ring-blue-100">
                                    <i class="fa-regular fa-comment-dots mr-1"></i> Zalo
                                </button>
                            @endif
                        @else
                            <button onclick="alert('Bạn cần kết nối thành công với người này trước khi có thể nhắn tin!')" class="w-full bg-white border-2 border-gray-200 text-gray-400 font-bold py-2.5 rounded-xl hover:bg-gray-50 transition-colors cursor-not-allowed">
                                <i class="fa-regular fa-comment-dots mr-1"></i> Zalo
                            </button>
                        @endif
                        
                        @if($trangThai == 'connected')
                            <button onclick="huyKetNoiRoommate({{ $nguoi->id }}, '{{ addslashes($nguoi->ho_ten) }}')" class="w-full bg-emerald-100 text-emerald-700 font-bold py-2.5 rounded-xl border border-emerald-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all duration-300 group-connected">
                                <span class="group-connected-default"><i class="fa-solid fa-user-group mr-1"></i> Bạn</span>
                                <span class="group-connected-hover hidden"><i class="fa-solid fa-user-slash mr-1"></i> Hủy</span>
                            </button>
                        @elseif($trangThai == 'sent')
                            <button disabled class="w-full bg-gray-100 text-gray-500 font-bold py-2.5 rounded-xl border border-gray-200 cursor-not-allowed">
                                <i class="fa-solid fa-clock mr-1"></i> Đang chờ...
                            </button>
                        @elseif($trangThai == 'received')
                            <button disabled class="w-full bg-amber-50 text-amber-600 font-bold py-2.5 rounded-xl border border-amber-200 cursor-not-allowed text-[13px]">
                                <i class="fa-solid fa-bell mr-1"></i> Nhận
                            </button>
                        @else
                            <button id="btn-connect-{{ $nguoi->id }}" onclick="ketNoiRoommate({{ $nguoi->id }})" class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-xl shadow-md hover:bg-blue-700 hover:-translate-y-0.5 transition-all focus:ring-4 focus:ring-blue-200">
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
<div id="roommate-drawer-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none" onclick="closeRoommateDrawer()"></div>

<div id="roommate-drawer" class="fixed top-0 right-0 h-full w-full max-w-lg md:max-w-xl bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto flex flex-col">
    <!-- Drawer Header -->
    <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white/95 backdrop-blur-md z-10">
        <div class="flex items-center gap-3">
            <span class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                <i class="fa-solid fa-user-astronaut text-xl"></i>
            </span>
            <div>
                <h3 class="text-lg font-black text-gray-900">Chi tiết bạn ở ghép</h3>
                <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">Hồ sơ thông tin lối sống chi tiết</p>
            </div>
        </div>
        <button onclick="closeRoommateDrawer()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Drawer Content -->
    <div class="flex-grow p-6 space-y-6">
        <!-- Top Section: Avatar & Basic Info -->
        <div class="flex flex-col items-center text-center p-6 bg-gradient-to-b from-blue-50/50 to-transparent rounded-3xl border border-blue-100/30">
            <div class="relative w-28 h-28 mb-4">
                <img id="drawer-avatar" class="w-full h-full rounded-full object-cover ring-4 ring-white shadow-md" src="" alt="Avatar">
                <div class="absolute -bottom-2 -right-2 bg-yellow-400 text-slate-900 px-2.5 py-1 rounded-full text-xs font-black shadow-md flex items-center gap-1 border-2 border-white">
                    <i class="fa-solid fa-bolt text-[10px]"></i> <span id="drawer-match-percent">90%</span>
                </div>
            </div>
            <h4 id="drawer-name" class="text-2xl font-extrabold text-gray-900 mb-1"></h4>
            <p id="drawer-meta" class="text-sm text-gray-500 font-medium mb-3"></p>
            
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
                <span class="w-1.5 h-3 bg-indigo-600 rounded-full"></span> Chi tiết lối sống & thói quen
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

    function openRoommateDetails(nguoiData) {
        // 1. Fill basic details
        document.getElementById('drawer-avatar').src = nguoiData.anh_dai_dien || `https://ui-avatars.com/api/?name=${encodeURIComponent(nguoiData.ho_ten)}&background=random&size=150`;
        document.getElementById('drawer-match-percent').textContent = `${nguoiData.matching_percentage}% Khớp`;
        document.getElementById('drawer-name').textContent = nguoiData.ho_ten;
        
        let ageText = nguoiData.nam_sinh ? `${new Date().getFullYear() - nguoiData.nam_sinh} tuổi` : '';
        let jobText = nguoiData.nghe_nghiep || 'Chưa cập nhật';
        let metaText = [ageText, jobText].filter(Boolean).join(' • ');
        document.getElementById('drawer-meta').textContent = metaText;

        // 2. Fill Budget & Max Roommates
        const budgetVal = nguoiData.tien_thue || (nguoiData.khao_sat_loi_song && nguoiData.khao_sat_loi_song.tien_thue) || 2000000;
        document.getElementById('drawer-budget').textContent = `${new Intl.NumberFormat('vi-VN').format(budgetVal)} đ/tháng`;
        
        const limitVal = nguoiData.so_nguoi_to_da || (nguoiData.khao_sat_loi_song && nguoiData.khao_sat_loi_song.so_nguoi_to_da) || 2;
        document.getElementById('drawer-roommates-limit').textContent = `${limitVal} người`;

        // 3. Render Locations & Terms
        const locationsList = document.getElementById('drawer-locations-list');
        locationsList.innerHTML = '';
        const locTerms = nguoiData.dia_diem_nhiem_ky || (nguoiData.khao_sat_loi_song && nguoiData.khao_sat_loi_song.dia_diem_nhiem_ky) || [{'dia_diem': nguoiData.thanh_pho || 'Hà Nội', 'nhiem_ky': 12}];
        locTerms.forEach(item => {
            if (item.dia_diem) {
                const badge = document.createElement('span');
                badge.className = 'px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg font-bold text-xs shadow-sm';
                badge.textContent = `📍 ${item.dia_diem} (${item.nhiem_ky}T)`;
                locationsList.appendChild(badge);
            }
        });

        // 4. Render Amenities
        const amenitiesList = document.getElementById('drawer-amenities-list');
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
                const badge = document.createElement('span');
                badge.className = 'px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-lg font-bold text-xs shadow-sm';
                badge.textContent = labels[item] || item;
                amenitiesList.appendChild(badge);
            });
        }

        // 5. Render contact box if connected
        const contactBox = document.getElementById('drawer-contact-box');
        const isConnected = nguoiData.trang_thai_ket_noi === 'connected';
        if (isConnected) {
            contactBox.classList.remove('hidden');
            document.getElementById('drawer-phone-text').innerHTML = `📱 <strong>Số điện thoại:</strong> ${nguoiData.so_dien_thoai || 'Chưa cập nhật'}`;
            document.getElementById('drawer-email-text').innerHTML = `✉️ <strong>Email:</strong> ${nguoiData.email}`;
        } else {
            contactBox.classList.add('hidden');
        }

        // 6. Detailed Lifestyle survey answers
        const lifestyleList = document.getElementById('drawer-lifestyle-list');
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
        const footer = document.getElementById('drawer-footer-actions');
        footer.innerHTML = '';
        
        // Zalo Button
        if (isConnected) {
            if (nguoiData.so_dien_thoai) {
                const zaloBtn = document.createElement('a');
                zaloBtn.href = `https://zalo.me/${nguoiData.so_dien_thoai.replace(/[^0-9]/g, '')}`;
                zaloBtn.target = '_blank';
                zaloBtn.className = 'flex-1 text-center bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-colors shadow-md';
                zaloBtn.innerHTML = '<i class="fa-regular fa-comment-dots mr-1.5"></i> Chat Zalo';
                footer.appendChild(zaloBtn);
            } else {
                const noSdtBtn = document.createElement('button');
                noSdtBtn.onclick = () => alert('Người dùng này chưa cập nhật số điện thoại!');
                noSdtBtn.className = 'flex-1 bg-slate-100 text-slate-500 font-bold py-3 rounded-xl cursor-not-allowed';
                noSdtBtn.innerHTML = '<i class="fa-regular fa-comment-dots mr-1.5"></i> Chưa có SĐT';
                footer.appendChild(noSdtBtn);
            }

            const disconnectBtn = document.createElement('button');
            disconnectBtn.onclick = () => {
                closeRoommateDrawer();
                huyKetNoiRoommate(nguoiData.id, nguoiData.ho_ten);
            };
            disconnectBtn.className = 'px-4 bg-red-50 text-red-600 font-bold py-3 rounded-xl hover:bg-red-100 transition-colors border border-red-200';
            disconnectBtn.innerHTML = '<i class="fa-solid fa-user-slash"></i>';
            footer.appendChild(disconnectBtn);
        } else {
            const disabledZaloBtn = document.createElement('button');
            disabledZaloBtn.onclick = () => alert('Bạn cần kết nối thành công với người này trước khi có thể nhắn tin!');
            disabledZaloBtn.className = 'flex-1 bg-white border border-gray-200 text-gray-400 font-bold py-3 rounded-xl cursor-not-allowed';
            disabledZaloBtn.innerHTML = '<i class="fa-regular fa-comment-dots mr-1.5"></i> Nhắn tin Zalo';
            footer.appendChild(disabledZaloBtn);

            // Connection Action Button
            if (nguoiData.trang_thai_ket_noi === 'sent') {
                const sentBtn = document.createElement('button');
                sentBtn.disabled = true;
                sentBtn.className = 'flex-1 bg-slate-100 text-slate-500 font-bold py-3 rounded-xl border border-slate-200 cursor-not-allowed';
                sentBtn.innerHTML = '<i class="fa-solid fa-clock mr-1.5"></i> Đang chờ...';
                footer.appendChild(sentBtn);
            } else if (nguoiData.trang_thai_ket_noi === 'received') {
                const recBtn = document.createElement('button');
                recBtn.disabled = true;
                recBtn.className = 'flex-1 bg-amber-50 text-amber-600 font-bold py-3 rounded-xl border border-amber-200 cursor-not-allowed';
                recBtn.innerHTML = '<i class="fa-solid fa-bell mr-1.5"></i> Chờ bạn duyệt';
                footer.appendChild(recBtn);
            } else {
                const connectBtn = document.createElement('button');
                connectBtn.id = `drawer-btn-connect-${nguoiData.id}`;
                connectBtn.onclick = () => {
                    // Call connection helper
                    const btnInCard = document.getElementById(`btn-connect-${nguoiData.id}`);
                    ketNoiRoommate(nguoiData.id);
                    // Also update this button inside drawer
                    connectBtn.disabled = true;
                    connectBtn.className = 'flex-1 bg-slate-100 text-slate-500 font-bold py-3 rounded-xl border border-slate-200 cursor-not-allowed';
                    connectBtn.innerHTML = '<i class="fa-solid fa-clock mr-1.5"></i> Đang chờ...';
                };
                connectBtn.className = 'flex-1 bg-blue-600 text-white font-bold py-3 rounded-xl shadow-md hover:bg-blue-700 transition-all';
                connectBtn.innerHTML = '<i class="fa-solid fa-handshake-angle mr-1.5"></i> Kết nối';
                footer.appendChild(connectBtn);
            }
        }

        // 8. Open drawer & overlay
        document.getElementById('roommate-drawer-backdrop').classList.remove('pointer-events-none');
        document.getElementById('roommate-drawer-backdrop').classList.remove('opacity-0');
        document.getElementById('roommate-drawer-backdrop').classList.add('opacity-100');
        
        document.getElementById('roommate-drawer').classList.remove('translate-x-full');
    }

    function closeRoommateDrawer() {
        document.getElementById('roommate-drawer-backdrop').classList.add('pointer-events-none');
        document.getElementById('roommate-drawer-backdrop').classList.remove('opacity-100');
        document.getElementById('roommate-drawer-backdrop').classList.add('opacity-0');
        
        document.getElementById('roommate-drawer').classList.add('translate-x-full');
    }
</script>
@endsection
