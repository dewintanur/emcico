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
    // Ambil tanggal dari request
    $tanggal = $request->input('tanggal');

    // Query kehadiran + relasi booking
    $kehadiran = Kehadiran::with('booking')
        ->when($tanggal, function ($query) use ($tanggal) {
            return $query->whereDate('tanggal_ci', $tanggal);
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
    $tanggal = $request->input('tanggal'); // Ambil tanggal dari request

    if ($tanggal) {
        $tanggal = \Carbon\Carbon::parse($tanggal)->toDateString(); // Format jadi string YYYY-MM-DD
    }

    return Excel::download(new RiwayatCheckinExport($tanggal), 'riwayat_checkin.xlsx');
}

public function exportPdf(Request $request)
{
    $tanggal = $request->input('tanggal');

    if ($tanggal) {
        $tanggal = \Carbon\Carbon::parse($tanggal)->toDateString();
    }

    $kehadiran = Kehadiran::with([
        'booking.ruangan', 
        'booking.peminjaman'
    ])->when($tanggal, function ($query) use ($tanggal) {
        return $query->whereDate('tanggal_ci', $tanggal);
    })->get();

  

    $pdf = PDF::loadView('exports.riwayat-checkin-pdf', compact('kehadiran'))
        ->setPaper('a4', 'landscape'); // **Set ke landscape**

    return $pdf->download('riwayat_checkin.pdf');
}



}
