<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kehadiran;
use App\Notifications\CheckoutReminderForFONotification;
use Carbon\Carbon;

class KirimNotifikasiCheckout extends Command
{
    protected $signature = 'app:kirim-notifikasi-checkout';
    protected $description = 'Notifikasi pengingat untuk Front Office memilih duty officer saat checkout';

    public function handle()
    {
        $sekarang = Carbon::now();
        $batas = $sekarang->copy()->addMinutes(30);
    
        $kehadiranList = Kehadiran::where('check_out_time', '>=', $sekarang)
            ->where('check_out_time', '<=', $batas)
            ->where('notified', false)
            ->get();
    
        foreach ($kehadiranList as $kehadiran) {
            // Kirim notifikasi kepada Front Office (FO)
            $fo = User::where('role', 'FO')->first(); // Asumsikan hanya ada satu FO yang bisa menerima pengingat
            
            if ($fo) {
                $fo->notify(new CheckoutReminderForFONotification($kehadiran));
                $kehadiran->update(['notified' => true]);
            }
        }
    }
}
