<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Notification;
use App\Models\Kehadiran;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\KonfirmasiDutyOfficerNotification;
use App\Events\KonfirmasiCheckoutEvent;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RuanganBelumSiap;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\PeminjamanBarang;
class RuanganController extends Controller
{
    public function index(Request $request)
{
    $today = now()->toDateString(); // Tanggal tetap
    $today_ci = now()->toDateString(); // Tanggal check-in harus hari ini

    $query = Ruangan::with([
        'bookings' => function ($q) use ($today) {
            $q->where('status', 'Approved')
                ->whereDate('tanggal', $today)
                ->orderBy('waktu_mulai', 'asc');
        },
        'kehadiran' => function ($q) use ($today_ci) {
            $q->select('id', 'ruangan_id', 'duty_officer', 'status_konfirmasi', 'status', 'tanggal_ci')
                ->whereDate('tanggal_ci', $today_ci) // Pastikan hanya ambil data hari ini
                ->latest();
        },
        'bookings.peminjaman.barang'

    ]);

    // Filter berdasarkan lantai
    if ($request->filled('lantai')) {
        $query->where('lantai', $request->lantai);
    }

    // Filter berdasarkan status
    if ($request->filled('status')) {
        $status = $request->status;

        $query->where(function ($q) use ($status, $today, $today_ci) {
            if ($status == 'Kosong') {
                $q->whereDoesntHave('bookings', function ($subQuery) use ($today) {
                    $subQuery->whereDate('tanggal', $today);
                })->whereDoesntHave('kehadiran', function ($subQuery) use ($today_ci) {
                    $subQuery->where('status', 'Checked-in')
                        ->whereDate('tanggal_ci', $today_ci);
                });
            } elseif ($status == 'Sedang Digunakan') {
                $q->whereHas('kehadiran', function ($subQuery) use ($today_ci) {
                    $subQuery->where('status', 'Checked-in')
                        ->whereDate('tanggal_ci', $today_ci);
                });
            } elseif ($status == 'Dipesan') {
                $q->whereHas('bookings', function ($subQuery) use ($today) {
                    $subQuery->whereDate('tanggal', $today);
                })->whereDoesntHave('kehadiran', function ($subQuery) use ($today_ci) {
                    $subQuery->where('status', 'Checked-in')
                        ->whereDate('tanggal_ci', $today_ci);
                });
            }
        });
    }

    // Filter pencarian nama ruangan
    if ($request->filled('search')) {
        $query->where('nama_ruangan', 'LIKE', '%' . $request->search . '%');
    }

    $ruangan = $query->orderBy('lantai', 'asc')->get();

    foreach ($ruangan as $room) {
        // Simpan semua booking hari ini ke properti tambahan
        $room->all_bookings = $room->bookings;

        // Cek apakah ruangan sedang digunakan
        $sedangDigunakan = $room->kehadiran->contains('status', 'Checked-in');

        if ($sedangDigunakan) {
            $room->status = 'Sedang Digunakan';
        } elseif ($room->bookings->isNotEmpty()) {
            $room->status = 'Dipesan';
        } else {
            $room->status = 'Kosong';
        }

        // Ambil booking terakhir yang sudah check-out hari ini
        $checkoutTerakhir = Booking::where('ruangan_id', $room->id)
            ->whereHas('kehadiran', function ($q) use ($today_ci) {
                $q->where('status', 'checked-out')
                    ->whereDate('tanggal_ci', $today_ci);
            })
            ->orderByDesc('waktu_selesai')
            ->first();

        // Booking yang tampil di card: yang sedang digunakan atau yang selanjutnya setelah checkout
        $room->next_booking = $room->bookings
            ->firstWhere(fn($b) => $b->waktu_mulai >= optional($checkoutTerakhir)->waktu_selesai);

        // Jika tidak ada booking baru setelah acara terakhir, set status Kosong
        if (!$room->next_booking) {
            $room->status = 'Kosong';
        }

        // --- Bagian untuk menyimpan peminjaman barang yang terkait booking saat ini ---
        $room->current_peminjaman_barang = collect(); // default koleksi kosong
        $current_booking = $room->bookings->first(); // booking hari ini pertama
        if ($current_booking) {
            $room->current_peminjaman_barang = $current_booking->peminjaman;
        }
    }

    // Ruangan yang kosong ditaruh di belakang
    $ruangan = $ruangan->sortBy(function ($room) {
        return $room->status === 'Kosong' ? 1 : 0;
    })->values();

    // Ambil daftar lantai yang tersedia
    $lantaiOptions = Ruangan::select('lantai')->distinct()->orderBy('lantai')->pluck('lantai');

    return view('FO.roomList', compact('ruangan', 'lantaiOptions'));
}

