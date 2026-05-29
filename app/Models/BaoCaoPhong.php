<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaoCaoPhong extends Model
{
    use HasFactory;

    protected $table = 'bao_cao_phong';

    protected $fillable = [
        'id_nguoi_bao_cao',
        'id_phong',
        'ly_do',
        'chi_tiet',
        'trang_thai',
    ];

    public function nguoiBaoCao()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_bao_cao');
    }

    public function phong()
    {
        return $this->belongsTo(PhongTro::class, 'id_phong');
    }
}
