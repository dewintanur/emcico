<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Booking;
use App\Models\Kehadiran;

class RuanganBelumSiap extends Notification
{
    use Queueable;

    protected $ruangan;

    public function __construct($ruangan)
    {
        $this->ruangan = $ruangan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    
    public function toDatabase($notifiable)
    {
        // Ambil kehadiran terakhir yang masih aktif (checked-in)
        $kehadiran = Kehadiran::where('ruangan_id', $this->ruangan->id)
            ->where('status', 'checked-in')
            ->latest()
            ->first();

        // Debugging log
        \Log::info('Debugging Kehadiran:', ['kehadiran' => $kehadiran]);

        $kode_booking = null;

        if ($kehadiran) {
            // Coba ambil booking berdasarkan kehadiran ini
            $booking = Booking::where('kode_booking', $kehadiran->kode_booking)->first();

            if ($booking) {
                $kode_booking = $booking->kode_booking;
            }
        }

        // Debugging log untuk kode booking
        \Log::info('Debugging: Kode Booking = ' . ($kode_booking ?? 'Tidak ada'));

        return [
            'message' => "Ruangan {$this->ruangan->nama_ruangan} belum siap checkout. User diharap kembali ke ruangan.",
            'room_id' => $this->ruangan->id,
            'kode_booking' => $kode_booking, // Tambahkan kode booking
        ];
    }
}
