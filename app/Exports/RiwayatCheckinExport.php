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
        return Kehadiran::with(['booking.peminjaman', 'dutyOfficer', 'fo'])
            ->when($this->tanggal, function ($query) {
                return $query->whereDate('tanggal_ci', $this->tanggal);
            })
            ->get()
            ->map(function ($data) {
                return [
                    'Kode Booking'      => $data->kode_booking,
                    'Nama Organisasi'   => $data->booking->nama_organisasi ?? '-',
                    'Nama Event'        => $data->booking->nama_event ?? '-',
                    'Nama User'         => $data->nama_ci,
                    'Tanggal Check-in'  => \Carbon\Carbon::parse($data->tanggal_ci)->format('d F Y, H:i'),
                    'Waktu Mulai'       => \Carbon\Carbon::parse($data->booking->waktu_mulai)->format('H:i'),
                    'Waktu Selesai'     => \Carbon\Carbon::parse($data->booking->waktu_selesai)->format('H:i'),
                    'Ruangan'           => $data->booking->ruangan->nama_ruangan ?? '-',
                    'Lantai'            => $data->booking->lantai ?? '-',
                    'Peminjaman Barang' => $data->booking->peminjaman->isNotEmpty() ? 'Ada' : 'Tidak Ada',
                    'Marketing'         => $data->booking->peminjaman->isNotEmpty() 
                        ? implode(', ', $data->booking->peminjaman->pluck('marketing')->toArray()) 
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

