@extends('layouts.app')

@section('title', 'Quản lý lịch hẹn - EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <i class="fa-regular fa-calendar-check text-blue-600"></i> Quản lý lịch hẹn xem phòng
                </h1>
                <p class="text-gray-500 mt-2">Xem và duyệt các yêu cầu đặt lịch từ người tìm trọ.</p>
            </div>
        </div>

        <!-- List/Grid of Bookings -->
        @if($danhSachLichHen->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <ul class="divide-y divide-gray-100">
                    @foreach($danhSachLichHen as $lichHen)
                        <li class="p-6 hover:bg-slate-50 transition-colors flex flex-col md:flex-row gap-6 items-start md:items-center" id="booking-item-{{ $lichHen->id }}">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900">
                                        <i class="fa-solid fa-user-circle text-gray-400 mr-1"></i> {{ $lichHen->nguoiThue->ho_ten ?? 'Người dùng ẩn danh' }}
                                    </h3>
                                    @if($lichHen->trang_thai_cuoc_hen === 'cho_duyet')
                                        <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider border border-yellow-200">Đang chờ xử lý</span>
                                    @elseif($lichHen->trang_thai_cuoc_hen === 'da_duyet')
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider border border-emerald-200">Đã duyệt</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider border border-red-200">Đã từ chối</span>
                                    @endif
                                </div>
                                <div class="space-y-1 text-sm text-gray-600">
                                    <p><i class="fa-solid fa-house text-gray-400 w-5"></i> Phòng: <a href="{{ route('room.show', $lichHen->phongTro->id) }}" class="font-bold text-blue-600 hover:underline">{{ $lichHen->phongTro->tieu_de }}</a></p>
                                    <p><i class="fa-regular fa-clock text-gray-400 w-5"></i> Thời gian hẹn: <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($lichHen->thoi_gian_hen)->format('H:i d/m/Y') }}</span></p>
                                    <p><i class="fa-solid fa-phone text-gray-400 w-5"></i> SĐT liên hệ: <span class="font-medium">{{ $lichHen->nguoiThue->so_dien_thoai ?? 'Không có' }}</span></p>
                                </div>
                            </div>
                            
                            @if($lichHen->trang_thai_cuoc_hen === 'cho_duyet')
                                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto" id="actions-{{ $lichHen->id }}">
                                    <button onclick="capNhatLichHen({{ $lichHen->id }}, 'da_duyet')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg transition-colors flex justify-center items-center gap-2 shadow-sm">
                                        <i class="fa-solid fa-check"></i> Duyệt lịch
                                    </button>
                                    <button onclick="capNhatLichHen({{ $lichHen->id }}, 'tu_choi')" class="bg-white hover:bg-red-50 text-red-600 font-bold py-2 px-4 rounded-lg border border-red-200 transition-colors flex justify-center items-center gap-2">
                                        <i class="fa-solid fa-xmark"></i> Từ chối
                                    </button>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $danhSachLichHen->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-calendar-xmark text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Chưa có lịch hẹn nào</h3>
                <p class="text-gray-500 mb-6">Bạn chưa có yêu cầu xem phòng nào từ người tìm trọ.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function capNhatLichHen(id, trang_thai) {
        if (trang_thai === 'tu_choi' && !confirm('Bạn chắc chắn muốn từ chối lịch hẹn này?')) {
            return;
        }

        const actionsDiv = document.getElementById('actions-' + id);
        const originalHtml = actionsDiv.innerHTML;
        actionsDiv.innerHTML = '<span class="text-gray-500"><i class="fa-solid fa-spinner fa-spin mr-2"></i>Đang xử lý...</span>';

        try {
            const response = await fetch(`/api/lich-hen/${id}/cap-nhat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ trang_thai: trang_thai })
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message);
                window.location.reload(); // Reload trang để cập nhật Badge trạng thái
            } else {
                alert(data.message);
                actionsDiv.innerHTML = originalHtml;
            }
        } catch (error) {
            console.error(error);
            alert('Lỗi mạng. Vui lòng thử lại.');
            actionsDiv.innerHTML = originalHtml;
        }
    }
</script>
@endpush
