<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class XacThucThucDia
 *
 * @package App\Models
 * @property int $id
 * @property int $id_phong
 * @property int $id_nguoi_xac_thuc
 * @property array|null $bao_cao_chi_tiet
 * @property string $trang_thai
 * @property \Illuminate\Support\Carbon|null $ngay_thuc_hien
 */
class XacThucThucDia extends Model
{
    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'xac_thuc_thuc_dia';

    /**
     * Các trường được phép Mass Assignment.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id_phong',
        'id_nguoi_xac_thuc',
        'bao_cao_chi_tiet',
        'trang_thai',
        'ngay_thuc_hien'
    ];

    /**
     * Các trường cần ép kiểu (Casting).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bao_cao_chi_tiet' => 'array',
            'ngay_thuc_hien' => 'datetime',
        ];
    }

    /**
     * Lấy thông tin phòng trọ được xác thực.
     *
     * @return BelongsTo
     */
    public function phongTro(): BelongsTo
    {
        return $this->belongsTo(PhongTro::class, 'id_phong');
    }

    /**
     * Lấy thông tin Cộng tác viên đi xác thực.
     *
     * @return BelongsTo
     */
    public function congTacVien(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_xac_thuc');
    }
}
