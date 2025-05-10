<?php
namespace App\Imports;

use App\Models\Booking;
use App\Models\PeminjamanBarang;
use App\Models\ListBarang;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BookingImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            $booking = new Booking([
                'kode_booking' => $row['kode_booking'],
                'tanggal' => Carbon::parse($row['tanggal'])->format('Y-m-d'),
                'nama_event' => $row['nama_event'],
                'nama_organisasi' => $row['nama_organisasi'],
                'kategori_event' => $row['kategori_event'],
                'kategori_ekraf' => $row['kategori_ekraf'],
                'jenis_event' => $row['jenis_event'],
                'ruangan_id' => (int) $row['ruangan_id'],
                'lantai' => (int) $row['lantai'],
                'waktu_mulai' => $row['waktu_mulai'],
                'waktu_selesai' => $row['waktu_selesai'],
                'nama_pic' => $row['nama_pic'],
                'no_pic' => $row['no_pic'],
                'status' => in_array($row['status'], ['Booking', 'Approved', 'Rejected']) ? $row['status'] : 'Booking',
                'created_at' => Carbon::parse($row['created_at'])->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($row['updated_at'])->format('Y-m-d H:i:s'),
            ]);

            $booking->save();

            if (!empty($row['barang_id']) && !empty($row['jumlah'])) {
                $barang = ListBarang::find($row['barang_id']);

                if ($barang && $barang->jumlah >= (int) $row['jumlah']) {
                    $barang->jumlah -= (int) $row['jumlah'];
                    $barang->save();

                    PeminjamanBarang::create([
                        'kode_booking' => $row['kode_booking'],
                        'barang_id' => (int) $row['barang_id'],
                        'jumlah' => (int) $row['jumlah'],
                        'marketing' => $row['marketing'] ?? 'Tidak Diketahui',
                        'created_by' => auth()->id(),
                    ]);
                } else {
                    Log::warning("Stok barang tidak mencukupi untuk booking {$row['kode_booking']}");
                    return null; // Return null jika stok barang tidak mencukupi
                }
            }

            return $booking;
        } catch (\Exception $e) {
            Log::error('Gagal mengimpor baris: ' . json_encode($row) . ' Error: ' . $e->getMessage());
            return null; // Return null jika terjadi kesalahan lainnya
        }
    }
}
