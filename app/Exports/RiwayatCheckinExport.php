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
                return $query->whereDate('tanggal_ci', $this->tanggal);
            })
            ->get()
            ->map(function ($data) {
                $booking = $data->booking;
                $peminjaman = $booking?->peminjaman ?? collect();
                $ruangan = $booking?->ruangan;

                return [
                    'Kode Booking'      => $data->kode_booking,
                    'Nama Organisasi'   => $booking->nama_organisasi ?? '-',
                    'Nama Event'        => $booking->nama_event ?? '-',
                    'Nama User'         => $data->nama_ci ?? '-',
                    'Tanggal Check-in'  => $data->tanggal_ci ? \Carbon\Carbon::parse($data->tanggal_ci)->format('d F Y, H:i') : '-',
                    'Waktu Mulai'       => $booking?->waktu_mulai ? \Carbon\Carbon::parse($booking->waktu_mulai)->format('H:i') : '-',
                    'Waktu Selesai'     => $booking?->waktu_selesai ? \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') : '-',
                    'Ruangan'           => $ruangan?->nama_ruangan ?? '-',
                    'Lantai'            => $booking->lantai ?? '-',
                    'Peminjaman Barang' => $peminjaman->isNotEmpty() ? 'Ada' : 'Tidak Ada',
                    'Marketing'         => $peminjaman->isNotEmpty()
                                            ? implode(', ', $peminjaman->pluck('marketing.nama')->filter()->unique()->toArray())
                                            : 'Tidak Ada',
                    'Tanda Tangan'      => $data->ttd ? 'Ada' : 'Tidak Ada',
                    'Duty Officer'      => $data->dutyOfficer->nama ?? 'Tidak Ada',
                    'Front Office'      => $data->fo->nama ?? 'Belum Checkout',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode Booking', 'Nama Organisasi', 'Nama Event', 'Nama User', 
            'Tanggal Check-in', 'Waktu Mulai', 'Waktu Selesai', 
            'Ruangan', 'Lantai', 'Peminjaman Barang', 'Marketing', 'Tanda Tangan', 
            'Duty Officer', 'Front Office'
        ];
    }
}
