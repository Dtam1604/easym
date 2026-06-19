@extends('layouts.app')

@section('title', 'Cộng tác viên - Xác minh Thực địa - EasyM')

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Danh sách chờ Thực địa</h1>
                <p class="text-gray-500 mt-2">Dành cho Cộng tác viên (CTV) của EasyM kiểm tra thực tế các phòng trọ đã duyệt Online.</p>
            </div>
            <div>
                <span class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-100 text-blue-800 font-bold text-sm">
                    <i class="fa-solid fa-clipboard-check mr-2 text-blue-600"></i> CTV: {{ auth()->user()->ho_ten }}
                </span>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center shadow-sm">
            <i class="fa-solid fa-circle-check mr-2 text-emerald-500"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center shadow-sm">
            <i class="fa-solid fa-circle-xmark mr-2 text-red-500"></i> {{ session('error') }}
        </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Mã Phòng</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Tiêu đề / Địa chỉ</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Chủ trọ</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Trạng thái</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-black text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($ds_phong_cho as $phong)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">#{{ $phong->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 mb-1 line-clamp-1" title="{{ $phong->tieu_de }}">{{ $phong->tieu_de }}</div>
                                <div class="text-xs text-gray-500 flex items-center"><i class="fa-solid fa-location-dot mr-1 text-gray-400"></i> {{ $phong->dia_chi_chi_tiet }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($phong->chuTro->ho_ten ?? 'Admin') }}&background=random" alt="">
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-bold text-gray-900">{{ $phong->chuTro->ho_ten ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-500">{{ $phong->chuTro->so_dien_thoai ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                                    Chờ xác thực
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a target="_blank" href="{{ route('room.show', $phong->id) }}" class="text-emerald-600 hover:text-emerald-900 font-bold bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-lg transition-colors inline-block mr-2">
                                    <i class="fa-solid fa-eye mr-1"></i> Xem chi tiết
                                </a>
                                <a href="{{ route('ctv.baocao', $phong->id) }}" class="text-blue-600 hover:text-blue-900 font-bold bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-lg transition-colors inline-block">
                                    <i class="fa-solid fa-clipboard-check mr-1"></i> Báo cáo
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-inbox text-2xl text-gray-400"></i>
                                </div>
                                <p class="font-bold text-lg">Không có phòng trọ nào đang chờ thực địa.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
