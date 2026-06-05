@extends('layouts.app')

@section('title', 'Báo cáo Thực địa - EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('ctv.index') }}" class="text-sm font-bold text-gray-500 hover:text-blue-600 mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Quay lại Danh sách</a>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Báo cáo Thực địa</h1>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500 font-bold">Mã Phòng</div>
                <div class="text-2xl font-black text-blue-600">#{{ $phong->id }}</div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Property Info Snapshot -->
            <div class="p-6 bg-gray-50 border-b border-gray-100 flex items-start gap-4">
                <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-200 flex-shrink-0">
                    <img src="{{ json_decode($phong->hinh_anh)[0] ?? 'https://placehold.co/400x300?text=No+Image' }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 line-clamp-1">{{ $phong->tieu_de }}</h3>
                    <p class="text-sm text-gray-500 mt-1"><i class="fa-solid fa-location-dot mr-1"></i> {{ $phong->dia_chi }}</p>
                    <p class="text-sm font-bold text-blue-600 mt-1">{{ number_format($phong->gia_thue, 0, ',', '.') }} VNĐ /tháng</p>
                </div>
            </div>

            <!-- Report Form -->
            <form action="{{ route('ctv.baocao.submit', $phong->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf
                
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Checklist Tiêu chuẩn EasyM</h4>
                    
                    <div class="space-y-4">
                        <!-- Tiêu chí 1 -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-blue-300 transition-colors">
                            <div>
                                <label class="font-bold text-gray-800 text-sm block">1. Phòng giống ảnh minh họa</label>
                                <span class="text-xs text-gray-500">Tình trạng thực tế khớp với hình ảnh đăng tải trên hệ thống.</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="phong_giong_anh" value="1" class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                    <span class="ml-2 text-sm font-bold text-emerald-600"><i class="fa-solid fa-check mr-1"></i> Có</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="phong_giong_anh" value="0" class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300">
                                    <span class="ml-2 text-sm font-bold text-red-600"><i class="fa-solid fa-xmark mr-1"></i> Không</span>
                                </label>
                            </div>
                        </div>

                        <!-- Tiêu chí 2 -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-blue-300 transition-colors">
                            <div>
                                <label class="font-bold text-gray-800 text-sm block">2. Đảm bảo nguồn Nước sạch</label>
                                <span class="text-xs text-gray-500">Nguồn nước trong, không có mùi lạ, vòi nước hoạt động bình thường.</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="nuoc_sach" value="1" class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                    <span class="ml-2 text-sm font-bold text-emerald-600"><i class="fa-solid fa-check mr-1"></i> Có</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="nuoc_sach" value="0" class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300">
                                    <span class="ml-2 text-sm font-bold text-red-600"><i class="fa-solid fa-xmark mr-1"></i> Không</span>
                                </label>
                            </div>
                        </div>

                        <!-- Tiêu chí 3 -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-blue-300 transition-colors">
                            <div>
                                <label class="font-bold text-gray-800 text-sm block">3. An ninh đảm bảo</label>
                                <span class="text-xs text-gray-500">Có cổng khóa từ, camera an ninh hoặc bãi xe an toàn.</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="an_ninh" value="1" class="w-5 h-5 text-blue-600 focus:ring-blue-500 border-gray-300" required>
                                    <span class="ml-2 text-sm font-bold text-emerald-600"><i class="fa-solid fa-check mr-1"></i> Có</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="an_ninh" value="0" class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300">
                                    <span class="ml-2 text-sm font-bold text-red-600"><i class="fa-solid fa-xmark mr-1"></i> Không</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Ảnh Thực Địa -->
                <div x-data="imageUploader()" class="border-t border-gray-100 pt-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Hình ảnh chụp đối chứng thực tế <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Vui lòng chụp ít nhất 1 hình ảnh thực tế của phòng trọ để làm căn cứ kiểm duyệt.</p>
                    
                    <div class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 rounded-2xl p-6 transition-colors duration-200 bg-gray-50/50 flex flex-col items-center justify-center cursor-pointer"
                         @click="$refs.fileInput.click()">
                        <input type="file" ref="fileInput" name="anh_thuc_dia[]" id="anh_thuc_dia" multiple accept="image/*" class="hidden" @change="previewImages" required>
                        
                        <div class="text-center space-y-2">
                            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center mx-auto text-blue-500 text-xl shadow-sm">
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
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                </div>

                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 flex gap-3">
                    <i class="fa-solid fa-circle-info mt-0.5 text-blue-500"></i>
                    <p class="text-xs text-blue-800 leading-relaxed font-medium">
                        Bằng việc bấm nộp báo cáo, tôi (CTV) cam kết các thông tin đánh giá trên là hoàn toàn chính xác dựa trên quá trình kiểm tra thực địa thực tế. Mọi sai sót sẽ bị xử lý theo quy định của EasyM.
                    </p>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-10 rounded-xl shadow-lg shadow-blue-200 transition-transform transform hover:-translate-y-1">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Gửi Báo cáo
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
