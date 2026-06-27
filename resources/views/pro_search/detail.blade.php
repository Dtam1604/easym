@extends('layouts.app')

@section('title', $phong->tieu_de . ' - Chi tiết phòng trọ')

@section('content')
<div class="ops-page pb-12">
    <!-- Header/Breadcrumb -->
    <div class="bg-white/95 border-b border-gray-200">
        <div class="ops-shell py-4">
            <nav class="flex text-sm text-gray-500 font-medium">
                <a href="{{ route('search.results') }}" class="hover:text-blue-600 transition-colors">Kết quả tìm kiếm</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 truncate max-w-xs">{{ $phong->tieu_de }}</span>
            </nav>
        </div>
    </div>

    <div class="ops-shell mt-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cột trái: Ảnh và Thông tin chi tiết -->
            <div class="w-full lg:w-2/3 space-y-8">
                <!-- Gallery Ảnh -->
                <div class="ops-card overflow-hidden p-2">
                    @if(is_array($phong->anh_phong) && count($phong->anh_phong) > 0)
                        <div class="flex overflow-x-auto snap-x snap-mandatory gap-2 pb-2">
                            @foreach($phong->anh_phong as $anh)
                                <div class="snap-center shrink-0 w-full h-96 bg-cover bg-center rounded-xl" style="background-image: url('{{ $anh }}');"></div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-96 bg-cover bg-center rounded-xl" style="background-image: url('https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=1200');"></div>
                    @endif
                </div>

                <!-- Tiêu đề và Giá -->
                <div class="ops-card p-6 sm:p-8 relative">


                    @if($phong->muc_do_xac_thuc == 2)
                        <span class="ops-badge ops-badge-blue mb-4">
                            <i class="fa-solid fa-shield-check"></i>
                            Đã xác thực bởi EasyM
                        </span>
                    @endif

                    <h1 class="text-3xl font-extrabold text-gray-900 pr-24 leading-tight mb-4">
                        {{ $phong->tieu_de }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 mb-6 pr-24">
                        <p class="text-gray-500 flex items-center gap-2 m-0">
                            <i class="fa-solid fa-location-dot text-gray-400 shrink-0"></i>
                            <span>{{ $phong->dia_chi_chi_tiet ?? 'Gần ĐH Lâm Nghiệp' }}</span>
                        </p>
                        @php
                            $coords = \Illuminate\Support\Facades\DB::selectOne("SELECT ST_Y(vi_tri::geometry) as lat, ST_X(vi_tri::geometry) as lng FROM phong_tro WHERE id = ?", [$phong->id]);
                            $gmapsUrl = $coords && $coords->lat ? "https://www.google.com/maps/dir/?api=1&destination={$coords->lat},{$coords->lng}" : "https://www.google.com/maps/dir/?api=1&destination=".urlencode($phong->dia_chi_chi_tiet ?? 'Đại học Lâm Nghiệp, Hà Nội');
                        @endphp
                        <a href="{{ $gmapsUrl }}" target="_blank" class="ops-badge ops-badge-blue py-1 shrink-0">
                            <i class="fa-solid fa-diamond-turn-right"></i> Chỉ đường
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-6 border-y border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Mức giá</p>
                            <p class="text-xl font-bold text-blue-600">{{ number_format($phong->gia_phong, 0, ',', '.') }}đ</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Diện tích</p>
                            <p class="text-lg font-bold text-gray-900">{{ $phong->dien_tich ?? '25' }} m²</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Cọc</p>
                            <p class="text-lg font-bold text-gray-900">1 tháng</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Giới tính</p>
                            <p class="text-lg font-bold text-gray-900">{{ $phong->gioi_tinh_cho_thue ?? 'Tất cả' }}</p>
                        </div>
                    </div>

                    <!-- Mô tả -->
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Thông tin mô tả</h3>
                        <div class="prose prose-blue max-w-none text-gray-600 leading-relaxed">
                            {!! nl2br(e($phong->mo_ta)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Khung liên hệ -->
            <div class="w-full lg:w-1/3">
                <div class="ops-card p-6 sticky top-24">
                    <div class="flex items-center gap-4 border-b border-gray-100 pb-6 mb-6">
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center font-bold text-xl text-blue-600 uppercase">
                            {{ substr($phong->chuTro->ho_ten ?? 'H', 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg flex items-center gap-1">
                               {{ $phong->chuTro->ho_ten ?? 'Chủ trọ ẩn danh' }}
                               @if(isset($phong->chuTro) && $phong->chuTro->da_xac_thuc_cccd)
                                   <i class="fa-solid fa-circle-check text-blue-500 text-sm" title="Tài khoản đã xác thực"></i>
                               @endif
                           </h3>
                            <p class="text-sm text-gray-500">Hoạt động 5 phút trước</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @if(!empty($phong->chuTro->so_dien_thoai))
                            <a href="tel:{{ $phong->chuTro->so_dien_thoai }}" class="ops-action-primary w-full min-h-[3rem]">
                                <i class="fa-solid fa-phone"></i>
                                Gọi: {{ $phong->chuTro->so_dien_thoai }}
                            </a>
                            
                            <a href="https://zalo.me/{{ preg_replace('/[^0-9]/', '', $phong->chuTro->so_dien_thoai) }}" target="_blank" class="ops-action-secondary w-full min-h-[3rem]">
                                <i class="fa-regular fa-message"></i>
                                Nhắn tin Zalo
                            </a>
                        @else
                            <button onclick="alert('Chủ trọ chưa cập nhật số điện thoại!')" class="w-full bg-gray-200 text-gray-500 font-bold py-3.5 px-4 rounded-xl cursor-not-allowed flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Chưa có số điện thoại
                            </button>
                        @endif
                        
                        <div class="mt-2">
                            <button onclick="document.getElementById('khungDatLich').style.display = document.getElementById('khungDatLich').style.display === 'none' ? 'block' : 'none'" type="button" class="w-full rounded-full bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold py-3.5 px-4 border border-emerald-200 transition-colors flex justify-center items-center gap-2">
                                <i class="fa-regular fa-calendar-check text-lg"></i>
                                Đặt lịch hẹn xem phòng
                            </button>
                            
                            <div id="khungDatLich" style="display: none;" class="mt-3 p-4 bg-white border border-gray-200 rounded-xl shadow-inner">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Chọn thời gian hẹn:</label>
                                <input type="datetime-local" id="thoiGianHenInput" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 mb-3 text-sm">
                                 <button onclick="guiDatLichVanilla()" type="button" id="btnGuiDatLich" class="w-full rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 transition-colors flex justify-center items-center">
                                    <span id="btnTextGui">Gửi yêu cầu</span>
                                    <span id="btnTextLoading" style="display: none;"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Đang gửi...</span>
                                </button>
                            </div>
                        </div>

                        <script>
                            function guiDatLichVanilla() {
                                const thoiGian = document.getElementById('thoiGianHenInput').value;
                                if (!thoiGian) {
                                    alert('Vui lòng chọn thời gian hẹn!');
                                    return;
                                }
                                
                                const btnGui = document.getElementById('btnGuiDatLich');
                                const textGui = document.getElementById('btnTextGui');
                                const textLoading = document.getElementById('btnTextLoading');
                                
                                btnGui.disabled = true;
                                textGui.style.display = 'none';
                                textLoading.style.display = 'inline-block';
                                
                                fetch("{{ route('dat_lich.gui') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                    },
                                    body: JSON.stringify({
                                        id_phong: {{ $phong->id }},
                                        thoi_gian_hen: thoiGian
                                    })
                                })
                                .then(async res => {
                                    const data = await res.json();
                                    
                                    if (res.ok) {
                                        alert(data.message || 'Gửi yêu cầu thành công!');
                                        if (data.success) {
                                            document.getElementById('khungDatLich').style.display = 'none';
                                        }
                                    } else {
                                        if (data.errors) {
                                            let errorMessages = Object.values(data.errors).flat().join('\n');
                                            alert('Lỗi: \n' + errorMessages);
                                        } else {
                                            alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                                        }
                                    }
                                })
                                .catch(err => {
                                    alert('Lỗi kết nối mạng hoặc máy chủ. Vui lòng thử lại!');
                                })
                                .finally(() => {
                                    btnGui.disabled = false;
                                    textGui.style.display = 'inline-block';
                                    textLoading.style.display = 'none';
                                });
                            }
                        </script>

                    <p class="text-xs text-gray-400 mt-6 text-center">Xin hãy báo là bạn tìm thấy phòng trên EasyM</p>
                    
                    <!-- Nút Báo Cáo -->
                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-center" x-data>
                        <button @click="$dispatch('open-report-modal')" class="text-sm font-bold text-gray-500 hover:text-red-600 transition-colors flex items-center gap-2">
                            <i class="fa-solid fa-flag"></i> Báo cáo tin đăng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Báo Cáo Phòng -->
<div x-data="{ 
        isOpen: false, 
        isSubmitting: false,
        lyDo: 'Thông tin ảo, lừa đảo',
        chiTiet: '',
        
        submitReport() {
            if(!this.lyDo) {
                alert('Vui lòng chọn lý do báo cáo');
                return;
            }
            
            this.isSubmitting = true;
            
            fetch('/phong-tro/{{ $phong->id }}/bao-cao', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ly_do: this.lyDo,
                    chi_tiet: this.chiTiet
                })
            })
            .then(async res => {
                const data = await res.json();
                if(res.ok && data.success) {
                    alert(data.message);
                    this.isOpen = false;
                    this.chiTiet = '';
                } else if (res.status === 401) {
                    alert('Bạn cần đăng nhập để thực hiện chức năng này!');
                    window.location.href = '/login';
                } else {
                    alert(data.message || 'Có lỗi xảy ra!');
                }
            })
            .catch(err => {
                alert('Lỗi kết nối. Vui lòng thử lại sau.');
            })
            .finally(() => {
                this.isSubmitting = false;
            });
        }
    }" 
    @open-report-modal.window="isOpen = true"
    x-show="isOpen" 
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto" 
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="isOpen" 
             @click.away="isOpen = false"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom ops-card text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Báo cáo vi phạm</h3>
                        <div class="mt-2 text-sm text-gray-500">
                            Bạn phát hiện điều gì bất thường với tin đăng này?
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-4 py-5 sm:p-6 bg-gray-50/50 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Lý do báo cáo <span class="text-red-500">*</span></label>
                    <select x-model="lyDo" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="Thông tin ảo, lừa đảo">Thông tin ảo, lừa đảo</option>
                        <option value="Đã cho thuê nhưng không cập nhật">Đã cho thuê nhưng không cập nhật</option>
                        <option value="Sai giá, sai diện tích thực tế">Sai giá, sai diện tích thực tế</option>
                        <option value="Địa chỉ không có thật">Địa chỉ không có thật</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Chi tiết thêm (tùy chọn)</label>
                    <textarea x-model="chiTiet" rows="3" placeholder="Vui lòng cung cấp thêm chi tiết để chúng tôi xử lý nhanh hơn..." class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                </div>
            </div>
            
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 gap-3">
                 <button @click="submitReport()" type="button" :disabled="isSubmitting" class="ops-action-danger w-full sm:ml-3 sm:w-auto disabled:opacity-50">
                    <span x-show="!isSubmitting">Gửi báo cáo</span>
                    <span x-show="isSubmitting" style="display: none;"><i class="fa-solid fa-spinner fa-spin"></i> Đang gửi</span>
                </button>
                 <button @click="isOpen = false" type="button" class="ops-action-secondary mt-3 w-full sm:mt-0 sm:w-auto">
                    Hủy bỏ
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