    public function konfirmasi($id)
    {
        // Ambil kehadiran terbaru yang checked-in berdasarkan created_at
        $kehadiran = Kehadiran::where('ruangan_id', $id)
            ->where('status', 'checked-in')
            ->orderByDesc('created_at') // Gunakan created_at sebagai acuan terbaru
            ->first();

        if (!$kehadiran) {
            return back()->with('error', 'Tidak ada kehadiran aktif untuk ruangan ini.');
        }

        // Debug sebelum update
        \Log::info('Sebelum update:', ['status_konfirmasi' => $kehadiran->status_konfirmasi]);

        // Perbarui status konfirmasi menjadi 'siap_checkout'
        $kehadiran->status_konfirmasi = 'siap_checkout';
        $kehadiran->save();

        // Debug setelah update
        \Log::info('Setelah update:', ['status_konfirmasi' => $kehadiran->status_konfirmasi]);
        $ruangan = Ruangan::find($id);
// Ambil semua peminjaman barang untuk booking ini
$peminjamanList = PeminjamanBarang::where('kode_booking', $kehadiran->kode_booking)->get();

foreach ($peminjamanList as $peminjaman) {
    // Update status pengembalian
    $peminjaman->status_pengembalian = 'Sudah Dikembalikan';
    $peminjaman->save();

    // Kembalikan stok barang
    $barang = $peminjaman->barang;
    if ($barang) {
        $barang->jumlah += $peminjaman->jumlah; // tambahkan kembali saat dikembalikan
        $barang->save();
        \Log::info('Pengembalian Barang:', [
            'kode_booking' => $kehadiran->kode_booking,
            'barang_id'    => $barang->id,
            'jumlah_dikembalikan' => $peminjaman->jumlah,
            'stok_terbaru' => $barang->jumlah
        ]);
    }
    
}

        // Kirim notifikasi ke FO
        if ($ruangan) {
            // Kirim notifikasi ke FO
            $foUsers = User::where('role', 'front_office')->get();
            Notification::send($foUsers, new KonfirmasiDutyOfficerNotification($ruangan));
        }
        // Kirim Event ke Frontend
        broadcast(new KonfirmasiCheckoutEvent($kehadiran))->toOthers();

        return back()->with('success', 'Konfirmasi berhasil dikirim ke FO.');
    }
    public function markAsRead($id) {
        $notification = Auth::user()->notifications()->find($id);
    
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notifikasi dibaca']);
        }
    
        return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
    }
    public function belumSiap(Request $request, $id)
{
    $ruangan = Ruangan::findOrFail($id);

    if (auth()->user()->role === 'duty_officer' && auth()->user()->id == optional($ruangan->kehadiran->first())->duty_officer) {
        $ruangan->kehadiran()->update(['status_konfirmasi' => 'belum_siap_checkout']);

        // Ambil note dari form modal
        $note = $request->input('note');

        // Kirim notifikasi ke FO
        $foUsers = User::where('role', 'front_office')->get();
        Notification::send($foUsers, new RuanganBelumSiap($ruangan, $note));

        return back()->with('success', 'Status ruangan diperbarui menjadi Belum Siap Check-Out.');
    }

    return back()->with('error', 'User perlu Kembali ke ruangan');
}
    public function showImportForm()
    {
        return view('it.importRuangan');
    }
    
    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt,sql'
    ]);

    try {
        $extension = $request->file('file')->getClientOriginalExtension();

        if ($extension === 'sql') {
            // Jika file SQL, jalankan langsung isi perintah SQL-nya
            $sql = file_get_contents($request->file('file')->getRealPath());
            \DB::unprepared($sql);

            return back()->with('success', 'File SQL berhasil diimpor ke database!');
        } else {
            // CSV atau TXT
            $file = fopen($request->file('file'), 'r');
            $header = fgetcsv($file); // Lewati baris header

            while (($row = fgetcsv($file)) !== false) {
                Ruangan::create([
                    'nama_ruangan'   => $row[0],
                    'lantai'         => $row[1],
                    'tanggal'        => $row[2],
                    'waktu_mulai'    => $row[3],
                    'waktu_selesai'  => $row[4],
                    'status'         => $row[5],
                ]);
            }

            fclose($file);
            return back()->with('success', 'Data ruangan berhasil diimpor dari file CSV/TXT!');
        }
    } catch (\Exception $e) {
        Log::error('Import Gagal: ' . $e->getMessage());
        return back()->with('error', 'Terjadi kesalahan saat mengimpor data.');
    }
}

}
