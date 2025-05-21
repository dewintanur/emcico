<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kehadiran;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DB::table('booking')
            ->select(
                'booking.*',
                'kehadiran.status as checkin_status',
                'kehadiran.nama_ci',
                'kehadiran.no_ci',
                'kehadiran.tanggal_ci',
                'kehadiran.status_konfirmasi',
                DB::raw('CASE WHEN kehadiran.tanggal_ci IS NOT NULL THEN kehadiran.duty_officer ELSE NULL END as duty_officer'),
                'ruangan.nama_ruangan',
                'ruangan.lantai'
            )
            ->leftJoin('ruangan', function ($join) {
                $join->on('booking.ruangan_id', '=', 'ruangan.id')
                    ->on('booking.lantai', '=', 'ruangan.lantai');
            })
            ->leftJoin('kehadiran', function ($join) {
                $join->on('booking.kode_booking', '=', 'kehadiran.kode_booking')
                    ->whereDate('kehadiran.tanggal_ci', now());
            })
            ->whereDate('booking.tanggal', today()) // ✅ Hanya ambil booking hari ini
            ->orderBy('booking.waktu_mulai', 'asc')
            ->orderBy('ruangan.lantai', 'asc');
    
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'Approved') {
                $query->whereNull('kehadiran.status');
            } else {
                $query->where('kehadiran.status', $request->status);
            }
        }
    
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('booking.kode_booking', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('booking.nama_event', 'LIKE', '%' . $request->search . '%');
            });
        }
    
        $perPage = 10;
        $bookings = $query->paginate($perPage);
    
        $dutyOfficers = User::where('role', 'duty_officer')->get();
    
        return view('FO.bookingList', compact('bookings', 'dutyOfficers'));
    }
    
    public function read($id, Request $request)
    {
        try {
            $user = auth()->user();
    
            $notification = $user->notifications()->find($id);
            if ($notification) {
                $notification->markAsRead();
    
                return response()->json(['success' => true, 'message' => 'Notification marked as read']);
            }
    
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        } catch (\Exception $e) {
            Log::error('Notification read error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

public function exportPDF()
{
    $today = Carbon::today()->toDateString();

    $kehadiran = Kehadiran::whereDate('created_at', $today)
                    ->with(['booking', 'fo'])
                    ->get();

    foreach ($kehadiran as $item) {
        $status = strtolower(str_replace([' ', '-'], '', $item->status)); // contoh: "checkedin" atau "checkedout"

        if ($status === 'checkedout') {
            $item->tanggal_co_display = $item->updated_at ? $item->updated_at->format('Y-m-d H:i') : '-';
        } else {
            $item->tanggal_co_display = 'Belum Checkout';
        }
    }

    $pdf = Pdf::loadView('FO.exportBookingPdf', compact('kehadiran'));
    return $pdf->download("kehadiran_{$today}.pdf");
}




   public function exportCSV()
{
    $today = Carbon::today()->toDateString();

    $kehadiran = Kehadiran::whereDate('created_at', $today)
                    ->with(['booking', 'fo'])
                    ->get();

    $fileName = "kehadiran_{$today}.csv";

    $headers = [
        "Content-type" => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $handle = fopen('php://output', 'w');
    fprintf($handle, "\xEF\xBB\xBF");

    // Tambah header kolom
    fputcsv($handle, [
        'Kode Booking',
        'Nama Event',
        'Nama Organisasi',
        'Nama PIC',
        'Nama Check-in',
        'No Check-in',
        'Tanggal Check-in',
        'Tanggal Check-out',
        'Front Office',
        'Status'
    ], ';');

   foreach ($kehadiran as $data) {
$status = strtolower(str_replace([' ', '-'], '', $data->status)); // jadi "checkedin" atau "checkedout"

$tanggal_co = ($status === 'checkedout' && $data->updated_at) ? $data->updated_at->format('Y-m-d H:i') : 'Belum checkout';

    fputcsv($handle, [
        $data->booking->kode_booking ?? '',
        $data->booking->nama_event ?? '',
        $data->booking->nama_organisasi ?? '',
        $data->booking->nama_pic ?? '',
        $data->nama_ci ?? '',
        "'" . ($data->no_ci ?? ''),
        $data->created_at ? $data->created_at->format('Y-m-d H:i') : '',
        $tanggal_co,
        $data->fo_id ?? 'belum checkout',
        $data->status ?? ''
    ], ';');
}


    fclose($handle);

    return response()->stream(function () use ($handle) {}, 200, $headers);
}


}
