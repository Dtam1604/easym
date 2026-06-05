<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class NguoiDung
 *
 * @package App\Models
 * @property int $id
 * @property string $ho_ten
 * @property string $email
 * @property string $mat_khau
 * @property string|null $so_dien_thoai
 * @property string $vai_tro
 * @property array|null $khao_sat_loi_song
 * @property string|null $gioi_tinh
 * @property int|null $nam_sinh
 * @property string|null $nghe_nghiep
 * @property string|null $anh_dai_dien
 * @property bool $da_xac_thuc_cccd
 * @property array|null $thong_tin_cccd
 */
class NguoiDung extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'nguoi_dung';

    /**
     * Các trường được phép thêm/sửa hàng loạt (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ho_ten',
        'email',
        'mat_khau',
        'google_id',
        'so_dien_thoai',
        'thanh_pho',
        'vai_tro',
        'dia_ban_quan_ly',
        'trang_thai_khoa',
        'khao_sat_loi_song',
        'gioi_tinh',
        'nam_sinh',
        'nghe_nghiep',
        'anh_dai_dien',
        'da_xac_thuc_cccd',
        'thong_tin_cccd',
    ];

    /**
     * Các trường cần bị ẩn đi khi serialize.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'mat_khau',
    ];

    /**
     * Các trường cần ép kiểu (Casting).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mat_khau' => 'hashed',
            'khao_sat_loi_song' => 'array',
            'thong_tin_cccd' => 'array',
            'da_xac_thuc_cccd' => 'boolean',
            'trang_thai_khoa' => 'boolean',
        ];
    }

    /**
     * Ghi đè phương thức getAuthPassword để Laravel sử dụng cột 'mat_khau' thay cho 'password'.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    /*
    |--------------------------------------------------------------------------
    | Mối quan hệ (Relationships)
    |--------------------------------------------------------------------------
    */

    /**
     * Lấy danh sách các phòng trọ do người này làm chủ.
     *
     * @return HasMany
     */
    public function danhSachPhongTro(): HasMany
    {
        return $this->hasMany(PhongTro::class, 'id_chu_tro');
    }

    /**
     * Lấy danh sách lịch hẹn của người thuê này.
     *
     * @return HasMany
     */
    public function lichHenCuaToi(): HasMany
    {
        return $this->hasMany(LichHen::class, 'id_nguoi_thue');
    }

    /**
     * Lời mời ở ghép do tôi gửi đi
     */
    public function loiMoiDaGui(): HasMany
    {
        return $this->hasMany(LoiMoiOGhep::class, 'id_nguoi_gui');
    }

    /**
     * Lời mời ở ghép người khác gửi cho tôi
     */
    public function loiMoiNhanDuoc(): HasMany
    {
        return $this->hasMany(LoiMoiOGhep::class, 'id_nguoi_nhan');
    }

    /**
     * Lấy danh sách các báo cáo thực địa do CTV này khảo sát.
     */
    public function xacThucThucDias(): HasMany
    {
        return $this->hasMany(XacThucThucDia::class, 'id_nguoi_xac_thuc');
    }

    /*
    |--------------------------------------------------------------------------
    | Logic Vai trò (Role Methods)
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->vai_tro === 'admin';
    }

    public function isChuTro(): bool
    {
        return $this->vai_tro === 'chu_tro';
    }

    public function isCongTacVien(): bool
    {
        return $this->vai_tro === 'cong_tac_vien';
    }
}
