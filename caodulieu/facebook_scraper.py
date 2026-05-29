"""
Facebook Group Scraper sử dụng Playwright

LƯU Ý QUAN TRỌNG:
Vì Facebook kiểm soát Scraping rất gắt gao (Block IP, Checkpoint), chúng ta phải mô phỏng trình duyệt như một người dùng thật.
Kịch bản chạy:
1. Chạy script lần đầu với mode 'login'. Trình duyệt sẽ mở lên, bạn TỰ TAY nhập tài khoản mật khẩu Facebook.
2. Đăng nhập xong, tắt trình duyệt. Script sẽ lưu lại Session (Cookie) vào file `state.json`.
3. Các lần chạy sau với mode 'scrape', script sẽ dùng lại Cookie đó để vào Hội nhóm cào bài viết tự động.
"""

from playwright.sync_api import sync_playwright
import time
import re
from phongtro123_scraper import get_coordinates, clean_price
from db_connection import insert_phong_tro

def login_and_save_state():
    """Mở trình duyệt cho người dùng đăng nhập tay và lưu phiên làm việc"""
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False) # Headless = False để hiện giao diện
        context = browser.new_context(
            viewport={'width': 1280, 'height': 720},
            user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        )
        page = context.new_page()
        page.goto('https://www.facebook.com/')
        
        print("\n" + "="*50)
        print("Trình duyệt đã mở. Vui lòng tự đăng nhập Facebook.")
        print("Sau khi trang chủ FB hiện ra đầy đủ, hãy GÕ 'XONG' vào cửa sổ Terminal/Console này rồi nhấn Enter.")
        print("="*50 + "\n")
        
        input("Nhập 'XONG' khi đã đăng nhập thành công: ")
        
        # Lưu Session
        context.storage_state(path="state.json")
        print("✅ Đã lưu phiên đăng nhập vào state.json. Giờ bạn có thể chạy cào dữ liệu.")
        browser.close()

def extract_room_info(post_text):
    """
    Sử dụng Regex để trích xuất Giá, Diện tích, Địa chỉ từ bài Post FB lộn xộn.
    Đây là kỹ năng cốt lõi của Data Engineer.
    """
    gia_phong = 0
    dien_tich = 20
    dia_chi = "Hà Nội"
    
    text_lower = post_text.lower()
    
    # 1. Tìm giá (Các mẫu như: 1.5tr, 1,5 triệu, 1500k, 2 củ)
    gia_match = re.search(r'([0-9]+[.,]?[0-9]*)\s*(tr|triệu|củ|k)', text_lower)
    if gia_match:
        gia_phong = clean_price(gia_match.group(0))
        
    # 2. Tìm diện tích (Các mẫu như: 25m2, 25 m vuông)
    dt_match = re.search(r'([0-9]+)\s*(m2|m vuông|mét vuông)', text_lower)
    if dt_match:
        dien_tich = int(dt_match.group(1))
        
    # 3. Tìm địa chỉ (Tìm từ khóa như: tại, ở, ngõ, đường, quận)
    dc_match = re.search(r'(tại|địa chỉ|ngõ|số nhà)\s*[:]?\s*(.*?)(?=\n|$)', text_lower)
    if dc_match:
        dia_chi_raw = dc_match.group(2).strip()
        # Lọc bớt rác (giới hạn độ dài địa chỉ)
        if 5 < len(dia_chi_raw) < 100:
            dia_chi = dia_chi_raw
            
    # Xử lý Geocode
    lat, lng = get_coordinates(dia_chi)
    
    return gia_phong, dien_tich, dia_chi, lat, lng

def scrape_group(group_url="https://www.facebook.com/groups/phongtrohanoi"):
    """Cào dữ liệu từ hội nhóm FB bằng Playwright sử dụng State đã lưu"""
    try:
        with sync_playwright() as p:
            # Chạy nền (Headless) hoặc hiện UI (headless=False) để debug
            browser = p.chromium.launch(headless=True)
            # Khôi phục trạng thái đã đăng nhập
            context = browser.new_context(storage_state="state.json")
            page = context.new_page()
            
            print(f"Đang vào nhóm: {group_url}")
            page.goto(group_url, timeout=60000)
            time.sleep(5) # Chờ FB load JS DOM
            
            # Cuộn trang xuống vài lần để lấy được nhiều bài viết
            print("Đang cuộn trang để lấy bài viết...")
            for _ in range(3):
                page.mouse.wheel(0, 2000)
                time.sleep(3)
                
            # Cấu trúc DOM của Facebook (Lưu ý: FB thường xuyên đổi class nên cần update lại Selector nếu lỗi)
            # div có role="article" thường chứa bài post
            posts = page.locator('div[role="article"]').all()
            print(f"Tìm thấy khoảng {len(posts)} bài viết.")
            
            count = 0
            for post in posts:
                try:
                    # Lấy text nội dung bài post
                    text_content = post.inner_text()
                    if not text_content or len(text_content) < 50:
                        continue
                        
                    # Bỏ qua nếu không giống bài đăng cho thuê phòng
                    if not any(keyword in text_content.lower() for keyword in ['cho thuê', 'nhượng', 'phòng trọ', 'ccmn']):
                        continue
                        
                    print("-" * 30)
                    print(f"Đang phân tích bài post: {text_content[:50]}...")
                    
                    # Cố gắng tìm thẻ img (thường lấy ảnh đầu tiên của bài)
                    image_urls = []
                    images = post.locator('img').all()
                    for img in images:
                        src = img.get_attribute('src')
                        # Bỏ qua các ảnh icon, emoji
                        if src and 'emoji' not in src and 'icon' not in src:
                            image_urls.append(src)
                    
                    # Extract Data
                    gia_phong, dien_tich, dia_chi, lat, lng = extract_room_info(text_content)
                    tieu_de = text_content[:100].replace('\n', ' ') + "..." # Lấy 100 ký tự đầu làm tiêu đề
                    
                    room_data = {
                        'tieu_de': tieu_de,
                        'gia_phong': gia_phong,
                        'dien_tich': dien_tich,
                        'dia_chi_chi_tiet': dia_chi,
                        'mo_ta': text_content,
                        'anh_phong': image_urls,
                        'lat': lat,
                        'lng': lng
                    }
                    
                    is_success = insert_phong_tro(room_data)
                    if is_success:
                        count += 1
                        
                except Exception as post_e:
                    print(f"Lỗi phân tích một bài post: {post_e}")
                    
            print(f"\n✅ CÀO FACEBOOK HOÀN TẤT. Thêm được {count} bài vào CSDL.")
            browser.close()
            
    except Exception as e:
        print(f"\n❌ LỖI NGHIÊM TRỌNG: Chưa có file state.json hoặc cấu trúc FB thay đổi.")
        print("Chi tiết:", e)
        print("👉 Vui lòng chạy mode 'login' trước để tạo state.json")

if __name__ == "__main__":
    import sys
    # Chạy `python facebook_scraper.py login` để đăng nhập
    # Chạy `python facebook_scraper.py scrape` để cào
    if len(sys.argv) > 1 and sys.argv[1] == 'login':
        login_and_save_state()
    else:
        scrape_group()
