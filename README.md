# EasyM

EasyM là website hỗ trợ tìm phòng trọ và tìm bạn ở ghép cho sinh viên, tập trung vào khu vực Hà Nội. Hệ thống kết hợp tìm kiếm phòng trên bản đồ, khảo sát lối sống, gợi ý bạn ở ghép và quy trình xác thực phòng trọ để giúp người dùng ra quyết định nhanh hơn.

## Mục Tiêu

- Giúp người tìm trọ lọc phòng theo nhu cầu thực tế.
- Đề xuất bạn ở ghép dựa trên ngân sách, khu vực, tiện ích và thói quen sinh hoạt.
- Hỗ trợ chủ trọ đăng và quản lý phòng.
- Cho phép cộng tác viên xác thực thực địa phòng trọ.
- Cung cấp khu quản trị để quản lý người dùng, phòng, KYC, báo cáo và trọng số thuật toán.

## Tính Năng Chính

- Đăng ký, đăng nhập, đăng nhập Google và đặt lại mật khẩu.
- Hồ sơ cá nhân và xác thực danh tính KYC.
- Tìm kiếm phòng trọ, xem chi tiết phòng và gửi báo cáo vi phạm.
- Khảo sát lối sống cho người tìm trọ.
- Gợi ý bạn ở ghép theo điểm tương thích.
- Gửi, chấp nhận, từ chối và hủy lời mời kết nối ở ghép.
- Chủ trọ đăng phòng, sửa phòng, quản lý lịch hẹn và yêu cầu xác thực.
- Cộng tác viên nộp báo cáo xác thực thực địa.
- Admin quản lý tiêu chí khảo sát, trọng số thuật toán, KYC, phòng trọ, người dùng, cộng tác viên và báo cáo.
- Hệ thống thông báo cho các luồng đặt lịch, lời mời và xử lý hồ sơ.

## Vai Trò Người Dùng

- Người tìm trọ: tìm phòng, làm khảo sát, tìm bạn ở ghép, đặt lịch và báo cáo phòng.
- Chủ trọ: đăng phòng, cập nhật thông tin phòng, quản lý lịch hẹn và yêu cầu xác thực.
- Cộng tác viên: kiểm tra phòng thực tế và gửi báo cáo xác thực.
- Admin: quản trị toàn bộ dữ liệu, tài khoản, tiêu chí, báo cáo và kiểm duyệt.

## Công Nghệ

- Laravel 12
- PHP 8.2+
- Blade
- Tailwind CSS
- Vite
- Laravel Socialite
- PostgreSQL/JSONB theo cấu hình môi trường dự án

## Cài Đặt

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

Chạy server phát triển:

```bash
php artisan serve
npm run dev
```

Hoặc dùng script tổng hợp:

```bash
composer run dev
```

## Kiểm Thử

```bash
php artisan test
```

Lưu ý: môi trường cần bật đúng PDO driver tương ứng với database trong `.env`.
