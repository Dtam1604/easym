import requests
import json
import re
from db_connection import insert_phong_tro
import time
import os

def extract_ids_by_regex_pure(file_path):
    """
    Quét văn bản thô để nhặt ra các cụm ID phòng trọ
    """
    if not os.path.exists(file_path):
        print(f"❌ Không tìm thấy file '{file_path}'!")
        return []

    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        raw_ids = re.findall(r'\b\d{8,10}\b', content)
        list_ids = list(set(raw_ids))
        print(f"🎯 Quét văn bản thô thành công: Tìm thấy {len(list_ids)} ID phòng trọ khả thi.")
        return list_ids
    except Exception as e:
        print(f"❌ Lỗi khi quét văn bản thô: {e}")
        return []

def clean_price(price_str):
    if not price_str:
        return 0
    try:
        return int(float(price_str))
    except ValueError:
        price_str = str(price_str).lower()
        match = re.search(r'([0-9]+[.,]?[0-9]*)', price_str)
        if not match:
            return int(re.sub(r'[^0-9]', '', price_str) or 0)
        num = float(match.group(1).replace(',', '.'))
        if 'tỷ' in price_str or 'ty' in price_str:
            return int(num * 1000000000)
        if 'tr' in price_str or 'triệu' in price_str or 'trieu' in price_str:
            return int(num * 1000000)
        elif 'nghìn' in price_str or 'k' in price_str or 'nghin' in price_str:
            return int(num * 1000)
        return int(re.sub(r'[^0-9]', '', price_str) or 0)

def scrape_data_real():
    file_nguon = 'view.json' 
    list_ids = extract_ids_by_regex_pure(file_nguon)
    
    if not list_ids:
        print("❌ Không tìm thấy bất kỳ cụm số ID nào trong file view.json.")
        return

    # Nâng cấp bộ Headers giả lập trình duyệt xịn hơn để tránh bị tường lửa quét
    headers = {
        'Accept': 'application/json, text/plain, */*',
        'Accept-Language': 'vi,en-US;q=0.9,en;q=0.8',
        'Cache-Control': 'no-cache',
        'Pragma': 'no-cache',
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
        'Referer': 'https://nhasieutot.com/',
        'Origin': 'https://nhasieutot.com'
    }

    print(f"🚀 Bắt đầu kích hoạt tiến trình cào dữ liệu chi tiết cho {len(list_ids)} phòng...")
    count_success = 0
    count_error = 0

    for index, room_id in enumerate(list_ids):
        url_detail = f'https://nhasieutot.com/api/v1/map/marker-detail?id={room_id}&source=listing&listing_source=nhatot'
        print(f"[{index + 1}/{len(list_ids)}] Đang gọi API lấy dữ liệu phòng ID: {room_id}...")
        
        try:
            response = requests.get(url_detail, headers=headers, timeout=15)
            
            if response.status_code != 200:
                print(f"   => ❌ Lỗi HTTP {response.status_code}. Server từ chối kết nối.")
                count_error += 1
                time.sleep(2) # Nghỉ lâu hơn nếu gặp lỗi kết nối
                continue
                
            try:
                res_data = response.json()
            except Exception:
                print("   => ❌ Server không trả về cấu trúc JSON hợp lệ (Có thể dính trang chặn Cloudflare).")
                count_error += 1
                continue

            if res_data is None:
                print("   => ⚠️ Phản hồi từ Server trả về rỗng (None).")
                count_error += 1
                continue

            # Xử lý kiểm tra linh hoạt cấu trúc JSON trả về để tránh lỗi NoneType
            if isinstance(res_data, dict):
                room = res_data.get('data') or res_data
            else:
                room = res_data

            if not room or not isinstance(room, dict) or 'latitude' not in room:
                print("   => ⚠️ Cấu trúc chi tiết phòng trống hoặc không chứa dữ liệu tọa độ.")
                count_error += 1
                continue
            
            # Tiến hành bóc tách dữ liệu sạch khi đã an toàn 100%
            tieu_de = room.get('subject', 'Phòng trọ EasyM')
            price_raw = room.get('price', 0)
            gia_phong = clean_price(price_raw)
            anh_phong = room.get('media_urls', [])
            mo_ta = room.get('body', 'Chưa có mô tả chi tiết.')
            
            try:
                dien_tich = float(room.get('size', 20.0))
            except:
                dien_tich = 20.0
                
            lat = float(room.get('latitude'))
            lng = float(room.get('longitude'))
            dia_chi = room.get('address_full', 'Hà Nội')

            room_data = {
                'tieu_de': tieu_de,
                'gia_phong': gia_phong,
                'dien_tich': dien_tich,
                'dia_chi_chi_tiet': dia_chi,
                'mo_ta': mo_ta,
                'anh_phong': anh_phong, 
                'lat': lat,
                'lng': lng
            }
            
            # Đẩy dữ liệu sang hàm kết nối CSDL của bạn
            is_success = insert_phong_tro(room_data)
            if is_success:
                print(f"   => [PostGIS] Đã lưu thành công: {tieu_de[:35]}...")
                count_success += 1
            else:
                print("   => ❌ Hàm insert_phong_tro trong DB trả về False.")
                count_error += 1
            
            # Giãn cách 1.5 giây an toàn bảo mật
            time.sleep(1.5)

        except Exception as e:
            count_error += 1
            print(f"   => ❌ Lỗi xử lý bản ghi: {e}")
            continue

    print("\n🎉 HOÀN TẤT TIẾN TRÌNH ĐỒNG BỘ DỮ LIỆU THỰC TẾ!")
    print(f"- Số lượng phòng nạp thành công: {count_success}")
    print(f"- Số lượng phòng thất bại/bỏ qua: {count_error}")

if __name__ == "__main__":
    scrape_data_real()