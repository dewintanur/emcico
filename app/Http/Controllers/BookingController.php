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
        $kehadiran = Kehadiran::with('booking')->get(); // Ambil data kehadiran beserta relasi booking

        $pdf = Pdf::loadView('FO.exportBookingPdf', compact('kehadiran'));
        return $pdf->download('kehadiran_list.pdf');
    }

    public function exportCSV()
    {
        $kehadiran = Kehadiran::with('booking')->get();

        // Nama file CSV
        $fileName = "KehadiranCheck.csv";

        // Header untuk download CSV
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Buka output file
        $handle = fopen('php://output', 'w');

        // Tambahkan UTF-8 BOM agar Excel baca encoding dengan benar
        fprintf($handle, "\xEF\xBB\xBF");

        // Buat header kolom CSV
        fputcsv($handle, [
            'Kode Booking',
            'Nama Event',
            'Nama Organisasi',
            'Nama PIC',
            'Nama Check-in',
            'No Check-in',
            'Tanggal',
            'Status'
        ], ';'); // Pakai `;` sebagai pemisah

        // Loop data dan tulis ke CSV
        foreach ($kehadiran as $data) {
            fputcsv($handle, [
                $data->booking->kode_booking ?? '',
                $data->booking->nama_event ?? '',
                $data->booking->nama_organisasi ?? '',
                $data->booking->nama_pic ?? '',
                $data->nama_ci ?? '',
                "'" . $data->no_ci ?? '',   // Tambah kutip untuk nomor
                "'" . $data->booking->tanggal ?? '', // Tambah kutip untuk tangga
                $data->status ?? ''
            ], ';'); // Pakai `;` sebagai pemisah
        }

        fclose($handle);

        return response()->stream(function () use ($handle) {}, 200, $headers);
    }


}
