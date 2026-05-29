import requests
from bs4 import BeautifulSoup
import re
from geopy.geocoders import Nominatim
from db_connection import insert_phong_tro
import time
import random

# Khởi tạo Geocoder để dịch địa chỉ thành Kinh độ/Vĩ độ
geolocator = Nominatim(user_agent="easym_scraper_kltn")

def get_coordinates(address):
    """Sử dụng Nominatim để lấy tọa độ từ chuỗi địa chỉ"""
    try:
        full_address = f"{address}, Việt Nam"
        location = geolocator.geocode(full_address, timeout=10)
        if location:
            return location.latitude, location.longitude
        return 21.028511, 105.804817 # Mặc định Hà Nội nếu không tìm thấy
    except Exception as e:
        print(f"Lỗi Geocode: {e}")
        return 21.028511, 105.804817

def clean_price(price_str):
    """
    Chuyển đổi giá tiền từ dạng text sang số nguyên
    VD: '1.5 triệu/tháng' -> 1500000
    """
    price_str = str(price_str).lower()
    match = re.search(r'([0-9]+[.,]?[0-9]*)', price_str)
    if not match:
        return 0
        
    num = float(match.group(1).replace(',', '.'))
    
    if 'triệu' in price_str:
        return int(num * 1000000)
    elif 'nghìn' in price_str or 'k' in price_str:
        return int(num * 1000)
    return int(num)

def scrape_phongtro123(url="https://phongtro123.com/tinh-thanh/ha-noi/quan-ha-dong"):
    """Cào dữ liệu danh sách phòng từ trang web mẫu bằng JSON-LD"""
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    }
    
    print(f"Đang tải trang: {url}")
    response = requests.get(url, headers=headers)
    
    if response.status_code != 200:
        print(f"Lỗi khi tải trang: {response.status_code}")
        return
        
    soup = BeautifulSoup(response.text, 'html.parser')
    
    import json
    
    # Tìm tất cả các đoạn JSON-LD chứa dữ liệu Hostel/Phòng trọ
    json_scripts = soup.find_all('script', type='application/ld+json')
    posts = []
    
    for script in json_scripts:
        try:
            data = json.loads(script.string)
            if isinstance(data, dict) and data.get('@type') in ['Hostel', 'Product', 'RealEstateAgent']:
                posts.append(data)
        except Exception:
            pass
            
    print(f"Tìm thấy {len(posts)} tin đăng có cấu trúc chuẩn. Bắt đầu xử lý...")
    
    count = 0
    for post in posts:
        try:
            # Nếu không phải là Hostel (Phòng trọ) thì có thể bỏ qua
            if post.get('@type') != 'Hostel':
                continue
                
            tieu_de = post.get('name', '')
            mo_ta = post.get('description', '')
            
            # Lấy giá tiền
            gia_phong = 0
            if 'priceRange' in post:
                gia_phong_str = str(post['priceRange']).replace('.', '').replace(',', '')
                match = re.search(r'([0-9]+)', gia_phong_str)
                if match:
                    gia_phong = int(match.group(1))
                    
            # Lấy diện tích (thường không có trong JSON-LD, sẽ parse thêm từ tiêu đề hoặc mô tả)
            dien_tich = 20
            dt_match = re.search(r'([0-9]+)\s*m2', mo_ta.lower() + " " + tieu_de.lower())
            if dt_match:
                dien_tich = int(dt_match.group(1))
                
            # Lấy địa chỉ
            dia_chi = "Hà Nội"
            if 'address' in post and isinstance(post['address'], dict):
                dia_chi = post['address'].get('streetAddress', dia_chi)
                
            # Lấy URL trang chi tiết
            detail_url = post.get('url', '')
            images = []
            mo_ta = post.get('description', '')
            
            # Cào trang chi tiết để lấy ĐẦY ĐỦ ảnh và mô tả
            if detail_url:
                try:
                    time.sleep(random.uniform(0.5, 1.5)) # Tránh bị chặn
                    res_detail = requests.get(detail_url, headers=headers, timeout=10)
                    if res_detail.status_code == 200:
                        soup_detail = BeautifulSoup(res_detail.text, 'html.parser')
                        
                        # Lấy tất cả ảnh chất lượng cao 900x600
                        detail_images = list(set([img['src'] for img in soup_detail.find_all('img') if 'pt123.cdn.static123.com' in img.get('src', '') and '900x600' in img.get('src', '')]))
                        if detail_images:
                            images = detail_images
                            
                        # Lấy mô tả chi tiết đầy đủ
                        h2_desc = soup_detail.find(string=lambda t: t and 'Thông tin mô tả' in t)
                        if h2_desc and h2_desc.parent and h2_desc.parent.parent:
                            p_tags = h2_desc.parent.parent.find_all('p')
                            full_desc = '\n'.join([p.get_text(strip=True) for p in p_tags if p.get_text(strip=True)])
                            if full_desc:
                                mo_ta = full_desc
                except Exception as e:
                    print(f"Lỗi khi cào trang chi tiết {detail_url}: {e}")

            # Fallback hình ảnh nếu trang chi tiết không có
            if not images and 'image' in post:
                if isinstance(post['image'], list):
                    images = post['image']
                else:
                    images = [post['image']]
            # Lấy tọa độ bằng Geocoding
            lat, lng = get_coordinates(dia_chi)
            
            # Đóng gói dữ liệu
            room_data = {
                'tieu_de': tieu_de,
                'gia_phong': gia_phong,
                'dien_tich': dien_tich,
                'dia_chi_chi_tiet': dia_chi,
                'mo_ta': mo_ta,
                'anh_phong': images,
                'lat': lat,
                'lng': lng
            }
            
            # Insert vào database
            is_success = insert_phong_tro(room_data)
            if is_success:
                count += 1
                
            time.sleep(random.uniform(0.5, 1.5))
            
        except Exception as e:
            print(f"Lỗi xử lý tin đăng: {e}")
            continue
            
    print(f"\n✅ HOÀN TẤT! Cào thành công {count} phòng trọ và đẩy vào Database EasyM.")

if __name__ == "__main__":
    # Để cào trang đầu tiên của Hà Nội
    scrape_phongtro123("https://phongtro123.com/tinh-thanh/ha-noi/quan-ha-dong")
