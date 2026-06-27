@extends('layouts.app')

@section('title', 'Cấu hình Thông tin Bản thân - EasyM')

@section('content')
<div class="ops-page py-8 sm:py-10">
    <div class="ops-shell max-w-4xl space-y-6">
        
        <!-- Header -->
        <div class="ops-card p-5 sm:p-6 flex items-center justify-between">
            <div>
                <p class="ops-kicker">Tài khoản</p>
                <h1 class="ops-title text-2xl sm:text-3xl mt-1">Thông tin cá nhân</h1>
                <p class="text-gray-500 mt-2">Cập nhật hồ sơ để hệ thống ghép phòng (Matching) hoạt động chính xác hơn.</p>
            </div>
        </div>

        @if(session('success'))
        <div class="ops-card p-4 bg-emerald-50 border-emerald-200 text-emerald-700 font-medium flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
        @endif

        <div class="ops-card overflow-hidden">
            <form action="{{ route('profile.update') }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    
                    <!-- Cột 1: Avatar & Căn cước -->
                    <div class="md:col-span-1 space-y-6 flex flex-col items-center border-r border-gray-100 pr-0 md:pr-8">
                        <div class="w-40 h-40 relative group">
                            <img src="{{ old('anh_dai_dien', $user->anh_dai_dien) ?? 'https://ui-avatars.com/api/?name='.urlencode($user->ho_ten).'&background=e0f2fe&color=0369a1' }}" 
                                id="avatarPreview"
                                class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg">
                        </div>
                        
                        <div class="w-full">
                            <label class="block text-sm font-bold text-gray-700 mb-2 text-center">URL Ảnh đại diện</label>
                            <input type="url" name="anh_dai_dien" id="anh_dai_dien" value="{{ old('anh_dai_dien', $user->anh_dai_dien) }}" placeholder="https://..."
                                class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                onchange="document.getElementById('avatarPreview').src = this.value || 'https://ui-avatars.com/api/?name={{ urlencode($user->ho_ten) }}'">
                        </div>

                        <div class="w-full mt-4 p-4 bg-gray-50 rounded-2xl border border-gray-200 text-center">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Trạng thái Xác thực</h4>
                            @if($user->da_xac_thuc_cccd)
                                <span class="ops-badge ops-badge-green">
                                    <i class="fa-solid fa-shield-check mr-1"></i> Đã xác minh CCCD
                                </span>
                            @else
                                <span class="ops-badge ops-badge-amber">
                                    <i class="fa-solid fa-shield-halved mr-1"></i> Chưa xác minh
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Cột 2: Thông tin chi tiết -->
                    <div class="md:col-span-2 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Hồ sơ Cơ bản</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="ho_ten" class="block text-sm font-bold text-gray-700 mb-1">Họ và Tên <span class="text-red-500">*</span></label>
                                <input type="text" name="ho_ten" id="ho_ten" value="{{ old('ho_ten', $user->ho_ten) }}" required
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('ho_ten') border-red-300 @enderror">
                                @error('ho_ten') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="so_dien_thoai" class="block text-sm font-bold text-gray-700 mb-1">Số điện thoại</label>
                                <input type="text" name="so_dien_thoai" id="so_dien_thoai" value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}"
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Địa chỉ Email</label>
                                <input type="email" value="{{ $user->email }}" disabled
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
                                <p class="text-xs text-gray-400 mt-1">Không thể thay đổi email.</p>
                            </div>

                            <div>
                                <label for="thanh_pho" class="block text-sm font-bold text-gray-700 mb-1">Khu vực (Thành phố)</label>
                                <input type="text" name="thanh_pho" id="thanh_pho" value="{{ old('thanh_pho', $user->thanh_pho) }}" placeholder="VD: Hà Nội"
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 pt-4">Đặc điểm nhận diện (Dùng cho Thuật toán)</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="gioi_tinh" class="block text-sm font-bold text-gray-700 mb-1">Giới tính</label>
                                <select name="gioi_tinh" id="gioi_tinh" class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Chưa chọn --</option>
                                    <option value="nam" {{ old('gioi_tinh', $user->gioi_tinh) == 'nam' ? 'selected' : '' }}>Nam</option>
                                    <option value="nu" {{ old('gioi_tinh', $user->gioi_tinh) == 'nu' ? 'selected' : '' }}>Nữ</option>
                                    <option value="khac" {{ old('gioi_tinh', $user->gioi_tinh) == 'khac' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>

                            <div>
                                <label for="nam_sinh" class="block text-sm font-bold text-gray-700 mb-1">Năm sinh</label>
                                <input type="number" name="nam_sinh" id="nam_sinh" value="{{ old('nam_sinh', $user->nam_sinh) }}" min="1900" max="{{ date('Y') }}"
                                    class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="nghe_nghiep" class="block text-sm font-bold text-gray-700 mb-1">Trường học / Nghề nghiệp</label>
                            <input type="text" name="nghe_nghiep" id="nghe_nghiep" value="{{ old('nghe_nghiep', $user->nghe_nghiep) }}" placeholder="VD: Sinh viên ĐH Lâm Nghiệp, Kỹ sư IT..."
                                class="w-full px-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="pt-6 flex justify-end">
                            <button type="submit" class="ops-action-primary min-h-[2.75rem] px-8">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
