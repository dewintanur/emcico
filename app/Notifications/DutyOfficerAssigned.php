<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class DutyOfficerAssigned extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['database']; // Simpan ke database
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Anda ditugaskan untuk Booking #{$this->booking->kode_booking}.",
            'booking_id' => $this->booking->id,
            'ruangan_id' => $this->booking->ruangan_id, // Kirim ruangan_id
            'url' => url("/ruangan?highlight={$this->booking->id}") // URL ke halaman ruangan dengan parameter highlight
        ];
    }
    
}
