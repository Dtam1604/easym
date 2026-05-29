<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TrongSoThuatToan
 *
 * @package App\Models
 * @property int $id
 * @property string $ten_tieu_chi
 * @property float $trong_so_nen
 * @property float $he_so_uu_tien
 */
class TrongSoThuatToan extends Model
{
    /**
     * Tên bảng trong CSDL
     */
    protected $table = 'trong_so_thuat_toan';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'trong_so_nen' => 'float',
            'he_so_uu_tien' => 'float',
        ];
    }
}
