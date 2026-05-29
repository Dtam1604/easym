@extends('layouts.app')

@section('title', 'Tất cả thông báo - EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <i class="fa-solid fa-bell text-blue-600"></i> Thông báo của bạn
                </h1>
                <p class="text-gray-500 mt-2">Theo dõi mọi cập nhật và thông tin quan trọng từ hệ thống.</p>
            </div>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('thong_bao.doc_tat_ca') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-white border border-gray-200 text-gray-700 hover:text-blue-600 hover:border-blue-300 font-bold py-2 px-4 rounded-xl transition-colors shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-check-double"></i> Đánh dấu tất cả đã đọc
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-100 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Notifications List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($thongBaos->count() > 0)
                <ul class="divide-y divide-gray-100">
                    @foreach($thongBaos as $notification)
                        <li class="hover:bg-slate-50 transition-colors {{ $notification->read_at ? 'opacity-70' : 'bg-blue-50/20' }}">
                            <a href="{{ route('thong_bao.doc', $notification->id) }}" class="flex p-6 gap-4 items-start">
                                <div class="w-12 h-12 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0 shadow-sm mt-1">
                                    <i class="fa-solid {{ $notification->data['icon'] ?? 'fa-bell' }} {{ $notification->data['color'] ?? 'text-gray-500' }} text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-1">
                                        <h3 class="text-base text-gray-900 {{ $notification->read_at ? 'font-medium' : 'font-extrabold' }}">
                                            {{ $notification->data['message'] ?? 'Bạn có một thông báo mới' }}
                                        </h3>
                                        <span class="text-xs text-gray-400 font-medium whitespace-nowrap bg-gray-100 px-2 py-1 rounded-md">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    @if(isset($notification->data['type']))
                                        <p class="text-sm text-gray-500 mt-2 flex items-center gap-1.5">
                                            @if($notification->data['type'] === 'lich_hen')
                                                <i class="fa-regular fa-calendar text-blue-400"></i> Xem chi tiết phòng
                                            @elseif($notification->data['type'] === 'loi_moi_o_ghep')
                                                <i class="fa-solid fa-users-viewfinder text-purple-400"></i> Quản lý bạn ở ghép
                                            @endif
                                            <i class="fa-solid fa-arrow-right text-[10px] text-gray-300 ml-1"></i>
                                        </p>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $thongBaos->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="p-16 text-center">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-regular fa-bell-slash text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hòm thư trống rỗng</h3>
                    <p class="text-gray-500">Bạn chưa có bất kỳ thông báo nào từ hệ thống EasyM.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
