@extends('layouts.app')

@section('title', 'Định danh điện tử (KYC) - EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Xác thực Định danh (KYC)</h1>
            <p class="text-gray-500">Tải lên giấy tờ tùy thân của bạn để nâng cấp độ tin cậy của tài khoản.</p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center shadow-sm">
            <i class="fa-solid fa-circle-check text-2xl mr-3 text-emerald-500"></i>
            <div>
                <h4 class="font-bold text-sm">Thành công!</h4>
                <p class="text-sm mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            
            <!-- Status Card -->
            <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Trạng thái hồ sơ</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($user->da_xac_thuc_cccd == true)
                            Hồ sơ của bạn đã được kiểm duyệt và hợp lệ.
                        @elseif(isset($user->thong_tin_cccd['trang_thai']) && $user->thong_tin_cccd['trang_thai'] === 'tu_choi')
                            Hồ sơ của bạn bị từ chối. Vui lòng xem lý do bên dưới và gửi lại.
                        @elseif($user->thong_tin_cccd !== null)
                            Hồ sơ của bạn đang trong hàng đợi phê duyệt.
                        @else
                            Bạn chưa gửi hồ sơ định danh nào.
                        @endif
                    </p>
                </div>
                <div>
                    @if($user->da_xac_thuc_cccd == true)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-black bg-emerald-100 text-emerald-700 shadow-sm border border-emerald-200">
                            <i class="fa-solid fa-shield-check mr-2"></i> Đã xác thực
                        </span>
                    @elseif(isset($user->thong_tin_cccd['trang_thai']) && $user->thong_tin_cccd['trang_thai'] === 'tu_choi')
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-black bg-red-100 text-red-700 shadow-sm border border-red-200">
                            <i class="fa-solid fa-circle-xmark mr-2"></i> Bị từ chối
                        </span>
                    @elseif($user->thong_tin_cccd !== null)
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-black bg-amber-100 text-amber-700 shadow-sm border border-amber-200">
                            <i class="fa-solid fa-clock-rotate-left mr-2"></i> Chờ duyệt
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-black bg-gray-100 text-gray-700 shadow-sm border border-gray-200">
                            <i class="fa-solid fa-triangle-exclamation mr-2"></i> Chưa xác thực
                        </span>
                    @endif
                </div>
            </div>

            <!-- Upload Form -->
            <div class="p-8">
                @if(isset($user->thong_tin_cccd['trang_thai']) && $user->thong_tin_cccd['trang_thai'] === 'tu_choi')
                    <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-xl">
                        <h4 class="text-red-800 font-bold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Lý do từ chối từ Admin:</h4>
                        <p class="text-red-600 text-sm italic">"{{ $user->thong_tin_cccd['ly_do'] ?? 'Không có lý do cụ thể' }}"</p>
                        <p class="text-sm text-gray-600 mt-2">Vui lòng tải lên lại các hình ảnh đúng theo yêu cầu.</p>
                    </div>
                @endif

                @if($user->da_xac_thuc_cccd == false)
                    <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Mặt trước -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh mặt trước CCCD/Thẻ SV <span class="text-red-500">*</span></label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-blue-400 transition-colors bg-gray-50 relative group overflow-hidden" id="container_mat_truoc_cccd">
                                    <div class="space-y-2 text-center relative z-10" id="placeholder_mat_truoc_cccd">
                                        <i class="fa-regular fa-id-card text-4xl text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="mat_truoc_cccd" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-2 py-1 shadow-sm border border-gray-200">
                                                <span>Tải ảnh lên</span>
                                                <input id="mat_truoc_cccd" name="mat_truoc_cccd" type="file" class="sr-only" accept="image/*" required onchange="previewImage(event, 'preview_mat_truoc_cccd', 'placeholder_mat_truoc_cccd')">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (Tối đa 5MB)</p>
                                    </div>
                                    <img id="preview_mat_truoc_cccd" class="hidden absolute inset-0 w-full h-full object-cover rounded-2xl z-0" src="#" alt="Preview">
                                    <label for="mat_truoc_cccd" class="absolute inset-0 z-20 cursor-pointer hidden" id="label_overlay_mat_truoc_cccd"></label>
                                </div>
                                @error('mat_truoc_cccd') <p class="text-red-500 text-xs mt-2 font-medium"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                            </div>

                            <!-- Mặt sau -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh mặt sau CCCD/Thẻ SV <span class="text-red-500">*</span></label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-blue-400 transition-colors bg-gray-50 relative group overflow-hidden" id="container_mat_sau_cccd">
                                    <div class="space-y-2 text-center relative z-10" id="placeholder_mat_sau_cccd">
                                        <i class="fa-regular fa-address-card text-4xl text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="mat_sau_cccd" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-2 py-1 shadow-sm border border-gray-200">
                                                <span>Tải ảnh lên</span>
                                                <input id="mat_sau_cccd" name="mat_sau_cccd" type="file" class="sr-only" accept="image/*" required onchange="previewImage(event, 'preview_mat_sau_cccd', 'placeholder_mat_sau_cccd')">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (Tối đa 5MB)</p>
                                    </div>
                                    <img id="preview_mat_sau_cccd" class="hidden absolute inset-0 w-full h-full object-cover rounded-2xl z-0" src="#" alt="Preview">
                                    <label for="mat_sau_cccd" class="absolute inset-0 z-20 cursor-pointer hidden" id="label_overlay_mat_sau_cccd"></label>
                                </div>
                                @error('mat_sau_cccd') <p class="text-red-500 text-xs mt-2 font-medium"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                            </div>
                            
                            @if($user->vai_tro === 'chu_tro')
                            <!-- Sổ đỏ (Chỉ dành cho chủ trọ) -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh chụp Sổ đỏ / Giấy tờ nhà đất <span class="text-red-500">*</span></label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:border-blue-400 transition-colors bg-gray-50 relative group overflow-hidden" id="container_so_do">
                                    <div class="space-y-2 text-center relative z-10" id="placeholder_so_do">
                                        <i class="fa-solid fa-file-contract text-4xl text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="so_do" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-2 py-1 shadow-sm border border-gray-200">
                                                <span>Tải ảnh lên</span>
                                                <input id="so_do" name="so_do" type="file" class="sr-only" accept="image/*" required onchange="previewImage(event, 'preview_so_do', 'placeholder_so_do')">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (Tối đa 5MB) - Yêu cầu đối với Chủ trọ</p>
                                    </div>
                                    <img id="preview_so_do" class="hidden absolute inset-0 w-full h-full object-cover rounded-2xl z-0" src="#" alt="Preview">
                                    <label for="so_do" class="absolute inset-0 z-20 cursor-pointer hidden" id="label_overlay_so_do"></label>
                                </div>
                                @error('so_do') <p class="text-red-500 text-xs mt-2 font-medium"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                            </div>
                            @endif
                        </div>

                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                            <p class="text-xs text-amber-800 leading-relaxed font-medium">
                                <i class="fa-solid fa-circle-info mr-1 text-amber-500"></i> Bằng việc nhấn nút "Gửi hồ sơ KYC", bạn đồng ý cho phép EasyM thu thập và lưu trữ hình ảnh của bạn trên máy chủ (Sử dụng Laravel Storage). Thông vị này chỉ dùng cho mục đích định danh để bảo vệ cộng đồng và hoàn toàn được mã hóa.
                            </p>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 px-8 rounded-xl shadow-lg shadow-blue-200 transition-transform transform hover:-translate-y-1">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Gửi hồ sơ KYC
                            </button>
                        </div>
                    </form>
                @else
                    <!-- Hiển thị lại hồ sơ đã duyệt -->
                    <div class="text-center py-10">
                        <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-check text-4xl text-emerald-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Hồ sơ của bạn đã được kiểm duyệt!</h3>
                        <p class="text-gray-500 max-w-md mx-auto">Danh tính của bạn đã được lưu trữ an toàn trong hệ thống. Bạn không cần phải thực hiện thao tác này nữa.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event, previewId, placeholderId) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById(previewId);
            var placeholder = document.getElementById(placeholderId);
            var labelOverlay = document.getElementById('label_overlay_' + event.target.name);
            
            output.src = reader.result;
            output.classList.remove('hidden');
            placeholder.classList.add('hidden');
            
            if (labelOverlay) {
                labelOverlay.classList.remove('hidden');
            }
        }
        
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
@endpush
@endsection
