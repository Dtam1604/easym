<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LichHenNotification extends Notification
{
    public $lichHen;
    public $message;
    public $trang_thai;

    /**
     * Create a new notification instance.
     */
    public function __construct($lichHen, $message, $trang_thai)
    {
        $this->lichHen = $lichHen;
        $this->message = $message;
        $this->trang_thai = $trang_thai; // 'da_duyet' or 'tu_choi'
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // Chỉ lưu vào db, không gửi email
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'lich_hen',
            'lich_hen_id' => $this->lichHen->id,
            'phong_tro_id' => $this->lichHen->id_phong,
            'message' => $this->message,
            'trang_thai' => $this->trang_thai,
            'url' => route('room.show', $this->lichHen->id_phong),
            'icon' => $this->trang_thai === 'da_duyet' ? 'fa-calendar-check' : 'fa-calendar-xmark',
            'color' => $this->trang_thai === 'da_duyet' ? 'text-emerald-500' : 'text-red-500',
        ];
    }
}
