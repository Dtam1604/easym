@extends('layouts.app')

@section('title', 'Cộng tác viên - Xác minh Thực địa - EasyM')

@section('content')
<div class="ops-page py-8 sm:py-10">
    <div class="ops-shell space-y-6">

        <div class="ops-card overflow-hidden">
            <div class="p-5 sm:p-6 lg:p-7 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <a href="{{ route('home') }}" class="ops-badge ops-action-secondary min-h-0 py-1.5">
                        <i class="fa-solid fa-arrow-left"></i>
                        Trang chủ
                    </a>
                    <div>
                        <p class="ops-kicker">CTV thực địa</p>
                        <h1 class="ops-title text-2xl sm:text-3xl mt-1">Danh sách phòng chờ khảo sát</h1>
                        <p class="ops-muted mt-2 max-w-3xl">Kiểm tra thực tế các phòng đã qua duyệt online, đối chiếu ảnh và gửi báo cáo để admin phê duyệt.</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:flex gap-3">
                    <div class="ops-badge ops-badge-blue">
                        <i class="fa-solid fa-user-check"></i>
                        {{ auth()->user()->ho_ten }}
                    </div>
                    <div class="ops-badge ops-badge-amber">
                        <i class="fa-solid fa-clock"></i>
                        {{ count($ds_phong_cho) }} chờ xử lý
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="ops-card border-emerald-200 bg-emerald-50 p-4 text-emerald-800 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="ops-card border-red-200 bg-red-50 p-4 text-red-800 flex items-center gap-3">
            <i class="fa-solid fa-circle-xmark text-red-500"></i>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
        @endif

        <div class="ops-card overflow-hidden">
            <div class="ops-card-header p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-gray-900">Phòng cần đi thực địa</h2>
                    <p class="ops-muted text-sm mt-1">Ưu tiên phòng có địa chỉ rõ ràng và chủ trọ đã cung cấp đủ thông tin.</p>
                </div>
                <span class="ops-badge">
                    <i class="fa-solid fa-list-check"></i>
                    {{ count($ds_phong_cho) }} mục
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="ops-table">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left">Mã phòng</th>
                            <th scope="col" class="px-6 py-4 text-left">Tin đăng</th>
                            <th scope="col" class="px-6 py-4 text-left">Chủ trọ</th>
                            <th scope="col" class="px-6 py-4 text-left">Trạng thái</th>
                            <th scope="col" class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($ds_phong_cho as $phong)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">#{{ $phong->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 mb-1 line-clamp-1" title="{{ $phong->tieu_de }}">{{ $phong->tieu_de }}</div>
                                <div class="text-xs text-gray-500 flex items-start gap-1.5 max-w-xl">
                                    <i class="fa-solid fa-location-dot mt-0.5 text-gray-400"></i>
                                    <span class="line-clamp-2">{{ $phong->dia_chi_chi_tiet }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full object-cover border border-gray-200" src="https://ui-avatars.com/api/?name={{ urlencode($phong->chuTro->ho_ten ?? 'Admin') }}&background=EAF2FF&color=1769E0" alt="">
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-bold text-gray-900">{{ $phong->chuTro->ho_ten ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $phong->chuTro->so_dien_thoai ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="ops-badge ops-badge-amber">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Chờ xác thực
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="inline-flex gap-2">
                                <a target="_blank" href="{{ route('room.show', $phong->id) }}" class="ops-action-secondary min-h-0 py-2">
                                    <i class="fa-solid fa-eye"></i> Xem
                                </a>
                                <a href="{{ route('ctv.baocao', $phong->id) }}" class="ops-action-primary min-h-0 py-2">
                                    <i class="fa-solid fa-clipboard-check"></i> Báo cáo
                                </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-clipboard-check text-2xl text-emerald-500"></i>
                                </div>
                                <p class="font-black text-lg text-gray-900">Không có phòng trọ nào đang chờ thực địa.</p>
                                <p class="text-sm mt-1">Danh sách sẽ tự cập nhật khi admin chuyển phòng sang bước khảo sát.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    </div>
</div>
@endsection
