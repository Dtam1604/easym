import os
import psycopg2
from psycopg2.extras import Json
from dotenv import load_dotenv

# Load biến môi trường từ file .env của Laravel
load_dotenv(dotenv_path='../.env')

# Lấy cấu hình kết nối từ biến môi trường
DB_HOST = os.getenv('DB_HOST', '127.0.0.1')
DB_PORT = os.getenv('DB_PORT', '5432')
DB_DATABASE = os.getenv('DB_DATABASE', 'easym')
DB_USERNAME = os.getenv('DB_USERNAME', 'postgres')
DB_PASSWORD = os.getenv('DB_PASSWORD', '162004')

def get_connection():
    """Tạo kết nối tới PostgreSQL"""
    try:
        conn = psycopg2.connect(
            host=DB_HOST,
            port=DB_PORT,
            database=DB_DATABASE,
            user=DB_USERNAME,
            password=DB_PASSWORD
        )
        return conn
    except Exception as e:
        print(f"Lỗi kết nối CSDL: {e}")
        return None

def insert_phong_tro(data):
    """
    Hàm insert một phòng trọ vào bảng phong_tro hỗ trợ chuẩn PostGIS
    :param data: dict chứa thông tin phòng
        - tieu_de, mo_ta, gia_phong, dien_tich, dia_chi_chi_tiet
        - anh_phong (list URL)
        - lat, lng (Kinh độ, Vĩ độ)
    """
    conn = get_connection()
    if not conn:
        return False
        
    try:
        cursor = conn.cursor()
        
        # Mặc định gán cho User ID = 1 (Thường là Admin) làm chủ trọ cho các tin cào về
        id_chu_tro = 1 
        trang_thai_thue = 1 # 1 = Còn phòng (Đang cho thuê)
        muc_do_xac_thuc = 1 # 1 = Đã xác thực cơ bản (Để phòng hiển thị trên web)
        
        # Xử lý mảng hình ảnh chuyển thành JSON để insert vào PostgreSQL JSON/JSONB column
        anh_phong_json = Json(data.get('anh_phong', []))
        anh_phap_ly_json = Json([])
        
        # Câu lệnh SQL có sử dụng ST_SetSRID và ST_MakePoint cho cột vi_tri (PostGIS geometry)
        sql = """
            INSERT INTO phong_tro (
                id_chu_tro, tieu_de, mo_ta, gia_phong, dien_tich, 
                dia_chi_chi_tiet, anh_phong, anh_phap_ly, 
                muc_do_xac_thuc, trang_thai_thue, vi_tri,
                created_at, updated_at
            ) VALUES (
                %s, %s, %s, %s, %s, 
                %s, %s, %s, 
                %s, %s, ST_SetSRID(ST_MakePoint(%s, %s), 4326),
                NOW(), NOW()
            ) RETURNING id;
        """
        
        values = (
            id_chu_tro,
            data['tieu_de'],
            data['mo_ta'],
            data['gia_phong'],
            data.get('dien_tich', 20),
            data['dia_chi_chi_tiet'],
            anh_phong_json,
            anh_phap_ly_json,
            muc_do_xac_thuc,
            trang_thai_thue,
            data['lng'],  # Kinh độ đứng trước trong ST_MakePoint
            data['lat']   # Vĩ độ đứng sau
        )
        
        cursor.execute(sql, values)
        phong_id = cursor.fetchone()[0]
        
        conn.commit()
        print(f"✅ Đã chèn thành công phòng trọ mới! (ID: {phong_id}) - {data['tieu_de']}")
        return True
        
    except Exception as e:
        print(f"❌ Lỗi khi chèn dữ liệu: {e}")
        if conn:
            conn.rollback()
        return False
    finally:
        if conn:
            cursor.close()
            conn.close()
