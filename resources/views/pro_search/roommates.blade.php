@extends('layouts.app')

@section('title', 'Tìm Bạn Ở Ghép Thông Minh - EasyM')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                Dựa trên thuật toán AI phân tích tương đồng lối sống, chúng tôi đã tìm thấy những người phù hợp nhất với bạn.
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
                <div class="bg-white rounded-xl p-4 shadow-sm border border-blue-50 flex items-center justify-between" id="invite-{{ $loiMoi->id }}">
                    <div class="flex items-center gap-3">
                        <img src="{{ $loiMoi->nguoiGui->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($loiMoi->nguoiGui->ho_ten).'&background=random' }}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $loiMoi->nguoiGui->ho_ten }}</p>
                            <p class="text-xs text-gray-500">{{ $loiMoi->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
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
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Chưa tìm thấy người phù hợp</h3>
            <p class="text-gray-500 mb-6">Bạn hãy thử mở rộng tiêu chí hoặc cập nhật lại bài Khảo sát lối sống nhé.</p>
            <a href="/khao-sat-loi-song" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform hover:-translate-y-1">
                <i class="fa-solid fa-clipboard-list mr-2"></i> Làm lại Khảo sát
            </a>
        </div>
        @else
        <!-- Lưới User Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($ds_goi_y as $nguoi)
            <!-- User Card -->
            <div class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full transform hover:-translate-y-2">
                
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
                <div class="pt-16 pb-6 px-6 text-center flex-1 flex flex-col">
                    <h3 class="text-xl font-extrabold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">{{ $nguoi->ho_ten }}</h3>
                    <p class="text-sm font-medium text-gray-500 mb-4">
                        @if($nguoi->nam_sinh)
                            <i class="fa-solid fa-cake-candles mr-1 text-gray-400"></i> {{ date('Y') - $nguoi->nam_sinh }} tuổi &bull;
                        @endif
                        <i class="fa-solid fa-briefcase ml-1 mr-1 text-gray-400"></i> {{ $nguoi->nghe_nghiep ?? 'Sinh viên' }}
                    </p>

                    <!-- Lifestyle Tags -->
                    <div class="flex flex-wrap justify-center gap-2 mb-6 mt-auto">
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
                                @if($key !== 'uu_tien' && $dem < 4)
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
                                    
                                    <span class="px-2.5 py-1 bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100 text-[11px] font-bold rounded-md uppercase tracking-wider">
                                        <i class="fa-solid {{ $icon }} mr-1"></i> {{ $text }}
                                    </span>
                                    @php $dem++; @endphp
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3 w-full">
                        @php
                            $trangThai = $trangThaiKetNoi[$nguoi->id] ?? null;
                        @endphp

                        @if($trangThai == 'connected')
                            @if(!empty($nguoi->so_dien_thoai))
                                <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $nguoi->so_dien_thoai) }}" target="_blank" class="w-full block text-center bg-blue-50 border-2 border-blue-200 text-blue-700 font-bold py-2.5 rounded-xl hover:bg-blue-100 transition-colors focus:ring-4 focus:ring-blue-100">
                                    <i class="fa-regular fa-comment-dots mr-1"></i> Zalo: {{ $nguoi->so_dien_thoai }}
                                </a>
                            @else
                                <button onclick="alert('Người dùng này chưa cập nhật số điện thoại!')" class="w-full bg-blue-50 border-2 border-blue-200 text-blue-700 font-bold py-2.5 rounded-xl hover:bg-blue-100 transition-colors focus:ring-4 focus:ring-blue-100">
                                    <i class="fa-regular fa-comment-dots mr-1"></i> Chưa có SĐT
                                </button>
                            @endif
                        @else
                            <button onclick="alert('Bạn cần kết nối thành công với người này trước khi có thể nhắn tin!')" class="w-full bg-white border-2 border-gray-200 text-gray-400 font-bold py-2.5 rounded-xl hover:bg-gray-50 transition-colors cursor-not-allowed">
                                <i class="fa-regular fa-comment-dots mr-1"></i> Nhắn tin Zalo
                            </button>
                        @endif
                        
                        @if($trangThai == 'connected')
                            <button disabled class="w-full bg-emerald-100 text-emerald-700 font-bold py-2.5 rounded-xl border border-emerald-200 cursor-default">
                                <i class="fa-solid fa-user-group mr-1"></i> Bạn cùng phòng
                            </button>
                        @elseif($trangThai == 'sent')
                            <button disabled class="w-full bg-gray-100 text-gray-500 font-bold py-2.5 rounded-xl border border-gray-200 cursor-not-allowed">
                                <i class="fa-solid fa-clock mr-1"></i> Đang chờ...
                            </button>
                        @elseif($trangThai == 'received')
                            <button disabled class="w-full bg-amber-50 text-amber-600 font-bold py-2.5 rounded-xl border border-amber-200 cursor-not-allowed text-[13px]">
                                <i class="fa-solid fa-bell mr-1"></i> Chờ bạn duyệt
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

<script>
    function ketNoiRoommate(idNguoiNhan) {
        const btn = document.getElementById('btn-connect-' + idNguoiNhan);
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang xử lý...';
        btn.disabled = true;

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
                btn.className = 'w-full bg-gray-100 text-gray-500 font-bold py-2.5 rounded-xl border border-gray-200 cursor-not-allowed';
                btn.innerHTML = '<i class="fa-solid fa-clock mr-1"></i> Đang chờ...';
                // Tùy chọn hiển thị thông báo toast thành công ở đây
            } else {
                alert(data.message || 'Có lỗi xảy ra.');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi mạng. Vui lòng thử lại.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
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
</script>
@endsection
