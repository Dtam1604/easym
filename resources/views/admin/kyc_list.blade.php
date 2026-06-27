@extends('layouts.app')

@section('title', 'Quản lý Duyệt KYC - Admin EasyM')

@section('content')
<div class="flex min-h-[calc(100dvh-72px)] ops-page font-sans" x-data="{ tab: 'cho_duyet' }">
    @include('admin.partials.sidebar', ['active' => 'kyc'])

    <main class="flex-1 flex flex-col ops-main">
        <header class="ops-header flex items-center justify-between px-6 lg:px-8 z-10 sticky top-0">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.dashboard') }}" class="ops-action-secondary min-h-0 w-10 h-10 p-0" aria-label="Quay lại dashboard">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <p class="ops-kicker">Định danh người dùng</p>
                    <h1 class="text-xl font-black text-gray-900">Xét duyệt hồ sơ KYC</h1>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-3">
                <span class="ops-badge ops-badge-amber">{{ count($ds_kyc) }} chờ duyệt</span>
                <span class="ops-badge ops-badge-green">{{ count($ds_da_duyet) }} đã duyệt</span>
            </div>
        </header>

        <div class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
            @if(session('success'))
                <div class="ops-card bg-emerald-50 border-emerald-200 p-4 text-emerald-800 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="ops-card overflow-hidden">
                <div class="ops-card-header p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <p class="ops-kicker">Hồ sơ xác minh</p>
                        <h2 class="text-lg font-black text-gray-900 mt-1">Đối chiếu giấy tờ và trạng thái tài khoản</h2>
                        <p class="ops-muted text-sm mt-1">Mở ảnh gốc trong tab mới để kiểm tra chi tiết trước khi phê duyệt.</p>
                    </div>
                    <div class="inline-flex rounded-full border border-gray-200 bg-white p-1 shadow-sm">
                        <button @click="tab = 'cho_duyet'"
                            :class="tab === 'cho_duyet' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:text-blue-600'"
                            class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-extrabold transition-colors">
                            <i class="fa-solid fa-hourglass-half"></i>
                            Chờ duyệt
                            <span class="rounded-full bg-white/20 px-2 text-xs">{{ count($ds_kyc) }}</span>
                        </button>
                        <button @click="tab = 'da_duyet'"
                            :class="tab === 'da_duyet' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:text-blue-600'"
                            class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-extrabold transition-colors">
                            <i class="fa-solid fa-shield-check"></i>
                            Đã duyệt
                            <span class="rounded-full bg-white/20 px-2 text-xs">{{ count($ds_da_duyet) }}</span>
                        </button>
                    </div>
                </div>

                <div class="p-5 lg:p-6">
                    <div x-show="tab === 'cho_duyet'" class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                        @forelse($ds_kyc as $user)
                            @php($kycData = $user->thong_tin_cccd)
                            <article class="ops-card overflow-hidden">
                                <div class="ops-card-header p-4 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $user->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($user->ho_ten).'&background=EAF2FF&color=1769E0' }}" class="w-11 h-11 rounded-full border border-gray-200 object-cover" alt="">
                                        <div class="min-w-0">
                                            <h3 class="font-black text-gray-900 truncate">{{ $user->ho_ten }}</h3>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="ops-badge py-1">{{ strtoupper($user->vai_tro) }}</span>
                                                <span class="text-xs font-bold text-gray-500">{{ \Carbon\Carbon::parse($kycData['ngay_gui'] ?? $user->updated_at)->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="ops-badge ops-badge-amber">Chờ duyệt</span>
                                </div>

                                <div class="p-4 space-y-4">
                                    <div class="grid grid-cols-2 gap-3">
                                        <a href="{{ $kycData['mat_truoc'] ?? '#' }}" target="_blank" class="group block">
                                            <p class="text-xs font-extrabold text-gray-500 mb-1">Mặt trước CCCD</p>
                                            <div class="aspect-[3/2] overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                                <img src="{{ $kycData['mat_truoc'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform" alt="Mặt trước">
                                            </div>
                                        </a>
                                        <a href="{{ $kycData['mat_sau'] ?? '#' }}" target="_blank" class="group block">
                                            <p class="text-xs font-extrabold text-gray-500 mb-1">Mặt sau CCCD</p>
                                            <div class="aspect-[3/2] overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                                <img src="{{ $kycData['mat_sau'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform" alt="Mặt sau">
                                            </div>
                                        </a>
                                    </div>

                                    @if(isset($kycData['so_do']))
                                        <a href="{{ $kycData['so_do'] }}" target="_blank" class="group block">
                                            <p class="text-xs font-extrabold text-gray-500 mb-1"><i class="fa-solid fa-file-contract text-blue-500 mr-1"></i> Sổ đỏ / giấy tờ nhà</p>
                                            <div class="h-28 overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                                <img src="{{ $kycData['so_do'] }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform" alt="Sổ đỏ">
                                            </div>
                                        </a>
                                    @endif
                                </div>

                                <div class="border-t border-gray-100 bg-gray-50 p-4 flex gap-3">
                                    <form action="{{ route('admin.kyc.approve', $user->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full rounded-full bg-emerald-600 px-4 py-2.5 text-sm font-black text-white hover:bg-emerald-700">
                                            <i class="fa-solid fa-check mr-1"></i> Duyệt
                                        </button>
                                    </form>
                                    <button type="button" onclick="openRejectModal({{ $user->id }}, '{{ addslashes($user->ho_ten) }}')" class="flex-1 rounded-full border border-red-200 bg-white px-4 py-2.5 text-sm font-black text-red-600 hover:bg-red-50">
                                        <i class="fa-solid fa-xmark mr-1"></i> Từ chối
                                    </button>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-white py-16 text-center">
                                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-clipboard-check text-2xl text-emerald-500"></i>
                                </div>
                                <h3 class="text-lg font-black text-gray-900">Không có hồ sơ nào chờ duyệt</h3>
                                <p class="text-gray-500 mt-1">Tất cả yêu cầu định danh đã được xử lý.</p>
                            </div>
                        @endforelse
                    </div>

                    <div x-show="tab === 'da_duyet'" x-cloak style="display: none;" class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                        @forelse($ds_da_duyet as $user)
                            @php($kycData = $user->thong_tin_cccd)
                            <article class="ops-card overflow-hidden">
                                <div class="ops-card-header p-4 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $user->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($user->ho_ten).'&background=ECFDF3&color=067647' }}" class="w-11 h-11 rounded-full border border-gray-200 object-cover" alt="">
                                        <div class="min-w-0">
                                            <h3 class="font-black text-gray-900 truncate">{{ $user->ho_ten }}</h3>
                                            <p class="text-xs font-bold text-gray-500 mt-1">Đã duyệt {{ \Carbon\Carbon::parse($user->updated_at)->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <span class="ops-badge ops-badge-green"><i class="fa-solid fa-shield-check"></i> Đã xác thực</span>
                                </div>
                                <div class="p-4 grid grid-cols-2 gap-3">
                                    <a href="{{ $kycData['mat_truoc'] ?? '#' }}" target="_blank" class="group block">
                                        <p class="text-xs font-extrabold text-gray-500 mb-1">Mặt trước CCCD</p>
                                        <div class="aspect-[3/2] overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                            <img src="{{ $kycData['mat_truoc'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform" alt="Mặt trước">
                                        </div>
                                    </a>
                                    <a href="{{ $kycData['mat_sau'] ?? '#' }}" target="_blank" class="group block">
                                        <p class="text-xs font-extrabold text-gray-500 mb-1">Mặt sau CCCD</p>
                                        <div class="aspect-[3/2] overflow-hidden rounded-xl border border-gray-200 bg-gray-100">
                                            <img src="{{ $kycData['mat_sau'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform" alt="Mặt sau">
                                        </div>
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-white py-16 text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-shield-check text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-black text-gray-900">Chưa có hồ sơ nào được duyệt</h3>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
    <div class="ops-card max-w-md w-full overflow-hidden transform scale-95 transition-transform" id="rejectModalContent">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-black text-gray-900"><i class="fa-solid fa-triangle-exclamation text-red-500 mr-2"></i> Từ chối hồ sơ</h3>
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
                <button type="button" onclick="closeRejectModal()" class="ops-action-secondary">Hủy</button>
                <button type="submit" class="ops-action-danger">Xác nhận từ chối</button>
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
            document.getElementById('ly_do').value = '';
        }, 200);
    }
</script>
@endsection
