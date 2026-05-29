@foreach($ds_goi_y ?? [] as $phong)
@php
    $lat = $phong->lat ?? (20.941 + (rand(-50, 50)/10000));
    $lng = $phong->lng ?? (105.558 + (rand(-50, 50)/10000));
@endphp

<div class="room-card bg-white rounded-2xl p-5 border border-gray-200 shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 cursor-pointer flex gap-5 group" 
     data-id="{{ $phong->id }}" 
     data-lat="{{ $lat }}" 
     data-lng="{{ $lng }}">
     


    <!-- Thông tin phòng trọ -->
    <div class="flex-1 min-w-0">
        <div class="flex justify-between items-start mb-2">
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 leading-tight pr-2">
                {{ $phong->tieu_de }}
            </h3>
            
            @if($phong->muc_do_xac_thuc == 2)
            <div class="flex-shrink-0" title="Đã xác thực thực địa">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Xác thực
                </span>
            </div>
            @endif
        </div>
        
        <div class="text-xl font-extrabold text-blue-600 mb-3">
            {{ number_format($phong->gia_phong, 0, ',', '.') }} đ<span class="text-xs font-normal text-gray-500">/tháng</span>
        </div>
        
        <div class="flex items-center text-sm text-gray-600 gap-4 mb-2">
            <span class="flex items-center gap-1.5 bg-gray-100 px-2 py-1 rounded-md font-medium">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                {{ $phong->dien_tich ?? 'N/A' }} m²
            </span>
            <span class="flex items-center gap-1.5 text-gray-500 truncate">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="truncate">{{ $phong->dia_chi_chi_tiet }}</span>
            </span>
        </div>
    </div>
</div>
@endforeach

@if(empty($ds_goi_y) || count($ds_goi_y) === 0)
<div class="text-center py-10">
    <img src="https://illustrations.popsy.co/amber/location-tracking.svg" alt="No results" class="w-48 mx-auto opacity-70 mb-4">
    <h3 class="text-lg font-bold text-gray-800">Không tìm thấy phòng phù hợp</h3>
    <p class="text-gray-500 mt-2">Vui lòng điều chỉnh lại bộ lọc hoặc thay đổi bán kính.</p>
</div>
@endif
