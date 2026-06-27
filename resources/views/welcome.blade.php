@extends('layouts.app')

@section('title', 'EasyM - Tìm bạn ở ghép & Phòng trọ thông minh')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-zinc-50 pt-16 md:pt-24 pb-20 md:pb-32">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Asymmetric Split Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            <!-- Left Column: Copy & Actions -->
            <div class="lg:col-span-6 flex flex-col items-start z-10 max-w-xl">
                <!-- Hero Stack (Max 4 text/CTA elements) -->
                <div class="flex items-center gap-2 mb-6 bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Hỗ trợ sinh viên Hà Nội</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-zinc-900 tracking-tighter leading-none mb-6">
                    Tìm phòng ưng ý.<br>Ghép đôi hợp lối sống.
                </h1>

                <p class="text-base sm:text-lg text-zinc-600 leading-relaxed max-w-[65ch] mb-8">
                    Hệ thống kết nối phòng trọ và bạn ở ghép thông minh nhờ khảo sát lối sống chi tiết cùng bản đồ GIS trực quan.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                    <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700 active:scale-[0.98] transition-all shadow-md shadow-blue-100 text-center">
                        Bắt đầu ngay
                    </a>
                    <a href="{{ route('search.results') }}" class="inline-flex justify-center items-center px-6 py-3 border border-zinc-200 text-base font-bold rounded-xl text-zinc-700 bg-white hover:bg-zinc-50 active:scale-[0.98] transition-all shadow-sm text-center">
                        Tìm kiếm phòng
                    </a>
                </div>
            </div>

            <!-- Right Column: Visual Asset -->
            <div class="lg:col-span-6 relative">
                <div class="aspect-[4/3] rounded-2xl overflow-hidden shadow-2xl border border-zinc-200">
                    <img src="https://picsum.photos/seed/easym-hero-room/1200/900" alt="Phòng trọ sinh viên hiện đại và ấm cúng" class="w-full h-full object-cover">
                </div>
                <!-- Subtle visual anchor (no neon/AI purple glow, just soft clean shadow) -->
                <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-lg border border-zinc-100 hidden sm:flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-zinc-400 font-medium font-mono">XÁC THỰC THỰC ĐỊA</p>
                        <p class="text-sm text-zinc-800 font-bold">100% phòng được kiểm định</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Logo Wall Section (Trusted by) -->
<section class="py-12 bg-white border-y border-zinc-100">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-xs font-bold text-zinc-400 uppercase tracking-wider font-mono">KẾT NỐI SINH VIÊN TỪ CÁC TRƯỜNG ĐẠI HỌC LỚN</h2>
        </div>
        <div class="flex flex-wrap justify-center items-center gap-12 md:gap-20 opacity-60 grayscale hover:opacity-100 transition-opacity">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded bg-zinc-900 text-white flex items-center justify-center font-bold text-sm">H</div>
                <span class="text-zinc-800 font-black text-lg tracking-tight">ĐHQGHN</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded bg-zinc-900 text-white flex items-center justify-center font-bold text-sm">B</div>
                <span class="text-zinc-800 font-black text-lg tracking-tight">BÁCH KHOA</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded bg-zinc-900 text-white flex items-center justify-center font-bold text-sm">N</div>
                <span class="text-zinc-800 font-black text-lg tracking-tight">KTQD</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded bg-zinc-900 text-white flex items-center justify-center font-bold text-sm">F</div>
                <span class="text-zinc-800 font-black text-lg tracking-tight">NGOẠI THƯƠNG</span>
            </div>
        </div>
    </div>
</section>

