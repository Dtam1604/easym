<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PhongTro
 *
 * @package App\Models
 * @property int $id
 * @property int $id_chu_tro
 * @property string $tieu_de
 * @property string|null $mo_ta
 * @property float $gia_phong
 * @property mixed $vi_tri
 * @property int $muc_do_xac_thuc
 */
class PhongTro extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'phong_tro';

    /**
     * Sử dụng $guarded để bảo vệ bảng khỏi Mass Assignment.
     * Ngoại trừ trường 'id', tất cả các trường khác đều có thể được gán dữ liệu.
     *
     * @var array<string>
     */
    protected $guarded = ['id'];

    /**
     * Các trường cần ép kiểu (Casting).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gia_phong' => 'float',
            'muc_do_xac_thuc' => 'integer',
            'anh_phong' => 'array',
            'anh_phap_ly' => 'array',
        ];
    }

    /**
     * Đăng ký Global Scopes cho Model.
     */
    protected static function booted(): void
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Local Scopes (Sử dụng để Lọc dữ liệu)
    |--------------------------------------------------------------------------
    */

    public function scopeKhoangGia(Builder $query, $min, $max): Builder
    {
        if ($min !== null) $query->where('gia_phong', '>=', $min);
        if ($max !== null) $query->where('gia_phong', '<=', $max);
        return $query;
    }

    public function scopeKhoangDienTich(Builder $query, $min, $max): Builder
    {
        if ($min !== null) $query->where('dien_tich', '>=', $min);
        if ($max !== null) $query->where('dien_tich', '<=', $max);
        return $query;
    }

    public function scopeXacThuc(Builder $query, $chiXacThuc): Builder
    {
        if ($chiXacThuc) {
            return $query->where('muc_do_xac_thuc', 2);
        }
        return $query;
    }

    public function scopeGioiTinh(Builder $query, $gioiTinh): Builder
    {
        if ($gioiTinh && $gioiTinh !== 'Tat ca') {
            return $query->whereIn('gioi_tinh_cho_thue', [$gioiTinh, 'Tat ca']);
        }
        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Mối quan hệ (Relationships)
    |--------------------------------------------------------------------------
    */

    /**
     * Lấy thông tin chủ trọ của phòng này.
     *
     * @return BelongsTo
     */
    public function chuTro(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'id_chu_tro');
    }

    /**
     * Lấy danh sách các báo cáo xác thực thực địa của phòng này.
     *
     * @return HasMany
     */
    public function baoCaoXacThuc(): HasMany
    {
        return $this->hasMany(XacThucThucDia::class, 'id_phong');
    }

    /**
     * Lấy danh sách người dùng đã yêu thích phòng trọ này.
     *
     * @return BelongsToMany
     */
    public function yeuThich(): BelongsToMany
    {
        return $this->belongsToMany(NguoiDung::class, 'danh_sach_yeu_thich', 'id_phong', 'id_nguoi_dung');
    }
}
