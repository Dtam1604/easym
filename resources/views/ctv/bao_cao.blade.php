@extends('layouts.app')

@section('title', 'Báo cáo Thực địa - EasyM')

@section('content')
<div class="ops-page py-8 sm:py-10">
    <div class="ops-shell max-w-5xl space-y-6">

        <div class="ops-card overflow-hidden">
            <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                <div class="space-y-2">
                    <a href="{{ route('ctv.index') }}" class="ops-action-secondary min-h-0 py-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        Quay lại danh sách
                    </a>
                    <div>
                        <p class="ops-kicker">Báo cáo thực địa</p>
                        <h1 class="ops-title text-2xl sm:text-3xl mt-1">Đối chiếu phòng trước khi phê duyệt</h1>
                    </div>
                </div>
                <div class="ops-badge ops-badge-blue text-base px-4 py-2">
                    <span class="text-xs text-blue-700">Mã phòng</span>
                    <strong>#{{ $phong->id }}</strong>
                </div>
            </div>
        </div>

        <div class="ops-card overflow-hidden">
            <div class="ops-card-header p-5 sm:p-6 flex items-start gap-4">
                <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-200 flex-shrink-0 border border-gray-200">
                    <img src="{{ $phong->anh_phong[0] ?? 'https://placehold.co/400x300?text=No+Image' }}" class="w-full h-full object-cover" alt="Ảnh phòng {{ $phong->id }}">
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-xl font-black text-gray-900 line-clamp-2">{{ $phong->tieu_de }}</h2>
                    <p class="text-sm text-gray-500 mt-2 flex items-start gap-2">
                        <i class="fa-solid fa-location-dot mt-0.5"></i>
                        <span>{{ $phong->dia_chi_chi_tiet }}</span>
                    </p>
                    <p class="text-base font-black text-blue-600 mt-2">{{ number_format($phong->gia_phong, 0, ',', '.') }} VNĐ / tháng</p>
                </div>
            </div>

            <form action="{{ route('ctv.baocao.submit', $phong->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf
                
                <div>
                    <div class="mb-4">
                        <p class="ops-kicker">Checklist</p>
                        <h3 class="text-lg font-black text-gray-900 mt-1">Tiêu chuẩn xác minh EasyM</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="ops-check-row">
                            <div class="min-w-0">
                                <label class="font-bold text-gray-800 text-sm block">1. Phòng giống ảnh minh họa</label>
                                <span class="text-xs text-gray-500">Tình trạng thực tế khớp với hình ảnh đăng tải trên hệ thống.</span>
                            </div>
                            <div class="ops-radio-group">
                                <label class="ops-radio-pill text-emerald-700">
                                    <input type="radio" name="phong_giong_anh" value="1" class="text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                    <span><i class="fa-solid fa-check mr-1"></i> Có</span>
                                </label>
                                <label class="ops-radio-pill text-red-700">
                                    <input type="radio" name="phong_giong_anh" value="0" class="text-red-600 focus:ring-red-500 border-gray-300">
                                    <span><i class="fa-solid fa-xmark mr-1"></i> Không</span>
                                </label>
                            </div>
                        </div>

                        <div class="ops-check-row">
                            <div class="min-w-0">
                                <label class="font-bold text-gray-800 text-sm block">2. Đảm bảo nguồn Nước sạch</label>
                                <span class="text-xs text-gray-500">Nguồn nước trong, không có mùi lạ, vòi nước hoạt động bình thường.</span>
                            </div>
                            <div class="ops-radio-group">
                                <label class="ops-radio-pill text-emerald-700">
                                    <input type="radio" name="nuoc_sach" value="1" class="text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                    <span><i class="fa-solid fa-check mr-1"></i> Có</span>
                                </label>
                                <label class="ops-radio-pill text-red-700">
                                    <input type="radio" name="nuoc_sach" value="0" class="text-red-600 focus:ring-red-500 border-gray-300">
                                    <span><i class="fa-solid fa-xmark mr-1"></i> Không</span>
                                </label>
                            </div>
                        </div>

                        <div class="ops-check-row">
                            <div class="min-w-0">
                                <label class="font-bold text-gray-800 text-sm block">3. An ninh đảm bảo</label>
                                <span class="text-xs text-gray-500">Có cổng khóa từ, camera an ninh hoặc bãi xe an toàn.</span>
                            </div>
                            <div class="ops-radio-group">
                                <label class="ops-radio-pill text-emerald-700">
                                    <input type="radio" name="an_ninh" value="1" class="text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                    <span><i class="fa-solid fa-check mr-1"></i> Có</span>
                                </label>
                                <label class="ops-radio-pill text-red-700">
                                    <input type="radio" name="an_ninh" value="0" class="text-red-600 focus:ring-red-500 border-gray-300">
                                    <span><i class="fa-solid fa-xmark mr-1"></i> Không</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-data="imageUploader()" class="border-t border-gray-100 pt-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Hình ảnh chụp đối chứng thực tế <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Vui lòng chụp ít nhất 1 hình ảnh thực tế của phòng trọ để làm căn cứ kiểm duyệt.</p>
                    
                    <div class="relative border-2 border-dashed border-blue-200 hover:border-blue-500 rounded-2xl p-6 sm:p-8 transition-colors duration-200 bg-blue-50/50 flex flex-col items-center justify-center cursor-pointer"
                         @click="$refs.fileInput.click()">
                        <input type="file" x-ref="fileInput" name="anh_thuc_dia[]" id="anh_thuc_dia" multiple accept="image/*" class="hidden" @change="previewImages" @click.stop required>
                         
                        <div class="text-center space-y-2">
                            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mx-auto text-blue-600 text-xl shadow-sm border border-blue-100">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                            <div class="text-sm font-bold text-gray-700">Chọn hoặc Kéo thả ảnh vào đây</div>
                            <div class="text-xs text-gray-400">Hỗ trợ JPG, PNG, JPEG. Tối đa 5MB/ảnh (Cần ít nhất 1 ảnh)</div>
                        </div>
                    </div>

                    <!-- Previews Grid -->
                    <template x-if="previews.length > 0">
                        <div class="mt-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                            <template x-for="(src, index) in previews" :key="index">
                                <div class="relative aspect-square rounded-xl overflow-hidden border border-gray-200 group shadow-sm bg-white">
                                    <img :src="src" class="w-full h-full object-cover">
                                    <button type="button" @click.stop="removeImage(index)" 
                                            class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center text-xs hover:bg-red-600 transition-colors shadow-md">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                    @error('anh_thuc_dia')
                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ghi_chu_thuc_dia" class="block text-sm font-bold text-gray-700 mb-2">Ghi chú thêm (Tùy chọn)</label>
                    <textarea id="ghi_chu_thuc_dia" name="ghi_chu_thuc_dia" rows="4" placeholder="Nhập nhận xét chi tiết của bạn về phòng trọ này..."
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white"></textarea>
                </div>

                <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100 flex gap-3">
                    <i class="fa-solid fa-circle-info mt-0.5 text-blue-500"></i>
                    <p class="text-xs text-blue-800 leading-relaxed font-medium">
                        Bằng việc bấm nộp báo cáo, tôi (CTV) cam kết các thông tin đánh giá trên là hoàn toàn chính xác dựa trên quá trình kiểm tra thực địa thực tế. Mọi sai sót sẽ bị xử lý theo quy định của EasyM.
                    </p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-2">
                    <a href="{{ route('ctv.index') }}" class="ops-action-secondary">
                        Hủy
                    </a>
                    <button type="submit" class="ops-action-primary min-h-[2.75rem] px-8">
                        <i class="fa-solid fa-paper-plane"></i> Gửi báo cáo
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageUploader', () => ({
            previews: [],
            files: [],
            previewImages(event) {
                const filesList = event.target.files;
                if (!filesList) return;
                for (let i = 0; i < filesList.length; i++) {
                    const file = filesList[i];
                    this.files.push(file);
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previews.push(e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
                this.syncInput();
            },
            removeImage(index) {
                this.previews.splice(index, 1);
                this.files.splice(index, 1);
                this.syncInput();
            },
            syncInput() {
                const dt = new DataTransfer();
                this.files.forEach(file => dt.items.add(file));
                this.$refs.fileInput.files = dt.files;
            }
        }));
    });
</script>
@endpush
@endsection
