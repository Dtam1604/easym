@extends('layouts.app')

@section('title', 'Khảo sát lối sống - EasyM')

@push('styles')
<style>
    .survey-choice input[type="radio"],
    .survey-choice input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .survey-choice label {
        cursor: pointer;
    }

    .survey-choice input:checked + label {
        border-color: var(--easym-accent);
        background: #f5f9ff;
        box-shadow: 0 16px 34px rgb(23 105 224 / 0.12);
    }

    .survey-choice input:checked + label .choice-icon {
        background: var(--easym-accent);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="min-h-[100dvh] bg-slate-50 py-8 sm:py-10" x-data="surveyForm()">
    <div class="easym-shell">
        <div class="grid gap-6 lg:grid-cols-[0.9fr_2fr] lg:items-start">
            <aside class="lg:sticky lg:top-24">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-blue-600">Khảo sát EasyM</p>
                    <h1 class="mt-3 text-3xl font-black leading-tight tracking-tight text-slate-950 sm:text-4xl">
                        @if(isset($loiSongHienTai) && !empty($loiSongHienTai))
                            Cập nhật hồ sơ ở ghép
                        @else
                            Tạo hồ sơ ở ghép phù hợp
                        @endif
                    </h1>
                    <p class="mt-4 text-sm leading-6 text-slate-600">
                        Hoàn thành 3 bước để EasyM hiểu ngân sách, khu vực, thói quen và những tiêu chí bạn không muốn nhượng bộ.
                    </p>

                    <div class="mt-7 space-y-3">
                        <template x-for="item in stepMeta" :key="item.id">
                            <div class="flex items-center gap-3 rounded-2xl border p-3 transition"
                                 :class="step >= item.id ? 'border-blue-100 bg-blue-50/70' : 'border-slate-200 bg-slate-50'">
                                <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-sm font-black"
                                     :class="step >= item.id ? 'bg-blue-600 text-white' : 'bg-white text-slate-400 border border-slate-200'">
                                    <span x-text="item.id"></span>
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900" x-text="item.title"></p>
                                    <p class="text-xs font-medium text-slate-500" x-text="item.desc"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </aside>

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 p-5 sm:p-6">
                    <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-blue-600 transition-all duration-300"
                             x-bind:style="'width: ' + (step / 3 * 100) + '%'"></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs font-bold text-slate-500">
                        <span x-text="'Bước ' + step + '/3'"></span>
                        <span x-text="stepMeta[step - 1].title"></span>
                    </div>
                </div>

                <form action="/api/survey/update" method="POST" id="survey-form" class="p-5 sm:p-8">
                    @csrf

                    <div x-show="step === 1"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-7">
                        <div>
                            <h2 class="text-2xl font-black tracking-tight text-slate-950">Nhu cầu phòng và khu vực</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Nhập phần bắt buộc trước để hệ thống hiểu phạm vi tìm kiếm của bạn.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-extrabold text-slate-800">Ngân sách thuê mong muốn</label>
                                <div class="relative">
                                    <input type="number" name="tien_thue" x-model="formData.tien_thue" min="0" class="w-full py-3.5 pl-4 pr-16 text-sm font-bold" required placeholder="2000000">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">VNĐ</span>
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-extrabold text-slate-800">Số người ở tối đa</label>
                                <input type="number" name="so_nguoi_to_da" x-model="formData.so_nguoi_to_da" min="1" class="w-full py-3.5 px-4 text-sm font-bold" required placeholder="2">
                            </div>
                        </div>

                        <div>
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <label class="block text-sm font-extrabold text-slate-800">Khu vực và thời hạn ở ghép</label>
                                <button type="button" @click="addLocationTerm()" class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-extrabold text-blue-700 hover:bg-blue-100">
                                    <i class="fa-solid fa-plus"></i>
                                    Thêm khu vực
                                </button>
                            </div>
                            <div class="space-y-3">
                                <template x-for="(locTerm, index) in formData.dia_diem_nhiem_ky" :key="index">
                                    <div class="grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3 sm:grid-cols-[1fr_160px_40px]">
                                        <input type="text" :name="'dia_diem_nhiem_ky['+index+'][dia_diem]'" x-model="locTerm.dia_diem" class="w-full px-4 py-3 text-sm font-semibold" required placeholder="Ví dụ: Cầu Giấy, Hà Nội">
                                        <div class="relative">
                                            <input type="number" :name="'dia_diem_nhiem_ky['+index+'][nhiem_ky]'" x-model="locTerm.nhiem_ky" min="1" class="w-full py-3 pl-4 pr-14 text-sm font-semibold" required placeholder="6">
                                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">tháng</span>
                                        </div>
                                        <button type="button" @click="removeLocationTerm(index)" class="grid h-10 w-10 place-items-center rounded-full text-slate-400 hover:bg-red-50 hover:text-red-600" :disabled="formData.dia_diem_nhiem_ky.length <= 1">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="mb-3 block text-sm font-extrabold text-slate-800">Tiện ích mong muốn</label>
                            <div class="survey-choice grid grid-cols-2 gap-3 md:grid-cols-4">
                                <template x-for="item in amenitiesList" :key="item.value">
                                    <div class="relative">
                                        <input type="checkbox" name="co_so_vat_chat[]" :id="'amenity_' + item.value" :value="item.value" x-model="formData.co_so_vat_chat">
                                        <label :for="'amenity_' + item.value" class="flex h-full items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3 text-sm font-bold text-slate-700 hover:border-blue-100 hover:bg-blue-50/40">
                                            <span class="choice-icon grid h-8 w-8 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-500">
                                                <i :class="item.icon"></i>
                                            </span>
                                            <span x-text="item.label"></span>
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div x-show="step === 2" style="display: none;"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">
                        <div>
                            <h2 class="text-2xl font-black tracking-tight text-slate-950">Thói quen sinh hoạt</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Các câu trả lời này giúp thuật toán ưu tiên người có nhịp sống tương đồng.</p>
                        </div>

                        @foreach($ds_tieu_chi as $tc)
                            <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                <label class="mb-4 block text-base font-black text-slate-900">{{ $tc->tieu_de_hien_thi ?? $tc->ten_tieu_chi }}</label>

                                @if($tc->loai_input == 'scale5')
                                    <div class="grid gap-4 sm:grid-cols-[72px_1fr_72px] sm:items-center">
                                        <span class="text-xs font-extrabold text-slate-400">Thấp</span>
                                        <input type="range" name="{{ $tc->ten_tieu_chi }}" min="1" max="5"
                                               class="h-3 w-full cursor-pointer appearance-none rounded-full bg-slate-200 accent-blue-600"
                                               x-model="formData.{{ $tc->ten_tieu_chi }}">
                                        <span class="text-right text-xs font-extrabold text-slate-400">Cao</span>
                                    </div>
                                    <div class="mt-4 inline-flex items-end rounded-2xl bg-blue-50 px-4 py-2">
                                        <span class="text-3xl font-black text-blue-600" x-text="formData.{{ $tc->ten_tieu_chi }}"></span>
                                        <span class="mb-1 ml-1 text-sm font-bold text-slate-400">/5</span>
                                    </div>
                                @elseif($tc->loai_input == 'boolean')
                                    <div class="survey-choice grid gap-3 sm:grid-cols-2">
                                        <div class="relative">
                                            <input type="radio" id="{{ $tc->ten_tieu_chi }}_yes" name="{{ $tc->ten_tieu_chi }}" value="1" x-model="formData.{{ $tc->ten_tieu_chi }}" required>
                                            <label for="{{ $tc->ten_tieu_chi }}_yes" class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4">
                                                <span class="choice-icon grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-slate-500"><i class="fa-solid fa-check"></i></span>
                                                <span class="font-extrabold text-slate-800">Có / đồng ý</span>
                                            </label>
                                        </div>
                                        <div class="relative">
                                            <input type="radio" id="{{ $tc->ten_tieu_chi }}_no" name="{{ $tc->ten_tieu_chi }}" value="0" x-model="formData.{{ $tc->ten_tieu_chi }}">
                                            <label for="{{ $tc->ten_tieu_chi }}_no" class="flex items-center gap-3 rounded-2xl border border-slate-200 p-4">
                                                <span class="choice-icon grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-slate-500"><i class="fa-solid fa-xmark"></i></span>
                                                <span class="font-extrabold text-slate-800">Không</span>
                                            </label>
                                        </div>
                                    </div>
                                @elseif($tc->loai_input == 'select')
                                    @if($tc->ten_tieu_chi == 'ton_giao')
                                        <div class="space-y-4">
                                            <select name="ton_giao" x-model="formData.ton_giao" class="w-full px-4 py-3.5 text-sm font-bold" required>
                                                <option value="khong">Không tôn giáo</option>
                                                <option value="phat_giao">Phật giáo</option>
                                                <option value="thien_chua">Thiên Chúa giáo</option>
                                                <option value="tin_lanh">Tin Lành</option>
                                                <option value="khac">Khác</option>
                                            </select>
                                            <label class="flex items-center justify-between gap-4 rounded-2xl border border-blue-100 bg-blue-50/60 p-4">
                                                <span>
                                                    <span class="block text-sm font-extrabold text-slate-900">Yêu cầu cùng tôn giáo</span>
                                                    <span class="block text-xs font-medium text-slate-500">Bật nếu đây là điều kiện bắt buộc.</span>
                                                </span>
                                                <input type="checkbox" name="ton_giao_loc_cung" value="1" x-model="formData.ton_giao_loc_cung" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                                            </label>
                                        </div>
                                    @elseif($tc->ten_tieu_chi == 'van_hoa')
                                        <div class="space-y-4">
                                            <select name="van_hoa" x-model="formData.van_hoa" class="w-full px-4 py-3.5 text-sm font-bold" required>
                                                <option value="mien_bac">Miền Bắc</option>
                                                <option value="mien_trung">Miền Trung</option>
                                                <option value="mien_nam">Miền Nam</option>
                                            </select>
                                            <label class="flex items-center justify-between gap-4 rounded-2xl border border-blue-100 bg-blue-50/60 p-4">
                                                <span>
                                                    <span class="block text-sm font-extrabold text-slate-900">Yêu cầu cùng vùng miền</span>
                                                    <span class="block text-xs font-medium text-slate-500">Bật nếu đây là điều kiện bắt buộc.</span>
                                                </span>
                                                <input type="checkbox" name="van_hoa_loc_cung" value="1" x-model="formData.van_hoa_loc_cung" class="h-5 w-5 rounded border-slate-300 text-blue-600">
                                            </label>
                                        </div>
                                    @else
                                        <select name="{{ $tc->ten_tieu_chi }}" x-model="formData.{{ $tc->ten_tieu_chi }}" class="w-full px-4 py-3.5 text-sm font-bold" required>
                                            <option value="">Chọn một tùy chọn</option>
                                        </select>
                                    @endif
                                @else
                                    <input type="text" name="{{ $tc->ten_tieu_chi }}" x-model="formData.{{ $tc->ten_tieu_chi }}" class="w-full px-4 py-3.5 text-sm font-bold" required placeholder="Nhập câu trả lời">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div x-show="step === 3" style="display: none;"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6">
                        <div>
                            <h2 class="text-2xl font-black tracking-tight text-slate-950">Tiêu chí không nhượng bộ</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Chọn tối đa 2 tiêu chí quan trọng nhất. EasyM sẽ tăng trọng số cho các tiêu chí này khi gợi ý bạn ở ghép.</p>
                        </div>

                        <div class="survey-choice grid gap-3 sm:grid-cols-2">
                            @foreach($ds_tieu_chi as $tc)
                                <div class="relative">
                                    <input type="checkbox" id="uu_tien_{{ $tc->ten_tieu_chi }}" name="uu_tien[]" value="{{ $tc->ten_tieu_chi }}" x-model="formData.uu_tien" :disabled="formData.uu_tien.length >= 2 && !formData.uu_tien.includes('{{ $tc->ten_tieu_chi }}')">
                                    <label for="uu_tien_{{ $tc->ten_tieu_chi }}" class="flex h-full items-center gap-3 rounded-2xl border border-slate-200 p-4 hover:border-blue-100 hover:bg-blue-50/40" :class="formData.uu_tien.length >= 2 && !formData.uu_tien.includes('{{ $tc->ten_tieu_chi }}') ? 'opacity-40 cursor-not-allowed bg-slate-50' : ''">
                                        <span class="choice-icon grid h-10 w-10 place-items-center rounded-full bg-slate-100 text-slate-500"><i class="fa-solid fa-star"></i></span>
                                        <span class="font-extrabold text-slate-800">{{ $tc->tieu_de_hien_thi ?? $tc->ten_tieu_chi }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-10 flex items-center justify-between border-t border-slate-100 pt-6">
                        <button type="button" class="easym-btn easym-btn-secondary px-5 py-3 text-sm" x-show="step > 1" @click="prevStep()">
                            <i class="fa-solid fa-arrow-left"></i>
                            Quay lại
                        </button>
                        <div x-show="step === 1"></div>

                        <button type="button" class="easym-btn easym-btn-primary px-6 py-3 text-sm"
                                x-show="step < 3" @click="nextStep()"
                                :class="!isStepValid(step) ? 'opacity-50 cursor-not-allowed' : ''">
                            Tiếp tục
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>

                        <button type="submit" class="easym-btn easym-btn-primary px-6 py-3 text-sm" x-show="step === 3">
                            <i class="fa-solid fa-check"></i>
                            @if(isset($loiSongHienTai) && !empty($loiSongHienTai))
                                Lưu khảo sát
                            @else
                                Hoàn tất
                            @endif
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('surveyForm', () => ({
            step: 1,
            stepMeta: [
                { id: 1, title: 'Nhu cầu phòng', desc: 'Ngân sách, khu vực, tiện ích' },
                { id: 2, title: 'Lối sống', desc: 'Thói quen và nhịp sinh hoạt' },
                { id: 3, title: 'Ưu tiên', desc: 'Tối đa 2 tiêu chí cốt lõi' },
            ],
            amenitiesList: [
                { value: 'dieu_hoa', label: 'Điều hòa', icon: 'fa-solid fa-wind' },
                { value: 'tu_lanh', label: 'Tủ lạnh', icon: 'fa-solid fa-box' },
                { value: 'may_giat', label: 'Máy giặt', icon: 'fa-solid fa-soap' },
                { value: 'nong_lanh', label: 'Nóng lạnh', icon: 'fa-solid fa-temperature-high' },
                { value: 'wifi', label: 'Wifi', icon: 'fa-solid fa-wifi' },
                { value: 'ban_cong', label: 'Ban công', icon: 'fa-solid fa-door-open' },
                { value: 've_sinh_khiep_kin', label: 'Khép kín', icon: 'fa-solid fa-shield-halved' },
                { value: 'bep_nau_an', label: 'Bếp riêng', icon: 'fa-solid fa-utensils' }
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
