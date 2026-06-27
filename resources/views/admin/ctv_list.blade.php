@extends('layouts.app')

@section('title', 'Quản lý Cộng tác viên (UC19) - EasyM')

@section('content')
<div class="flex min-h-[calc(100dvh-72px)] ops-page font-sans" x-data="ctvManagement()">

    <!-- SIDEBAR -->
    <aside class="w-64 sidebar-dark ops-sidebar hidden md:flex flex-col z-20">
        <div class="h-16 flex items-center justify-center border-b border-slate-800">
            <h1 class="ops-brand text-2xl">
                EasyM <span>ADMIN</span>
            </h1>
        </div>
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-chart-pie w-6"></i>
                        <span class="font-medium text-sm">Tổng quan & Thống kê</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.dashboard') }}#weights-config" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-sliders w-6"></i>
                        <span class="font-medium text-sm">Cấu hình Thuật toán</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tieuchi.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-layer-group w-6"></i>
                        <span class="font-medium text-sm">Thêm Tiêu chí mới</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.kyc.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-id-card-clip w-6"></i>
                        <span class="font-medium text-sm">Duyệt Hồ sơ KYC</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.baocao.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-flag w-6 text-red-400"></i>
                        <span class="font-medium text-sm text-red-100">Báo cáo vi phạm</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.phongtro.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-building w-6"></i>
                        <span class="font-medium text-sm">Quản lý Tất cả phòng trọ</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.nguoidung.index') }}" class="sidebar-item flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-users w-6"></i>
                        <span class="font-medium text-sm">Quản lý Tài khoản</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.ctv.index') }}" class="sidebar-item active flex items-center px-6 py-3 transition-colors">
                        <i class="fa-solid fa-user-gear w-6"></i>
                        <span class="font-medium text-sm">Quản lý CTV (UC19)</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col ops-main">
        <!-- Header -->
        <header class="ops-header flex items-center justify-between px-6 lg:px-8 z-10 sticky top-0">
            <div class="flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="ops-action-secondary min-h-0 w-10 h-10 p-0 mr-4" aria-label="Quay lại dashboard">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <p class="ops-kicker">Nhân sự thực địa</p>
                    <h2 class="text-xl font-black text-gray-900">Quản lý Cộng tác viên</h2>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-600">Xin chào, <span class="font-bold text-blue-600">Admin Tâm</span></span>
                <img src="https://ui-avatars.com/api/?name=Admin&background=1769E0&color=fff" class="w-9 h-9 rounded-full border-2 border-blue-100">
            </div>
        </header>

        <div class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
            @if(session('success'))
                <div class="ops-card bg-emerald-50 border-emerald-200 p-4">
                    <div class="flex items-center">
                        <i class="fa-solid fa-circle-check text-emerald-500 mr-3"></i>
                        <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="ops-card bg-red-50 border-red-200 p-4">
                    <div class="flex items-start">
                        <i class="fa-solid fa-circle-exclamation text-red-500 mr-3 mt-0.5"></i>
                        <div>
                            <p class="text-red-700 font-bold mb-1">Vui lòng kiểm tra lại thông tin:</p>
                            <ul class="list-disc pl-5 text-sm text-red-600 space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- STATS COUNTER -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="ops-card p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tổng số CTV</div>
                        <div class="text-2xl font-black text-gray-800 mt-0.5">{{ $ctvs->total() }}</div>
                    </div>
                </div>
                <div class="ops-card p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-house-circle-check"></i>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tổng tin đã kiểm định</div>
                        <div class="text-2xl font-black text-gray-800 mt-0.5">
                            {{ \App\Models\XacThucThucDia::where('trang_thai', 'da_duyet')->count() }}
                        </div>
                    </div>
                </div>
                <div class="ops-card p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Đang chờ phê duyệt</div>
                        <div class="text-2xl font-black text-gray-800 mt-0.5">
                            {{ \App\Models\XacThucThucDia::where('trang_thai', 'cho_duyet')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLLABORATORS LIST -->
            <div class="ops-card overflow-hidden">
                <div class="ops-card-header p-5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                    <div>
                        <p class="ops-kicker">Danh sách</p>
                        <h3 class="font-black text-gray-900 text-lg flex items-center gap-2 mt-1"><i class="fa-solid fa-user-shield text-blue-500"></i> Cộng tác viên khảo sát</h3>
                    </div>
                    <button @click="isCreateModalOpen = true" class="ops-action-primary">
                        <i class="fa-solid fa-user-plus"></i> Thêm CTV mới
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="ops-table">
                        <thead>
                            <tr>
                                <th class="p-4 text-left">CTV</th>
                                <th class="p-4 text-left">Địa bàn quản lý</th>
                                <th class="p-4 text-center">Đã xác thực</th>
                                <th class="p-4 text-center">Trạng thái</th>
                                <th class="p-4 text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($ctvs as $ctv)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="p-4 align-middle">
                                    <div class="flex gap-3 items-center">
                                        <img src="{{ $ctv->anh_dai_dien ?? 'https://ui-avatars.com/api/?name='.urlencode($ctv->ho_ten).'&background=f3e8ff&color=6b21a8' }}" class="w-10 h-10 rounded-full border border-gray-200 object-cover shadow-sm">
                                        <div>
                                            <div class="font-bold text-gray-900 flex items-center gap-1">
                                                {{ $ctv->ho_ten }}
                                                @if($ctv->da_xac_thuc_cccd)
                                                    <i class="fa-solid fa-circle-check text-blue-500 text-xs" title="Đã xác thực danh tính"></i>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $ctv->email }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5"><i class="fa-solid fa-phone mr-1"></i>{{ $ctv->so_dien_thoai ?? 'Chưa cập nhật' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <span class="ops-badge py-1">
                                            <i class="fa-solid fa-location-crosshairs text-sky-500"></i>
                                            {{ $ctv->dia_ban_quan_ly ?? 'Chưa phân vùng' }}
                                        </span>
                                        <button @click="openRegionModal({{ $ctv->id }}, @js($ctv->ho_ten), @js($ctv->dia_ban_quan_ly))" 
                                                class="text-gray-400 hover:text-blue-600 transition-colors" title="Sửa địa bàn">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="p-4 align-middle text-center font-bold text-gray-800 text-sm">
                                    {{ $ctv->xac_thuc_thuc_dias_count }} tin
                                </td>
                                <td class="p-4 align-middle text-center">
                                    @if($ctv->trang_thai_khoa)
                                        <span class="ops-badge ops-badge-red py-1 text-[10px] uppercase">
                                            <i class="fa-solid fa-user-slash mr-1"></i> Bị khóa
                                        </span>
                                    @else
                                        <span class="ops-badge ops-badge-green py-1 text-[10px] uppercase">
                                            <i class="fa-solid fa-user-check mr-1"></i> Hoạt động
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 align-middle text-center">
                                    <div class="flex justify-center gap-2">
                                        <!-- Khóa / Mở khóa -->
                                        @if($ctv->trang_thai_khoa)
                                            <form action="{{ route('admin.ctv.toggle_lock', $ctv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn mở khóa tài khoản CTV {{ $ctv->ho_ten }}? Họ sẽ có thể tiếp tục truy cập hệ thống.');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-full text-xs font-bold transition-all border border-emerald-200 hover:border-emerald-600 shadow-sm flex items-center gap-1">
                                                    <i class="fa-solid fa-unlock"></i> Mở khóa
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.ctv.toggle_lock', $ctv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn khóa tài khoản CTV {{ $ctv->ho_ten }}? Họ sẽ không thể đăng nhập hệ thống.');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-yellow-50 text-yellow-600 hover:bg-yellow-600 hover:text-white rounded-full text-xs font-bold transition-all border border-yellow-200 hover:border-yellow-600 shadow-sm flex items-center gap-1">
                                                    <i class="fa-solid fa-user-lock"></i> Khóa
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Xóa tài khoản CTV -->
                                        <form action="{{ route('admin.nguoidung.destroy', $ctv->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản CTV {{ $ctv->ho_ten }}? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-full text-xs font-bold transition-all border border-red-200 hover:border-red-600 shadow-sm flex items-center gap-1">
                                                <i class="fa-solid fa-trash-can"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-500">
                                    <div class="text-4xl text-gray-300 mb-3"><i class="fa-solid fa-users-slash"></i></div>
                                    <p class="font-medium text-lg">Chưa có Cộng tác viên nào.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200 bg-white">
                    {{ $ctvs->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- CREATE CTV MODAL (Alpine.js) -->
    <div x-show="isCreateModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isCreateModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="isCreateModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isCreateModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Thêm Cộng tác viên mới</h3>
                            <div class="text-xs text-gray-400">Tạo tài khoản CTV khảo sát thực địa.</div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.ctv.store') }}" method="POST">
                    @csrf
                    <div class="p-6 space-y-4 bg-gray-50/50">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" name="ho_ten" required class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-white" placeholder="Nguyễn Văn A">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Địa chỉ Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-white" placeholder="verifier@easym.vn">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                            <input type="text" name="so_dien_thoai" required class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-white" placeholder="0987654321">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Địa bàn phân công (Khu vực)</label>
                            <input type="text" name="dia_ban_quan_ly" class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-white" placeholder="Ví dụ: ĐH Lâm Nghiệp, Xuân Mai">
                            <span class="text-[10px] text-gray-400 mt-1 block">Có thể phân vùng quận/huyện hoặc khu vực trường Đại học.</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100 gap-2">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Tạo tài khoản
                        </button>
                        <button type="button" @click="isCreateModalOpen = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- REGION UPDATE MODAL (Alpine.js) -->
    <div x-show="isRegionModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isRegionModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="isRegionModalOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isRegionModalOpen" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Phân vùng Địa bàn hoạt động</h3>
                            <div class="text-xs text-gray-400">Điều chỉnh khu vực khảo sát cho: <span class="font-bold text-blue-600" x-text="selectedCtvName"></span></div>
                        </div>
                    </div>
                </div>

                <form :action="updateRegionUrl" method="POST">
                    @csrf
                    <div class="p-6 bg-gray-50/50">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Địa bàn quản lý mới</label>
                            <input type="text" name="dia_ban_quan_ly" x-model="selectedCtvRegion" class="w-full px-3.5 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium bg-white" placeholder="Ví dụ: ĐH Lâm Nghiệp, Xuân Mai">
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100 gap-2">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 focus:outline-none">
                            Lưu thay đổi
                        </button>
                        <button type="button" @click="isRegionModalOpen = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none">
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('ctvManagement', () => ({
            isCreateModalOpen: false,
            isRegionModalOpen: false,
            
            selectedCtvId: null,
            selectedCtvName: '',
            selectedCtvRegion: '',
            updateRegionUrl: '',

            openRegionModal(id, name, region) {
                this.selectedCtvId = id;
                this.selectedCtvName = name;
                this.selectedCtvRegion = region;
                this.updateRegionUrl = `/admin/ctv-list/${id}/update-region`;
                this.isRegionModalOpen = true;
            }
        }));
    });
</script>
@endpush
