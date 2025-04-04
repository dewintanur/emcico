<?php
namespace App\Imports;

use App\Models\Booking;
use App\Models\PeminjamanBarang;
use App\Models\ListBarang;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingImport implements ToModel
{
    public function model(array $row)
    {
        try {
            // Import data booking
            $booking = new Booking([
                'kode_booking' => $row[0],
                'tanggal' => Carbon::parse($row[1])->format('Y-m-d'),
                'nama_event' => $row[2],
                'nama_organisasi' => $row[3],
                'kategori_event' => $row[4],
                'kategori_ekraf' => $row[5],
                'jenis_event' => $row[6],
                'ruangan_id' => (int) $row[7],  
                'lantai' => (int) $row[8],
                'waktu_mulai' => $row[9],
                'waktu_selesai' => $row[10],
                'nama_pic' => $row[11],
                'no_pic' => $row[12],
                'status' => in_array($row[13], ['Booking', 'Approved', 'Rejected']) ? $row[13] : 'Booking',
                'created_at' => Carbon::parse($row[14])->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($row[15])->format('Y-m-d H:i:s'),
            ]);

            $booking->save();

            // Cek apakah ada barang yang dipinjam (Kolom Barang di Excel)
            if (!empty($row[16]) && !empty($row[17])) { // Kolom ke-16 = barang_id, ke-17 = jumlah
                $barang = ListBarang::find($row[16]);

                if ($barang && $barang->jumlah >= (int) $row[17]) {
                    // Kurangi stok barang
                    $barang->jumlah -= (int) $row[17];
                    $barang->save();

                    // Simpan peminjaman barang
                    PeminjamanBarang::create([
                        'kode_booking' => $row[0],
                        'barang_id' => (int) $row[16],
                        'jumlah' => (int) $row[17],
                        'marketing' => $row[18] ?? 'Tidak Diketahui',
                        'created_by' => auth()->id(),
                    ]);
                } else {
                    Log::warning("Stok barang tidak mencukupi untuk booking {$row[0]}");
                }
            }

            return $booking;
        } catch (\Exception $e) {
            Log::error('Gagal mengimpor baris: ' . json_encode($row) . ' Error: ' . $e->getMessage());
            return null;
        }
    }
}
