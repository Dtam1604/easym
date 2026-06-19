@extends('layouts.app')

@section('title', 'Khảo sát lối sống - EasyM')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Custom Radio Buttons for elegant UI */
    .radio-card input[type="radio"], .radio-card input[type="checkbox"] {
        display: none;
    }
    .radio-card label {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .radio-card input[type="radio"]:checked + label {
        border-color: #3b82f6; /* Tailwind blue-500 */
        background-color: #eff6ff; /* Tailwind blue-50 */
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
    }
    .radio-card input[type="radio"]:checked + label .icon-container {
        color: #3b82f6;
        background-color: #dbeafe; /* Tailwind blue-100 */
    }
    
    /* Checkbox cho phần ưu tiên */
    .checkbox-card input[type="checkbox"]:checked + label {
        border-color: #10b981; /* Tailwind emerald-500 */
        background-color: #ecfdf5; /* Tailwind emerald-50 */
    }
    .checkbox-card input[type="checkbox"]:checked + label .icon-container {
        color: #10b981;
        background-color: #d1fae5;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8" x-data="surveyForm()">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight mb-3">
                @if(isset($loiSongHienTai) && !empty($loiSongHienTai))
                    Cập nhật lại Khảo sát Lối sống 🔄
                @else
                    Tìm bạn ở ghép hoàn hảo! 🚀
                @endif
            </h1>
            <p class="text-lg text-gray-600">Hoàn thành 4 bước khảo sát ngắn để thuật toán EasyM gợi ý những người bạn "tâm đầu ý hợp" nhất.</p>
        </div>

        <!-- Form Box -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            
            <!-- Progress Bar -->
            <div class="bg-slate-50 px-8 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between relative">
                    <!-- Progress Line background -->
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 z-0 rounded-full"></div>
                    <!-- Active Progress Line -->
                    <div class="absolute top-1/2 left-0 h-1 bg-blue-600 -translate-y-1/2 z-0 transition-all duration-500 ease-out rounded-full"
                         x-bind:style="'width: ' + ((step - 1) / 2 * 100) + '%'"></div>

                    <!-- Steps Dots -->
                    <template x-for="i in 3" :key="i">
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                                 x-bind:class="step >= i ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 scale-110' : 'bg-white text-gray-400 border-2 border-gray-200'">
                                <span x-text="i"></span>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="flex justify-between mt-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                    <span :class="step >= 1 ? 'text-blue-600' : ''">Yêu Cầu Ở Ghép</span>
                    <span :class="step >= 2 ? 'text-blue-600' : ''">Thói Quen Sinh Hoạt</span>
                    <span :class="step >= 3 ? 'text-blue-600' : ''">Ưu Tiên Cốt Lõi</span>
                </div>
            </div>

            <!-- Form Content -->
            <!-- Chú ý thay đổi tham số Route tùy theo config web.php của bạn -->
            <form action="/api/survey/update" method="POST" id="survey-form" class="p-8 md:p-10">
                @csrf

                <!-- Bước 1: Yêu cầu ở ghép -->
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <i class="fa-solid fa-house-chimney text-blue-500"></i> Thông tin nhu cầu ở ghép
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 font-bold mb-2 text-sm">Ngân sách thuê mong muốn (VNĐ/tháng)</label>
                            <input type="number" name="tien_thue" x-model="formData.tien_thue" min="0" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-slate-50 font-medium text-gray-700 shadow-sm" required placeholder="Ví dụ: 2000000">
                        </div>
                        <div>
                            <label class="block text-gray-800 font-bold mb-2 text-sm">Số người ở ghép tối đa mong muốn (người)</label>
                            <input type="number" name="so_nguoi_to_da" x-model="formData.so_nguoi_to_da" min="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-slate-50 font-medium text-gray-700 shadow-sm" required placeholder="Ví dụ: 2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-800 font-bold mb-3 text-sm">Điều kiện cơ sở vật chất / Tiện ích mong muốn</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <template x-for="item in amenitiesList" :key="item.value">
                                <label class="flex items-center p-3 border border-gray-200 rounded-xl hover:bg-slate-50 cursor-pointer transition-all">
                                    <input type="checkbox" name="co_so_vat_chat[]" :value="item.value" x-model="formData.co_so_vat_chat" class="rounded text-blue-600 focus:ring-blue-500 h-5 w-5 mr-2">
                                    <span class="text-sm font-semibold text-gray-700" x-text="item.label"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-gray-800 font-bold text-sm">Khu vực muốn ở ghép & Thời hạn ở ghép (Nhiệm kỳ)</label>
                            <button type="button" @click="addLocationTerm()" class="text-sm font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                <i class="fa-solid fa-plus"></i> Thêm địa điểm
                            </button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(locTerm, index) in formData.dia_diem_nhiem_ky" :key="index">
                                <div class="flex items-center gap-3 p-3 bg-slate-50 border border-gray-200 rounded-xl">
                                    <div class="flex-1">
                                        <input type="text" :name="'dia_diem_nhiem_ky['+index+'][dia_diem]'" x-model="locTerm.dia_diem" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3" required placeholder="Khu vực (VD: Hà Nội hoặc Quận 7)">
                                    </div>
                                    <div class="w-40 flex items-center gap-2">
                                        <input type="number" :name="'dia_diem_nhiem_ky['+index+'][nhiem_ky]'" x-model="locTerm.nhiem_ky" min="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm py-2 px-3" required placeholder="Nhiệm kỳ (tháng)">
                                        <span class="text-xs font-bold text-gray-500 whitespace-nowrap">tháng</span>
                                    </div>
                                    <button type="button" @click="removeLocationTerm(index)" class="text-red-500 hover:text-red-700 p-2" :disabled="formData.dia_diem_nhiem_ky.length <= 1">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Bước 2: Trả lời câu hỏi (Dynamic) -->
                <div x-show="step === 2" style="display: none;" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                        <i class="fa-solid fa-clipboard-question text-blue-500"></i> Các thói quen sinh hoạt
                    </h2>

                    <div class="space-y-8">
                        @foreach($ds_tieu_chi as $tc)
                            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                                <label class="block text-gray-800 font-bold mb-4 text-lg">{{ $tc->tieu_de_hien_thi ?? $tc->ten_tieu_chi }}</label>
                                
                                @if($tc->loai_input == 'scale5')
                                    <div class="flex items-center justify-between gap-4 mb-2">
                                        <span class="text-sm font-bold text-gray-400">1 (Thấp)</span>
                                        <input type="range" name="{{ $tc->ten_tieu_chi }}" min="1" max="5" 
                                               class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600" 
                                               x-model="formData.{{ $tc->ten_tieu_chi }}">
                                        <span class="text-sm font-bold text-gray-400">5 (Cao)</span>
                                    </div>
                                    <div class="text-center mt-4">
                                        <span class="font-black text-blue-600 text-4xl" x-text="formData.{{ $tc->ten_tieu_chi }}"></span>
                                        <span class="text-xl text-gray-400 font-bold">/5</span>
                                    </div>
                                @elseif($tc->loai_input == 'boolean')
                                    <div class="flex gap-4 radio-card">
                                        <div class="flex-1">
                                            <input type="radio" id="{{ $tc->ten_tieu_chi }}_yes" name="{{ $tc->ten_tieu_chi }}" value="1" x-model="formData.{{ $tc->ten_tieu_chi }}" required>
                                            <label for="{{ $tc->ten_tieu_chi }}_yes" class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl text-center h-full">
                                                <i class="fa-solid fa-check text-3xl text-gray-400 mb-2 icon-container bg-transparent transition-colors"></i>
                                                <span class="font-bold text-gray-700">Có / Đồng ý</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <input type="radio" id="{{ $tc->ten_tieu_chi }}_no" name="{{ $tc->ten_tieu_chi }}" value="0" x-model="formData.{{ $tc->ten_tieu_chi }}">
                                            <label for="{{ $tc->ten_tieu_chi }}_no" class="flex flex-col items-center justify-center p-4 border-2 border-gray-200 rounded-xl text-center h-full">
                                                <i class="fa-solid fa-xmark text-3xl text-gray-400 mb-2 icon-container bg-transparent transition-colors"></i>
                                                <span class="font-bold text-gray-700">Không</span>
                                            </label>
                                        </div>
                                    </div>
                                @elseif($tc->loai_input == 'select')
                                    @if($tc->ten_tieu_chi == 'ton_giao')
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-2">Tôn giáo của bạn:</label>
                                                <select name="ton_giao" x-model="formData.ton_giao" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-slate-50 font-medium text-gray-700 shadow-sm" required>
                                                    <option value="khong">Không tôn giáo</option>
                                                    <option value="phat_giao">Phật giáo</option>
                                                    <option value="thien_chua">Thiên Chúa giáo</option>
                                                    <option value="tin_lanh">Tin Lành</option>
                                                    <option value="khac">Khác</option>
                                                </select>
                                            </div>
                                            <div class="mt-4 p-4 bg-blue-50/50 rounded-xl border border-blue-100 flex items-center justify-between">
                                                <div>
                                                    <p class="font-bold text-slate-800 text-sm">Yêu cầu cùng tôn giáo</p>
                                                    <p class="text-xs text-slate-500">Bật tính năng này để loại bỏ hoàn toàn các bạn khác tôn giáo (Lọc cứng)</p>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="ton_giao_loc_cung" value="1" x-model="formData.ton_giao_loc_cung" class="sr-only peer">
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                </label>
                                            </div>
                                        </div>
                                    @elseif($tc->ten_tieu_chi == 'van_hoa')
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-2">Vùng miền gốc của bạn:</label>
                                                <select name="van_hoa" x-model="formData.van_hoa" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-slate-50 font-medium text-gray-700 shadow-sm" required>
                                                    <option value="mien_bac">Miền Bắc</option>
                                                    <option value="mien_trung">Miền Trung</option>
                                                    <option value="mien_nam">Miền Nam</option>
                                                </select>
                                            </div>
                                            <div class="mt-4 p-4 bg-blue-50/50 rounded-xl border border-blue-100 flex items-center justify-between">
                                                <div>
                                                    <p class="font-bold text-slate-800 text-sm">Yêu cầu cùng vùng miền</p>
                                                    <p class="text-xs text-slate-500">Bật tính năng này để loại bỏ hoàn toàn các bạn khác vùng miền (Lọc cứng)</p>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="van_hoa_loc_cung" value="1" x-model="formData.van_hoa_loc_cung" class="sr-only peer">
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                </label>
                                            </div>
                                        </div>
                                    @else
                                        <select name="{{ $tc->ten_tieu_chi }}" x-model="formData.{{ $tc->ten_tieu_chi }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 py-3 px-4 bg-slate-50 font-medium text-gray-700" required>
                                            <option value="">-- Chọn một tùy chọn --</option>
                                        </select>
                                    @endif
                                @else
                                    <input type="text" name="{{ $tc->ten_tieu_chi }}" x-model="formData.{{ $tc->ten_tieu_chi }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required placeholder="Nhập câu trả lời...">
                                 @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bước 3: Ưu tiên cốt lõi -->
                <div x-show="step === 3" style="display: none;" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 flex items-center gap-3">
                        <i class="fa-solid fa-star text-yellow-400"></i> Tiêu chí Vàng (Top Priorities)
                    </h2>
                    <p class="text-gray-600 mb-6 text-lg">
                        Hãy chọn tối đa <span class="font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">2 tiêu chí</span> mà bạn TUYỆT ĐỐI không muốn nhượng bộ ở bạn cùng phòng. Thuật toán của chúng tôi sẽ tăng cường nhân đôi điểm (Boost) cho các lựa chọn này.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 checkbox-card">
                        @foreach($ds_tieu_chi as $tc)
                        <div>
                            <input type="checkbox" id="uu_tien_{{ $tc->ten_tieu_chi }}" name="uu_tien[]" value="{{ $tc->ten_tieu_chi }}" x-model="formData.uu_tien" :disabled="formData.uu_tien.length >= 2 && !formData.uu_tien.includes('{{ $tc->ten_tieu_chi }}')">
                            <label for="uu_tien_{{ $tc->ten_tieu_chi }}" class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:bg-gray-50 transition-colors" :class="formData.uu_tien.length >= 2 && !formData.uu_tien.includes('{{ $tc->ten_tieu_chi }}') ? 'opacity-40 cursor-not-allowed bg-gray-50' : ''">
                                <div class="icon-container w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mr-4 transition-colors text-xl">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <span class="font-bold text-gray-800 text-lg">{{ $tc->tieu_de_hien_thi ?? $tc->ten_tieu_chi }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-12 flex items-center justify-between pt-6 border-t border-gray-100">
                    <button type="button" class="px-6 py-3 bg-white border-2 border-gray-200 text-gray-600 font-bold rounded-xl hover:bg-gray-50 hover:text-gray-900 transition-colors focus:ring-4 focus:ring-gray-100"
                            x-show="step > 1" @click="prevStep()">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại
                    </button>
                    <!-- Chống float bằng div rỗng khi ko có nút Quay lại -->
                    <div x-show="step === 1"></div>

                    <button type="button" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all transform focus:ring-4 focus:ring-blue-300"
                            x-show="step < 3" @click="nextStep()" 
                            :class="!isStepValid(step) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700 hover:-translate-y-1'">
                        Tiếp tục <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                    
                    <button type="submit" class="px-8 py-3 bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 transition-all transform focus:ring-4 focus:ring-emerald-300"
                            x-show="step === 3" :class="formData.uu_tien.length === 0 ? 'opacity-90 hover:bg-emerald-600' : 'hover:bg-emerald-600 hover:-translate-y-1'">
                        <i class="fa-solid fa-check mr-2"></i> 
                        @if(isset($loiSongHienTai) && !empty($loiSongHienTai))
                            Lưu Khảo sát & Tìm bạn
                        @else
                            Hoàn tất & Tìm phòng
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('surveyForm', () => ({
            step: 1,
            amenitiesList: [
                { value: 'dieu_hoa', label: '❄️ Điều hòa' },
                { value: 'tu_lanh', label: '🧊 Tủ lạnh' },
                { value: 'may_giat', label: '🧺 Máy giặt' },
                { value: 'nong_lanh', label: '🔥 Nóng lạnh' },
                { value: 'wifi', label: '📶 Wifi' },
                { value: 'ban_cong', label: '🌅 Ban công' },
                { value: 've_sinh_khiep_kin', label: '🚽 Khép kín' },
                { value: 'bep_nau_an', label: '🍳 Bếp riêng' }
            ],
            formData: {
                tien_thue: {!! isset($loiSongHienTai['tien_thue']) ? (int)$loiSongHienTai['tien_thue'] : "''" !!},
                so_nguoi_to_da: {!! isset($loiSongHienTai['so_nguoi_to_da']) ? (int)$loiSongHienTai['so_nguoi_to_da'] : "''" !!},
                co_so_vat_chat: {!! isset($loiSongHienTai['co_so_vat_chat']) ? json_encode($loiSongHienTai['co_so_vat_chat']) : '[]' !!},
                dia_diem_nhiem_ky: {!! isset($loiSongHienTai['dia_diem_nhiem_ky']) ? json_encode($loiSongHienTai['dia_diem_nhiem_ky']) : "[{ dia_diem: '', nhiem_ky: 6 }]" !!},
                @foreach($ds_tieu_chi as $tc)
                    @php 
                        $giaTriMacDinh = '';
                        if (isset($loiSongHienTai[$tc->ten_tieu_chi])) {
                            $giaTriMacDinh = $loiSongHienTai[$tc->ten_tieu_chi];
                        } elseif ($tc->loai_input == 'scale5') {
                            $giaTriMacDinh = 3;
                        } elseif ($tc->ten_tieu_chi == 'ton_giao') {
                            $giaTriMacDinh = 'khong';
                        } elseif ($tc->ten_tieu_chi == 'van_hoa') {
                            $giaTriMacDinh = 'mien_bac';
                        }
                    @endphp
                    @if($tc->loai_input == 'scale5')
                        {{ $tc->ten_tieu_chi }}: {{ is_numeric($giaTriMacDinh) ? (int)$giaTriMacDinh : 3 }},
                    @elseif($tc->loai_input == 'boolean')
                        {{ $tc->ten_tieu_chi }}: '{{ $giaTriMacDinh }}',
                    @else
                        {{ $tc->ten_tieu_chi }}: '{{ addslashes($giaTriMacDinh) }}',
                    @endif
                @endforeach
                ton_giao_loc_cung: {!! (isset($loiSongHienTai['ton_giao_loc_cung']) && $loiSongHienTai['ton_giao_loc_cung'] && $loiSongHienTai['ton_giao_loc_cung'] !== 'false') ? 'true' : 'false' !!},
                van_hoa_loc_cung: {!! (isset($loiSongHienTai['van_hoa_loc_cung']) && $loiSongHienTai['van_hoa_loc_cung'] && $loiSongHienTai['van_hoa_loc_cung'] !== 'false') ? 'true' : 'false' !!},
                uu_tien: {!! isset($loiSongHienTai['uu_tien']) ? json_encode($loiSongHienTai['uu_tien']) : '[]' !!}
            },
            
            addLocationTerm() {
                this.formData.dia_diem_nhiem_ky.push({ dia_diem: '', nhiem_ky: 6 });
            },
            
            removeLocationTerm(index) {
                if (this.formData.dia_diem_nhiem_ky.length > 1) {
                    this.formData.dia_diem_nhiem_ky.splice(index, 1);
                }
            },
            
            nextStep() {
                if (this.isStepValid(this.step)) {
                    this.step++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },
            
            prevStep() {
                if (this.step > 1) {
                    this.step--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },
            
            isStepValid(currentStep) {
                if (currentStep === 1) {
                    if (this.formData.tien_thue === '' || this.formData.so_nguoi_to_da === '') {
                        return false;
                    }
                    if (this.formData.dia_diem_nhiem_ky.some(item => !item.dia_diem || !item.nhiem_ky)) {
                        return false;
                    }
                    return true;
                }
                if (currentStep === 2) {
                    // Check if all dynamic fields have value
                    let isValid = true;
                    @foreach($ds_tieu_chi as $tc)
                        if(this.formData.{{ $tc->ten_tieu_chi }} === '') {
                            isValid = false;
                        }
                    @endforeach
                    return isValid;
                }
                return true; 
            }
        }));
    });
</script>
@endpush
