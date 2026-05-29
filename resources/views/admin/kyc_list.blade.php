@extends('layouts.app')

@section('title', 'Quản lý Duyệt KYC - Admin EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 py-10" x-data="{ tab: 'cho_duyet' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-blue-600 mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-1"></i> Quay lại Dashboard</a>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Xét duyệt Định danh (KYC)</h1>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="flex gap-6 mb-8 border-b border-gray-200">
            <button @click="tab = 'cho_duyet'" :class="{'border-blue-500 text-blue-600': tab === 'cho_duyet', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'cho_duyet'}" class="pb-3 border-b-2 font-bold text-sm transition-colors flex items-center gap-2 px-2">
                <i class="fa-solid fa-hourglass-half"></i> Đang chờ duyệt
                <span class="bg-blue-100 text-blue-700 py-0.5 px-2 rounded-full text-xs">{{ count($ds_kyc) }}</span>
            </button>
            <button @click="tab = 'da_duyet'" :class="{'border-emerald-500 text-emerald-600': tab === 'da_duyet', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'da_duyet'}" class="pb-3 border-b-2 font-bold text-sm transition-colors flex items-center gap-2 px-2">
                <i class="fa-solid fa-shield-check"></i> Đã phê duyệt
                <span class="bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-xs">{{ count($ds_da_duyet) }}</span>
            </button>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center shadow-sm">
            <i class="fa-solid fa-circle-check text-xl mr-3 text-emerald-500"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Danh sách Chờ duyệt -->
        <div x-show="tab === 'cho_duyet'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($ds_kyc as $user)
                @php
                    $kycData = $user->thong_tin_cccd;
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                    <!-- Card Header -->
                    <div class="p-5 border-b border-gray-100 flex items-center gap-4 bg-gray-50/50">
                        <img src="{{ $user->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($user->ho_ten).'&background=random' }}" class="w-12 h-12 rounded-full border border-gray-200 object-cover">
                        <div>
                            <h3 class="font-bold text-gray-900 leading-tight">{{ $user->ho_ten }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded text-gray-600 bg-gray-200">{{ strtoupper($user->vai_tro) }}</span>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($kycData['ngay_gui'] ?? $user->updated_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body: Images -->
                    <div class="p-5 flex-1 flex flex-col gap-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs font-bold text-gray-500 mb-1">Mặt trước CCCD</p>
                                <a href="{{ $kycData['mat_truoc'] ?? '#' }}" target="_blank" class="block aspect-[3/2] bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:opacity-80 transition-opacity">
                                    <img src="{{ $kycData['mat_truoc'] ?? '' }}" class="w-full h-full object-cover" alt="Mặt trước">
                                </a>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 mb-1">Mặt sau CCCD</p>
                                <a href="{{ $kycData['mat_sau'] ?? '#' }}" target="_blank" class="block aspect-[3/2] bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:opacity-80 transition-opacity">
                                    <img src="{{ $kycData['mat_sau'] ?? '' }}" class="w-full h-full object-cover" alt="Mặt sau">
                                </a>
                            </div>
                        </div>

                        @if(isset($kycData['so_do']))
                        <div>
                            <p class="text-xs font-bold text-gray-500 mb-1"><i class="fa-solid fa-file-contract text-blue-500 mr-1"></i> Sổ đỏ / Giấy tờ nhà (Dành cho Chủ trọ)</p>
                            <a href="{{ $kycData['so_do'] }}" target="_blank" class="block h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:opacity-80 transition-opacity relative group">
                                <img src="{{ $kycData['so_do'] }}" class="w-full h-full object-cover" alt="Sổ đỏ">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold"><i class="fa-solid fa-magnifying-glass"></i> Xem chi tiết</span>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Card Footer: Actions -->
                    <div class="p-5 border-t border-gray-100 bg-gray-50 flex gap-3">
                        <form action="{{ route('admin.kyc.approve', $user->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-xl text-sm transition-colors shadow-sm">
                                <i class="fa-solid fa-check mr-1"></i> Duyệt
                            </button>
                        </form>
                        
                        <!-- Nút kích hoạt modal từ chối -->
                        <button type="button" onclick="openRejectModal({{ $user->id }}, '{{ addslashes($user->ho_ten) }}')" class="flex-1 bg-white hover:bg-red-50 text-red-600 border border-red-200 hover:border-red-300 font-bold py-2 rounded-xl text-sm transition-colors shadow-sm">
                            <i class="fa-solid fa-xmark mr-1"></i> Từ chối
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-clipboard-check text-3xl text-emerald-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Không có hồ sơ nào chờ duyệt!</h3>
                    <p class="text-gray-500 mt-2">Tất cả yêu cầu định danh đã được xử lý.</p>
                </div>
            @endforelse
        </div>

        <!-- Danh sách Đã Duyệt -->
        <div x-show="tab === 'da_duyet'" x-cloak style="display: none;" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($ds_da_duyet as $user)
                @php
                    $kycData = $user->thong_tin_cccd;
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow opacity-95">
                    <!-- Card Header -->
                    <div class="p-5 border-b border-gray-100 flex items-center gap-4 bg-emerald-50/30">
                        <img src="{{ $user->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($user->ho_ten).'&background=random' }}" class="w-12 h-12 rounded-full border border-gray-200 object-cover">
                        <div>
                            <h3 class="font-bold text-gray-900 leading-tight flex items-center gap-1.5">
                                {{ $user->ho_ten }}
                                <i class="fa-solid fa-circle-check text-emerald-500 text-sm" title="Đã xác thực"></i>
                            </h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded text-emerald-700 bg-emerald-100">{{ strtoupper($user->vai_tro) }}</span>
                                <span class="text-xs text-gray-500">Đã duyệt {{ \Carbon\Carbon::parse($user->updated_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body: Images -->
                    <div class="p-5 flex-1 flex flex-col gap-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs font-bold text-gray-500 mb-1">Mặt trước CCCD</p>
                                <a href="{{ $kycData['mat_truoc'] ?? '#' }}" target="_blank" class="block aspect-[3/2] bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:opacity-80 transition-opacity">
                                    <img src="{{ $kycData['mat_truoc'] ?? '' }}" class="w-full h-full object-cover" alt="Mặt trước">
                                </a>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 mb-1">Mặt sau CCCD</p>
                                <a href="{{ $kycData['mat_sau'] ?? '#' }}" target="_blank" class="block aspect-[3/2] bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:opacity-80 transition-opacity">
                                    <img src="{{ $kycData['mat_sau'] ?? '' }}" class="w-full h-full object-cover" alt="Mặt sau">
                                </a>
                            </div>
                        </div>

                        @if(isset($kycData['so_do']))
                        <div>
                            <p class="text-xs font-bold text-gray-500 mb-1"><i class="fa-solid fa-file-contract text-blue-500 mr-1"></i> Sổ đỏ / Giấy tờ nhà</p>
                            <a href="{{ $kycData['so_do'] }}" target="_blank" class="block h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 hover:opacity-80 transition-opacity relative group">
                                <img src="{{ $kycData['so_do'] }}" class="w-full h-full object-cover" alt="Sổ đỏ">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold"><i class="fa-solid fa-magnifying-glass"></i> Xem chi tiết</span>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-gray-100 border-dashed">
                    <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-shield-check text-3xl text-emerald-500"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Chưa có hồ sơ nào được duyệt!</h3>
                    <p class="text-gray-500 mt-2">Danh sách trống.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Từ Chối KYC -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden transform scale-95 transition-transform" id="rejectModalContent">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900"><i class="fa-solid fa-triangle-exclamation text-red-500 mr-2"></i> Từ chối Hồ sơ</h3>
            <button type="button" onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <form id="rejectForm" method="POST" action="">
            @csrf
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">Bạn đang từ chối hồ sơ định danh của <strong id="rejectUserName" class="text-gray-900"></strong>. Vui lòng cung cấp lý do để người dùng biết và khắc phục.</p>
                
                <label for="ly_do" class="block text-sm font-bold text-gray-700 mb-2">Lý do từ chối <span class="text-red-500">*</span></label>
                <textarea name="ly_do" id="ly_do" rows="3" required placeholder="Ví dụ: Ảnh mặt trước bị mờ, Sổ đỏ không rõ chữ ký..." class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
            </div>
            <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()" class="px-5 py-2.5 rounded-xl font-bold text-gray-700 hover:bg-gray-200 transition-colors">Hủy</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold bg-red-600 hover:bg-red-700 text-white shadow-sm shadow-red-200 transition-colors">Xác nhận Từ chối</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(userId, userName) {
        document.getElementById('rejectUserName').innerText = userName;
        document.getElementById('rejectForm').action = `/admin/kyc/${userId}/reject`;
        
        const modal = document.getElementById('rejectModal');
        const modalContent = document.getElementById('rejectModalContent');
        
        modal.classList.remove('hidden');
        // Kích hoạt animation
        setTimeout(() => {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        const modalContent = document.getElementById('rejectModalContent');
        
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('ly_do').value = ''; // Reset form
        }, 200);
    }
</script>
@endsection
