<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kehadiran;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RiwayatCheckinExport;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckinHistoryController extends Controller
{
  public function index(Request $request)
{
    $bulan = $request->input('bulan'); // Format: YYYY-MM

    $kehadiran = Kehadiran::with('booking')
        ->when($bulan, function ($query) use ($bulan) {
            $start = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $end = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
            return $query->whereBetween('tanggal_ci', [$start, $end]);
        })
        ->orderBy('tanggal_ci', 'desc')
        ->get();

    return view('riwayat.checkin', compact('kehadiran'));
}



    public function show($kode_booking)
    {
        $kehadiran = Kehadiran::with('booking.ruangan')
            ->where('kode_booking', $kode_booking)
            ->orderBy('tanggal_ci', 'desc')
            ->get();

        return view('riwayat.checkin_detail', compact('kehadiran', 'kode_booking'));
    }
    
 public function exportExcel(Request $request)
{
    $bulan = $request->input('bulan');

    return Excel::download(new RiwayatCheckinExport($bulan), 'riwayat_checkin_' . $bulan . '.xlsx');
}


public function exportPdf(Request $request)
{
    $bulan = $request->input('bulan');

    $kehadiran = Kehadiran::with([
        'booking', 'booking.ruangan', 'booking.peminjaman', 
        'dutyOfficer', 'fo'
    ])
    ->when($bulan, function ($query) use ($bulan) {
        $start = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $end = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        return $query->whereBetween('tanggal_ci', [$start, $end]);
    })
    ->orderBy('created_at', 'desc')
    ->get();

    $pdf = PDF::loadView('exports.riwayat-checkin-pdf', compact('kehadiran'))
              ->setPaper('a4', 'landscape');

    return $pdf->download('riwayat_checkin_' . $bulan . '.pdf');
}



}
