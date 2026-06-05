<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoiMoiOGhepNotification extends Notification
{
    public $loiMoi;
    public $message;
    public $trang_thai;
    public $type; // 'nhan_loi_moi' hoặc 'phan_hoi_loi_moi'

    /**
     * Create a new notification instance.
     */
    public function __construct($loiMoi, $message, $type, $trang_thai = null)
    {
        $this->loiMoi = $loiMoi;
        $this->message = $message;
        $this->type = $type; 
        $this->trang_thai = $trang_thai; 
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Chọn icon và màu sắc
        $icon = 'fa-user-group';
        $color = 'text-blue-500';
        
        if ($this->type === 'phan_hoi_loi_moi') {
            if ($this->trang_thai === 'dong_y') {
                $icon = 'fa-handshake';
                $color = 'text-emerald-500';
            } else if ($this->trang_thai === 'tu_choi') {
                $icon = 'fa-user-xmark';
                $color = 'text-red-500';
            }
        } elseif ($this->type === 'huy_ket_noi') {
            $icon = 'fa-user-slash';
            $color = 'text-orange-500';
        }

        return [
            'type' => 'loi_moi_o_ghep',
            'sub_type' => $this->type,
            'loi_moi_id' => $this->loiMoi->id,
            'message' => $this->message,
            'url' => route('tim-ban.index'), // Mở trang tìm bạn ở ghép
            'icon' => $icon,
            'color' => $color,
        ];
    }
}
