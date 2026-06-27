@extends('layouts.app')

@section('title', isset($phong) ? 'Sửa thông tin phòng - EasyM' : 'Đăng phòng mới - EasyM')

@section('content')
<div class="ops-page py-8 sm:py-10">
    <div class="ops-shell max-w-4xl space-y-6">
        <!-- Header Section -->
        <div class="ops-card p-5 sm:p-6 lg:p-7">
            <a href="{{ route('chutro.phong') }}" class="ops-action-secondary min-h-0 py-2 mb-4">
                <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách phòng
            </a>
            <p class="ops-kicker">Chủ trọ</p>
            <h1 class="ops-title text-2xl sm:text-3xl mt-1 flex items-center gap-3">
                <i class="fa-solid {{ isset($phong) ? 'fa-pen-to-square' : 'fa-house-medical' }} text-blue-600"></i>
                {{ isset($phong) ? 'Cập nhật thông tin phòng' : 'Đăng phòng mới' }}
            </h1>
            <p class="text-gray-500 mt-2">Điền đầy đủ thông tin, ảnh phòng và giấy tờ xác thực để tin đăng đạt hiệu quả cao hơn.</p>
        </div>

        @if ($errors->any())
            <div class="ops-card p-4 bg-red-50 text-red-700 border-red-200">
                <div class="font-bold mb-2"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Vui lòng kiểm tra lại các lỗi sau:</div>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($phong) ? route('chutro.phong.update', $phong->id) : route('chutro.phong.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($phong))
                @method('PUT')
            @endif

            <!-- Phần 1: Thông tin cơ bản -->
            <div class="ops-card overflow-hidden">
                <div class="ops-card-header p-6">
                    <p class="ops-kicker">Bước 1</p>
                    <h2 class="text-lg font-black text-gray-900 mt-1"><i class="fa-solid fa-circle-info text-blue-500 mr-2"></i>Thông tin cơ bản</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <label for="tieu_de" class="block text-sm font-bold text-gray-700 mb-2">Tiêu đề bài đăng <span class="text-red-500">*</span></label>
                        <input type="text" name="tieu_de" id="tieu_de" value="{{ old('tieu_de', $phong->tieu_de ?? '') }}" required placeholder="VD: Phòng trọ khép kín full đồ gần ĐH Lâm Nghiệp" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="gia_phong" class="block text-sm font-bold text-gray-700 mb-2">Giá phòng (VNĐ/tháng) <span class="text-red-500">*</span></label>
                        <input type="number" name="gia_phong" id="gia_phong" value="{{ old('gia_phong', $phong->gia_phong ?? '') }}" required min="0" step="1000" placeholder="VD: 1500000" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="dien_tich" class="block text-sm font-bold text-gray-700 mb-2">Diện tích (m²)</label>
                        <input type="number" name="dien_tich" id="dien_tich" value="{{ old('dien_tich', $phong->dien_tich ?? '') }}" min="0" step="0.1" placeholder="VD: 25.5" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label for="mo_ta" class="block text-sm font-bold text-gray-700 mb-2">Mô tả chi tiết <span class="text-red-500">*</span></label>
                        <textarea name="mo_ta" id="mo_ta" rows="5" required placeholder="Mô tả về tiện ích, giờ giấc, an ninh, điện nước..." class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('mo_ta', $phong->mo_ta ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Phần 2: Vị trí và Tọa độ -->
            <div class="ops-card overflow-hidden">
                <div class="ops-card-header p-6">
                    <p class="ops-kicker">Bước 2</p>
                    <h2 class="text-lg font-black text-gray-900 mt-1"><i class="fa-solid fa-map-location-dot text-blue-500 mr-2"></i>Vị trí & địa chỉ</h2>
                    <p class="text-xs text-gray-500 mt-1">Việc cung cấp toạ độ giúp phòng trọ của bạn dễ dàng được tìm thấy trên bản đồ.</p>
                </div>
                <div class="p-6 grid grid-cols-1 gap-6">
                    <div>
                        <label for="dia_chi_chi_tiet" class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ chi tiết <span class="text-red-500">*</span></label>
                        <input type="text" name="dia_chi_chi_tiet" id="dia_chi_chi_tiet" value="{{ old('dia_chi_chi_tiet', $phong->dia_chi_chi_tiet ?? '') }}" required placeholder="Số nhà, ngõ, đường, xã/phường..." class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                        <div>
                            <label for="lat" class="block text-sm font-bold text-gray-700 mb-2">Vĩ độ (Latitude) <span class="text-red-500">*</span></label>
                            <input type="text" name="lat" id="lat" value="{{ old('lat', $phong->lat ?? '') }}" required placeholder="VD: 21.028511" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                        </div>
                        <div>
                            <label for="lng" class="block text-sm font-bold text-gray-700 mb-2">Kinh độ (Longitude) <span class="text-red-500">*</span></label>
                            <input type="text" name="lng" id="lng" value="{{ old('lng', $phong->lng ?? '') }}" required placeholder="VD: 105.804817" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">
                        </div>
                        <div class="col-span-1 md:col-span-2 text-sm text-gray-600">
                            <i class="fa-solid fa-lightbulb text-amber-500 mr-1"></i> Mẹo: Lên <a href="https://maps.google.com" target="_blank" class="text-blue-600 font-bold hover:underline">Google Maps</a>, cắm ghim vào vị trí phòng của bạn, sau đó copy 2 con số tọa độ dán vào đây.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Phần 3: Hình ảnh -->
            <div class="ops-card overflow-hidden">
                <div class="ops-card-header p-6 flex justify-between items-center">
                    <div>
                        <p class="ops-kicker">Bước 3</p>
                        <h2 class="text-lg font-black text-gray-900 mt-1"><i class="fa-solid fa-images text-blue-500 mr-2"></i>Hình ảnh</h2>
                        <p class="text-xs text-gray-500 mt-1">Đăng tải hình ảnh phòng và các giấy tờ liên quan</p>
                    </div>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Ảnh không gian phòng -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh không gian phòng trọ</label>
                        <p class="text-xs text-gray-500 mb-3">Tải lên nhiều ảnh (Phòng ngủ, WC, bếp...). Hình ảnh này sẽ hiển thị công khai cho người thuê.</p>
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-blue-200 border-dashed rounded-2xl bg-blue-50/30 hover:bg-blue-50 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="anh_phong" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Tải ảnh lên</span>
                                        <input id="anh_phong" name="anh_phong[]" type="file" multiple accept="image/*" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                            </div>
                        </div>

                        <!-- Khung hiển thị ảnh xem trước ngay lập tức khi vừa chọn file -->
                        <div id="preview_anh_phong" class="mt-4 flex flex-wrap gap-2 hidden"></div>

                        @if(isset($phong) && is_array($phong->anh_phong) && count($phong->anh_phong) > 0)
                            <div class="mt-4">
                                <p class="text-sm font-bold text-gray-700 mb-2">Ảnh đã lưu:</p>
                                <div class="flex gap-2 overflow-x-auto pb-2">
                                    @foreach($phong->anh_phong as $anh)
                                        <img src="{{ $anh }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 shadow-sm shrink-0">
                                    @endforeach
                                </div>
                                <p class="text-xs text-amber-600 mt-1"><i class="fa-solid fa-triangle-exclamation"></i> Tải ảnh mới lên sẽ ghi đè toàn bộ ảnh cũ.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Ảnh giấy tờ pháp lý -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Giấy tờ Pháp lý / Sổ đỏ</label>
                        <p class="text-xs text-gray-500 mb-3">Ảnh này được <strong class="text-emerald-600">bảo mật 100%</strong> và chỉ dùng để BQL EasyM xác thực tính hợp pháp của phòng.</p>
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-emerald-300 border-dashed rounded-2xl bg-emerald-50/30 hover:bg-emerald-50 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fa-solid fa-file-contract text-4xl text-emerald-400 mb-3"></i>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="anh_phap_ly" class="relative cursor-pointer rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500">
                                        <span>Tải Giấy tờ lên</span>
                                        <input id="anh_phap_ly" name="anh_phap_ly[]" type="file" multiple accept="image/*" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">Hỗ trợ ảnh mặt trước/sau của Sổ đỏ</p>
                            </div>
                        </div>

                        <!-- Khung hiển thị tài liệu xem trước ngay lập tức khi vừa chọn file -->
                        <div id="preview_anh_phap_ly" class="mt-4 flex flex-wrap gap-2 hidden"></div>

                        @if(isset($phong) && is_array($phong->anh_phap_ly) && count($phong->anh_phap_ly) > 0)
                            <div class="mt-4">
                                <p class="text-sm font-bold text-gray-700 mb-2">Sổ đỏ đã lưu:</p>
                                <div class="flex gap-2 overflow-x-auto pb-2">
                                    @foreach($phong->anh_phap_ly as $anh)
                                        <img src="{{ $anh }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200 shadow-sm shrink-0 blur-sm hover:blur-none transition-all cursor-pointer" title="Hover để xem">
                                    @endforeach
                                </div>
                                <p class="text-xs text-amber-600 mt-1"><i class="fa-solid fa-triangle-exclamation"></i> Tải tài liệu mới lên sẽ ghi đè tài liệu cũ.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4 pt-4">
                <a href="{{ route('chutro.phong') }}" class="ops-action-secondary">
                    Hủy bỏ
                </a>
                <button type="submit" class="ops-action-primary min-h-[2.75rem] px-8">
                    <i class="fa-solid fa-floppy-disk"></i> {{ isset($phong) ? 'Lưu cập nhật' : 'Đăng phòng ngay' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function setupImagePreview(inputId, previewContainerId) {
        const input = document.getElementById(inputId);
        const previewContainer = document.getElementById(previewContainerId);

        input.addEventListener('change', function(event) {
            previewContainer.innerHTML = ''; // Clear previous previews
            const files = event.target.files;
            
            if (files.length > 0) {
                previewContainer.classList.remove('hidden');
            } else {
                previewContainer.classList.add('hidden');
                return;
            }

            Array.from(files).forEach((file, index) => {
                // Validate file size (Max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File "' + file.name + '" vượt quá 5MB. Vui lòng chọn ảnh nhỏ hơn.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'relative inline-block';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-16 h-16 object-cover rounded-lg border border-blue-300 shadow-sm shrink-0';
                    img.title = file.name;

                    // Add a tiny success icon overlay
                    const checkIcon = document.createElement('i');
                    checkIcon.className = 'fa-solid fa-circle-check absolute -top-1 -right-1 text-blue-500 bg-white rounded-full text-xs shadow-sm';

                    imgContainer.appendChild(img);
                    imgContainer.appendChild(checkIcon);
                    previewContainer.appendChild(imgContainer);
                }
                reader.readAsDataURL(file);
            });
        });
    }

    // Initialize the previews when DOM is loaded
    document.addEventListener("DOMContentLoaded", function() {
        setupImagePreview('anh_phong', 'preview_anh_phong');
        setupImagePreview('anh_phap_ly', 'preview_anh_phap_ly');
    });
</script>
@endpush
@endsection
