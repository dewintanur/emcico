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
    protected $note;  // tambahkan properti note

    public function __construct($ruangan, $note = null)
    {
        $this->ruangan = $ruangan;
        $this->note = $note;  // simpan note dari konstruktor
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

        \Log::info('Debugging Kehadiran:', ['kehadiran' => $kehadiran]);

        $kode_booking = null;

        if ($kehadiran) {
            $booking = Booking::where('kode_booking', $kehadiran->kode_booking)->first();
            if ($booking) {
                $kode_booking = $booking->kode_booking;
            }
        }

        \Log::info('Debugging: Kode Booking = ' . ($kode_booking ?? 'Tidak ada'));

        return [
            'message' => "Ruangan {$this->ruangan->nama_ruangan} belum siap checkout. User diharap kembali ke ruangan.",
            'room_id' => $this->ruangan->id,
            'kode_booking' => $kode_booking,
            'note' => $this->note,  // tambahkan note ke payload notifikasi
        ];
    }
}
