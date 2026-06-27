@foreach($ds_goi_y ?? [] as $phong)
@php
    $lat = $phong->lat ?? (20.941 + (rand(-50, 50)/10000));
    $lng = $phong->lng ?? (105.558 + (rand(-50, 50)/10000));
    $anhDauTien = is_array($phong->anh_phong ?? null) && count($phong->anh_phong) > 0 ? $phong->anh_phong[0] : null;
@endphp

<div class="room-card group cursor-pointer overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-200 hover:border-blue-100 hover:shadow-xl active:scale-[0.99]"
     data-id="{{ $phong->id }}"
     data-lat="{{ $lat }}"
     data-lng="{{ $lng }}">
    <div class="flex gap-4 p-4">
        <div class="relative h-28 w-32 shrink-0 overflow-hidden rounded-2xl bg-slate-100">
            @if($anhDauTien)
                <img src="{{ $anhDauTien }}" alt="Ảnh phòng {{ $phong->tieu_de }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
            @else
                <img src="https://picsum.photos/seed/easym-room-{{ $phong->id }}/360/280" alt="Ảnh minh họa phòng trọ" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
            @endif

            @if($phong->muc_do_xac_thuc == 2)
                <span class="absolute left-2 top-2 inline-flex items-center gap-1 rounded-full border border-white/70 bg-white/95 px-2 py-1 text-[10px] font-black text-blue-700 shadow-sm">
                    <i class="fa-solid fa-shield-check"></i>
                    Xác thực
                </span>
            @endif
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-3">
                <h3 class="line-clamp-2 pr-1 text-base font-black leading-snug text-slate-950 transition-colors group-hover:text-blue-600">
                    {{ $phong->tieu_de }}
                </h3>
                <span class="shrink-0 rounded-full border border-blue-100 bg-blue-50 px-2.5 py-1 text-[11px] font-black text-blue-700">
                    {{ $phong->dien_tich ?? 'N/A' }} m²
                </span>
            </div>

            <div class="mt-2 text-xl font-black text-blue-600">
                {{ number_format($phong->gia_phong, 0, ',', '.') }} đ
                <span class="text-xs font-bold text-slate-400">/ tháng</span>
            </div>

            <div class="mt-3 flex items-start gap-2 text-xs font-medium leading-5 text-slate-500">
                <i class="fa-solid fa-location-dot mt-1 shrink-0 text-slate-400"></i>
                <span class="line-clamp-2">{{ $phong->dia_chi_chi_tiet }}</span>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-4 py-3">
        <span class="text-xs font-bold text-slate-500">Nhấn để xem trên bản đồ</span>
        <span class="inline-flex items-center gap-2 text-xs font-black text-blue-600">
            Chi tiết
            <i class="fa-solid fa-arrow-right"></i>
        </span>
    </div>
</div>
@endforeach

@if(empty($ds_goi_y) || count($ds_goi_y) === 0)
<div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
    <div class="mx-auto mb-4 grid h-20 w-20 place-items-center rounded-full bg-blue-50 text-blue-500">
        <i class="fa-solid fa-map-location-dot text-3xl"></i>
    </div>
    <h3 class="text-base font-black text-slate-950">Không tìm thấy phòng phù hợp</h3>
    <p class="mx-auto mt-2 max-w-xs text-sm leading-6 text-slate-500">Hãy nới rộng bán kính, đổi khoảng giá hoặc bỏ bộ lọc xác thực để xem thêm lựa chọn.</p>
</div>
@endif
