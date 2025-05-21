<?php

namespace App\Exports;

use App\Models\Kehadiran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RiwayatCheckinExport implements FromCollection, WithHeadings
{
    protected $tanggal;

    public function __construct($tanggal = null)
    {
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        return Kehadiran::with(['booking.peminjaman.marketing', 'booking.ruangan', 'dutyOfficer', 'fo'])
            ->when($this->tanggal, function ($query) {
                return $query->whereYear('tanggal_ci', substr($this->tanggal, 0, 4))
                    ->whereMonth('tanggal_ci', substr($this->tanggal, 5, 2));
            })
            ->get()
            ->map(function ($data) {
                $booking = $data->booking;
                $peminjaman = $booking?->peminjaman ?? collect();
                $ruangan = $booking?->ruangan;

                return [
                    'Kode Booking' => $data->kode_booking,
                    'Nama Event' => $booking->nama_event ?? '-',
                    'Nama Organisasi' => $booking->nama_organisasi ?? '-',
                    'Nama PIC' => $booking->nama_pic ?? '-',
                    'Nama User' => $data->nama_ci ?? '-',
                    'Tanggal Check-in' => $data->created_at
                        ? \Carbon\Carbon::parse($data->created_at)->format('d F Y, H:i')
                        : '-',
                    'Tanggal Check-out' => $data->updated_at && $data->updated_at != $data->created_at
                        ? \Carbon\Carbon::parse($data->updated_at)->format('d F Y, H:i')
                        : 'Belum Checkout',
                    'Waktu Mulai' => $booking?->waktu_mulai
                        ? \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i')
                        : '-',
                    'Waktu Selesai' => $booking?->waktu_selesai
                        ? \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i')
                        : '-',
                    'Ruangan' => $ruangan?->nama_ruangan ?? '-',
                    'Lantai' => $booking->lantai ?? '-',
                    'Peminjaman Barang' => $peminjaman->isNotEmpty() ? 'Ada' : 'Tidak Ada',
                    'Marketing' => $peminjaman->isNotEmpty()
                        ? implode(', ', $peminjaman->pluck('marketing')->filter()->unique()->toArray())
                        : 'Tidak Ada',
                    'Tanda Tangan' => $data->ttd ? 'Ada' : 'Tidak Ada',
                    'Duty Officer' => $data->dutyOfficer->nama ?? 'Tidak Ada',
                    'Front Office' => $data->fo->nama ?? 'Belum Checkout',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode Booking',
            'Nama Event',
            'Nama Organisasi',
            'Nama PIC',
            'Nama User',
            'Tanggal Check-in',
            'Tanggal Check-out',
            'Waktu Mulai',
            'Waktu Selesai',
            'Ruangan',
            'Lantai',
            'Peminjaman Barang',
            'Marketing',
            'Tanda Tangan',
            'Duty Officer',
            'Front Office',
        ];
    }
}
