<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LichHen
 *
 * @package App\Models
 * @property int $id
 * @property int $id_nguoi_thue
 * @property int $id_chu_tro
 * @property int $id_phong
 * @property string $thoi_gian_hen
 * @property string $trang_thai_cuoc_hen
 */
class LichHen extends Model
{
    /**
     * Tên bảng trong cơ sở dữ liệu.
     *
     * @var string
     */
    protected $table = 'lich_hen';

    /**
     * Bảo vệ Model khỏi lỗi Mass Assignment bằng cách chỉ chặn trường 'id'.
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
            'thoi_gian_hen' => 'datetime',
        ];
    }

    /**
     * Lấy thông tin người thuê (người đặt lịch hẹn).
     *
     * @return BelongsTo
     */
    public function nguoiThue(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_thue');
    }

    /**
     * Lấy thông tin chủ trọ của cuộc hẹn.
     *
     * @return BelongsTo
     */
    public function chuTro(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'id_chu_tro');
    }

    /**
     * Lấy thông tin phòng trọ được hẹn xem.
     *
     * @return BelongsTo
     */
    public function phongTro(): BelongsTo
    {
        return $this->belongsTo(PhongTro::class, 'id_phong');
    }
}