<!-- Features Section (Bento Grid) -->
<section class="py-24 bg-zinc-50">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest font-mono">TÍNH NĂNG NỔI BẬT</span>
            <h2 class="text-3xl sm:text-4xl font-black text-zinc-900 tracking-tight mt-3 mb-4">
                Giải pháp toàn diện cho việc tìm trọ và tìm bạn ở ghép
            </h2>
            <p class="text-zinc-600 text-base max-w-[65ch] mx-auto">
                Tích hợp các công cụ thông minh giúp bạn tìm được không gian sống thoải mái và những người đồng hành hợp cạ nhất.
            </p>
        </div>

        <!-- Bento Grid: Asymmetric Layout with rhythm -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <!-- Cell 1: Large (2/3 width) - Lifestyle Compatibility -->
            <div class="md:col-span-8 bg-white border border-zinc-100 p-8 rounded-2xl shadow-sm flex flex-col justify-between group overflow-hidden relative">
                <div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-blue-50 text-blue-600 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                    <h3 class="text-xl font-bold text-zinc-900 mb-2">Khảo sát lối sống thông minh</h3>
                    <p class="text-zinc-500 text-sm max-w-[50ch] mb-6">
                        Hệ thống đối sánh 20+ chỉ số thói quen sinh hoạt như giờ giấc đi ngủ, sở thích thú cưng, tần suất dọn dẹp và mức độ hướng ngoại để đề xuất người ở ghép phù hợp nhất.
                    </p>
                </div>
                <div class="mt-4 bg-zinc-50 rounded-xl p-4 border border-zinc-100">
                    <div class="flex items-center justify-between mb-3 pb-3 border-b border-zinc-150">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center font-bold text-xs text-blue-700">M</div>
                            <div>
                                <p class="text-xs font-bold text-zinc-800">Hoàng Minh</p>
                                <p class="text-[10px] text-zinc-400">SV Đại học Bách Khoa</p>
                            </div>
                        </div>
                        <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded">96% Tương thích</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[11px] text-zinc-500 font-medium">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span>Không hút thuốc</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span>Ngủ trước 12h đêm</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span>Yêu thích sạch sẽ</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                            <span>Thân thiện, hòa đồng</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cell 2: Small (1/3 width) - Field Verification -->
            <div class="md:col-span-4 bg-white border border-zinc-100 p-8 rounded-2xl shadow-sm flex flex-col justify-between overflow-hidden relative">
                <div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </span>
                    <h3 class="text-xl font-bold text-zinc-900 mb-2">Đăng tin & Xác thực thực địa</h3>
                    <p class="text-zinc-500 text-sm mb-6">
                        Các bài đăng phòng trọ của Chủ trọ được đội ngũ Cộng tác viên kiểm định trực tiếp và cung cấp hình ảnh chân thực trước khi hiển thị.
                    </p>
                </div>
                <div class="mt-4 bg-emerald-50 rounded-xl p-4 border border-emerald-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-emerald-800">Cộng tác viên đã duyệt</span>
                    </div>
                    <span class="text-[10px] font-mono text-emerald-600 font-bold bg-white px-1.5 py-0.5 rounded shadow-sm border border-emerald-100">ĐÃ XÁC THỰC</span>
                </div>
            </div>

            <!-- Cell 3: Small (1/3 width) - GIS Heatmap -->
            <div class="md:col-span-4 bg-white border border-zinc-100 p-8 rounded-2xl shadow-sm flex flex-col justify-between overflow-hidden relative">
                <div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-orange-50 text-orange-600 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </span>
                    <h3 class="text-xl font-bold text-zinc-900 mb-2">Bản đồ GIS trực quan</h3>
                    <p class="text-zinc-500 text-sm mb-6">
                        Hiển thị trực quan vị trí phòng trọ, khoảng cách tới trường học, điểm đón xe buýt để bạn tối ưu hóa thời gian di chuyển.
                    </p>
                </div>
                <div class="mt-4 h-24 bg-zinc-100 rounded-xl border border-zinc-200 overflow-hidden relative">
                    <img src="https://picsum.photos/seed/easym-map/400/300" alt="Bản đồ GIS mô phỏng" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-zinc-900/10 backdrop-blur-[1px] flex items-center justify-center">
                        <span class="text-[10px] font-mono font-bold text-zinc-800 bg-white/90 px-2 py-1 rounded shadow-sm border border-zinc-200">BẢN ĐỒ VỊ TRÍ CHUẨN</span>
                    </div>
                </div>
            </div>

            <!-- Cell 4: Large (2/3 width) - Suggestion Engine -->
            <div class="md:col-span-8 bg-white border border-zinc-100 p-8 rounded-2xl shadow-sm flex flex-col justify-between overflow-hidden relative">
                <div>
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-purple-50 text-purple-600 mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </span>
                    <h3 class="text-xl font-bold text-zinc-900 mb-2">Gợi ý phòng trọ khớp nhu cầu</h3>
                    <p class="text-zinc-500 text-sm max-w-[50ch] mb-6">
                        Dựa trên cấu hình khoảng cách và khoảng giá bạn mong muốn, thuật toán thông minh sẽ lọc và sắp xếp danh sách phòng tối ưu nhất ngay tức thì.
                    </p>
                </div>
                <div class="mt-4 bg-zinc-50 rounded-xl p-4 border border-zinc-100">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img src="https://picsum.photos/seed/easym-verify/200/150" alt="Ảnh phòng demo" class="w-16 h-12 rounded-lg object-cover border border-zinc-200">
                            <div>
                                <p class="text-xs font-bold text-zinc-800">Phòng trọ ban công thoáng mát</p>
                                <p class="text-[10px] text-zinc-500">Cầu Giấy, Hà Nội - Cách ĐHQGHN 800m</p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs font-black text-blue-600">3.200.000 đ/tháng</p>
                            <p class="text-[10px] text-zinc-400">Độ khớp nhu cầu: 94%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-24 bg-white border-t border-zinc-100">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest font-mono">Ý KIẾN NGƯỜI DÙNG</span>
            <h2 class="text-3xl sm:text-4xl font-black text-zinc-900 tracking-tight mt-3 mb-4">
                Sinh viên nói gì về EasyM
            </h2>
            <p class="text-zinc-600 text-base max-w-[65ch] mx-auto">
                Những chia sẻ thực tế từ các bạn sinh viên đã tìm được phòng trọ ưng ý và người bạn cùng phòng lý tưởng thông qua nền tảng.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Review 1 -->
            <div class="bg-zinc-50 p-8 rounded-2xl border border-zinc-100 flex flex-col justify-between">
                <div>
                    <div class="flex text-blue-600 gap-1 mb-4">
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                    </div>
                    <!-- Max 3 lines of body -->
                    <p class="text-zinc-700 text-sm leading-relaxed mb-6 italic">
                        "Nhờ khảo sát lối sống của EasyM mà mình tìm được một người bạn cùng phòng cực kỳ hợp ý, cả hai đều thích sạch sẽ và ngủ sớm."
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-xs text-blue-700">T</div>
                    <div>
                        <p class="text-xs font-bold text-zinc-800">Thảo Vy</p>
                        <p class="text-[10px] text-zinc-400">Sinh viên Đại học Ngoại Thương</p>
                    </div>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="bg-zinc-50 p-8 rounded-2xl border border-zinc-100 flex flex-col justify-between">
                <div>
                    <div class="flex text-blue-600 gap-1 mb-4">
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                    </div>
                    <!-- Max 3 lines of body -->
                    <p class="text-zinc-700 text-sm leading-relaxed mb-6 italic">
                        "Tính năng bản đồ GIS giúp mình tính được chính xác khoảng cách đến giảng đường. Phòng trọ còn được CTV xác thực nên rất an tâm."
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-xs text-blue-700">D</div>
                    <div>
                        <p class="text-xs font-bold text-zinc-800">Minh Đức</p>
                        <p class="text-[10px] text-zinc-400">Sinh viên Đại học Bách Khoa</p>
                    </div>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="bg-zinc-50 p-8 rounded-2xl border border-zinc-100 flex flex-col justify-between">
                <div>
                    <div class="flex text-blue-600 gap-1 mb-4">
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                        <i class="fa-solid fa-star text-sm"></i>
                    </div>
                    <!-- Max 3 lines of body -->
                    <p class="text-zinc-700 text-sm leading-relaxed mb-6 italic">
                        "Là chủ nhà, mình rất thích quy trình duyệt hồ sơ của EasyM. Giao diện quản lý phòng và đặt lịch hẹn cực kỳ dễ thao tác."
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center font-bold text-xs text-blue-700">N</div>
                    <div>
                        <p class="text-xs font-bold text-zinc-800">Bác Nam</p>
                        <p class="text-[10px] text-zinc-400">Chủ trọ khu vực Cầu Giấy</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-24 bg-zinc-900 text-white relative overflow-hidden">
    <!-- Clean, subtle background lines instead of AI gradients -->
    <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tighter mb-6">
            Bắt đầu hành trình tìm trọ dễ dàng ngay hôm nay
        </h2>
        <p class="text-zinc-400 text-base sm:text-lg max-w-[60ch] mx-auto mb-10 leading-relaxed">
            Đăng ký tài khoản miễn phí để tham gia làm khảo sát lối sống hoặc đăng thông tin phòng trọ của bạn lên hệ thống EasyM.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-8 py-3.5 border border-transparent text-base font-bold rounded-xl text-zinc-900 bg-white hover:bg-zinc-50 active:scale-[0.98] transition-all shadow-lg text-center w-full sm:w-auto">
                Tạo tài khoản miễn phí
            </a>
            <a href="{{ route('search.results') }}" class="inline-flex justify-center items-center px-8 py-3.5 border border-zinc-700 text-base font-bold rounded-xl text-white bg-transparent hover:bg-zinc-800 active:scale-[0.98] transition-all text-center w-full sm:w-auto">
                Xem bản đồ gợi ý
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-zinc-950 text-zinc-500 py-12 border-t border-zinc-900 text-sm">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="text-xl font-black tracking-tighter text-white">EasyM.</span>
                <span class="text-xs text-zinc-600 font-medium font-mono">HỆ THỐNG GHÉP ĐÔI THÔNG MINH</span>
            </div>
            <div class="flex gap-6 font-medium">
                <a href="{{ route('search.results') }}" class="hover:text-white transition-colors">Tìm phòng</a>
                <a href="{{ route('tim-ban.index') }}" class="hover:text-white transition-colors">Tìm bạn ở ghép</a>
                <a href="{{ route('survey.show') }}" class="hover:text-white transition-colors">Khảo sát</a>
            </div>
            <p class="text-xs text-zinc-600">
                &copy; {{ date('Y') }} EasyM. Được xây dựng dành cho sinh viên.
            </p>
        </div>
    </div>
</footer>
@endsection
