<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoiMoiOGhep extends Model
{
    use HasFactory;

    protected $table = 'loi_moi_o_ghep';

    protected $fillable = [
        'id_nguoi_gui',
        'id_nguoi_nhan',
        'trang_thai', // cho_duyet, chap_nhan, tu_choi
    ];

    /**
     * Người gửi lời mời
     */
    public function nguoiGui(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_gui');
    }

    /**
     * Người nhận lời mời
     */
    public function nguoiNhan(): BelongsTo
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_nhan');
    }
}
