<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Kehadiran;
class CheckoutReminderNotification extends Notification
{
    use Queueable;

   
    public function via($notifiable)
    {
        return ['database'];
    }
    

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Pengingat untuk memilih duty officer untuk checkout.',
            'kode_booking' => $this->kehadiran->kode_booking, // Menggunakan kode_booking dari Kehadiran
        ];
    }
protected $kehadiran;

public function __construct($kehadiran)
{
    $this->kehadiran = $kehadiran;
}


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Pengingat waktu checkout, segera pilih duty officer.',
            'kode_booking' => $this->kehadiran->kode_booking,
        ];
    }
}
