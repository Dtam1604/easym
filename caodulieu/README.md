# Module Cào Dữ Liệu Phòng Trọ (Data Ingestion Pipeline)

Đây là hệ thống cào dữ liệu (Web Scraping) độc lập được viết bằng Python dành cho đồ án EasyM. Hệ thống tự động thu thập tin đăng phòng trọ từ các nguồn web tĩnh và động, chuẩn hóa dữ liệu (Regex) và chuyển đổi địa chỉ thành tọa độ không gian (Geocoding) trước khi đẩy vào Cơ sở dữ liệu PostGIS.

## 1. Yêu cầu hệ thống (Prerequisites)
- Máy tính đã cài đặt **Python 3.9** trở lên.
- Cần có kết nối Internet ổn định.

## 2. Cài đặt môi trường
Mở Terminal/Command Prompt, trỏ vào thư mục `caodulieu` và chạy lệnh sau:

```bash
# Cài đặt các thư viện cần thiết
pip install -r requirements.txt

# (Bắt buộc nếu muốn cào Facebook) Cài đặt trình duyệt ẩn danh cho Playwright
playwright install chromium
```

## 3. Cấu hình Cơ sở dữ liệu
Script tự động đọc cấu hình DB từ file `.env` của Laravel ở thư mục gốc (`easym/.env`). Bạn không cần cấu hình gì thêm miễn là web Laravel của bạn đang kết nối DB bình thường.

---

## 4. Hướng dẫn chạy các Script

### Kịch bản 1: Cào Web tĩnh (Ví dụ: Phongtro123)
Trang web tĩnh rất dễ cào, tốc độ siêu nhanh và không cần đăng nhập.
Chạy lệnh:
```bash
python phongtro123_scraper.py
```
> Script sẽ tìm các khối HTML, bóc tách Tiêu đề, Giá, Diện tích, lọc Hình ảnh và đẩy trực tiếp vào PostgreSQL.

### Kịch bản 2: Cào Facebook Hội Nhóm (Playwright Automated Browser)
Facebook chặn cào dữ liệu bằng tường lửa rất mạnh, vì vậy ta phải dùng phương pháp **Giả lập trình duyệt (Browser Automation)** kết hợp với **Session Cookies**.

**Bước 1: Đăng nhập để lấy Cookie (Chỉ làm 1 lần)**
```bash
python facebook_scraper.py login
```
- Một cửa sổ trình duyệt Chromium sẽ hiện lên. 
- Bạn tự tay nhập tài khoản và mật khẩu Facebook của bạn vào.
- Khi vào được trang chủ FB, hãy quay lại cửa sổ Terminal gõ chữ `XONG` rồi Enter.
- Hệ thống sẽ lưu Cookie của bạn vào file `state.json`.

**Bước 2: Chạy cào dữ liệu hội nhóm**
```bash
python facebook_scraper.py scrape
```
- Lúc này trình duyệt sẽ chạy ngầm (Headless), dùng Cookie bạn vừa tạo để vượt rào Facebook, chui vào nhóm "Phòng trọ Hà Nội", cuộn trang và thu thập các bài viết.
- Hệ thống tự động dùng Regex bóc tách Giá (VD: 1.5tr -> 1500000) và Diện tích từ trong chuỗi văn bản dài dòng.

## 5. Lưu ý về Geocoding (Lấy tọa độ)
Hệ thống sử dụng `geopy` (Nominatim của OpenStreetMap) miễn phí để dịch chuỗi Địa chỉ thành Vĩ độ/Kinh độ. 
Do là hàng free nên thỉnh thoảng có thể dịch sai hoặc không tìm ra (khi đó nó sẽ lấy mặc định tọa độ trung tâm Hà Nội).

---
*Developed by Senior Data Engineer for EasyM Project*
