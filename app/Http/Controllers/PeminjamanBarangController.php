<?php

namespace App\Http\Controllers;

use App\Models\PeminjamanBarang;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\ListBarang;
use Illuminate\Support\Facades\Auth; // Tambahkan ini jika belum ada
use Illuminate\Support\Facades\Log; // Tambahkan ini di atas
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanBarangController extends Controller
{

    public function index($kode_booking)
{
    $booking = Booking::where('kode_booking', $kode_booking)->first();
    $peminjaman = PeminjamanBarang::with(['booking', 'barang'])
        ->where('kode_booking', $kode_booking)
        ->get();

    $checkinData = session('checkin_data');
    \Log::info('Data Check-in:', $checkinData ?? []);

    session()->keep(['checkin_data']);

    if ($peminjaman->isEmpty()) {
        return redirect()->route('proses_checkin')->with('success', 'Check-in berhasil tanpa peminjaman barang.');
    }
    \Log::info('Data Peminjaman:', $peminjaman->toArray());

    return view('showPeminjaman', compact('booking', 'peminjaman', 'kode_booking', 'checkinData'));
}

    public function historyPeminjaman()
    {
        $riwayat = DB::table('peminjaman_barang')
            ->leftJoin('users as creator', 'peminjaman_barang.created_by', '=', 'creator.id')
            ->leftJoin('users as deleter', 'peminjaman_barang.deleted_by', '=', 'deleter.id')
            ->leftJoin('list_barang', 'peminjaman_barang.barang_id', '=', 'list_barang.id')
            ->select(
                'peminjaman_barang.*',
                'list_barang.nama_barang',
                'creator.nama as created_by_user',
                'deleter.nama as deleted_by_user'
            )
            ->orderBy('peminjaman_barang.created_at', 'desc') // Urutkan berdasarkan waktu dibuat
            ->paginate(10); // Menampilkan 10 data per halaman

        return view('marketing.riwayat', compact('riwayat'));
    }

    public function listPeminjaman(Request $request)
{
    $query = Booking::with(['ruangan', 'peminjaman.barang'])
        ->whereHas('peminjaman'); // Hanya booking yang memiliki peminjaman barang

    // Filter berdasarkan tanggal
    if ($request->has('date') && !empty($request->date)) {
        $query->whereDate('tanggal', $request->date); // pakai kolom tanggal dari tabel booking
    } else {
        // Default: tampilkan data untuk esok hari
        $query->whereDate('tanggal', Carbon::tomorrow());
    }
    

    // Pencarian berdasarkan kode booking atau nama barang
    if ($request->has('search') && !empty($request->search)) {
        $query->where(function ($q) use ($request) {
            $q->where('kode_booking', 'like', '%' . $request->search . '%')
              ->orWhere('nama_event', 'like', '%' . $request->search . '%') // Tambah pencarian nama event
              ->orWhereHas('ruangan', function ($ruanganQuery) use ($request) {
                  $ruanganQuery->where('nama_ruangan', 'like', '%' . $request->search . '%');
              })
              ->orWhereHas('peminjaman.barang', function ($barangQuery) use ($request) {
                  $barangQuery->where('nama_barang', 'like', '%' . $request->search . '%');
              })
              ->orWhereHas('peminjaman', function ($peminjamanQuery) use ($request) {
                  $peminjamanQuery->where('marketing', 'like', '%' . $request->search . '%')
                                  ->orWhere('created_by', 'like', '%' . $request->search . '%');
              });
        });
    }
    
    

    $peminjamanList = $query->paginate(10); // Pagination agar tidak terlalu banyak data dalam satu halaman
    $listBarang = ListBarang::where('jumlah', '>', 0)->get();

    return view('marketing.marketingList', compact('peminjamanList', 'listBarang'));
}

public function store(Request $request)
{
    try {
        if (!Auth::check()) {
            return response()->json(['error' => 'Anda harus login untuk meminjam barang.'], 401);
        }

        $request->validate([
            'kode_booking' => 'required|exists:booking,kode_booking',
            'barang_id' => 'required|exists:list_barang,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Cek apakah barang ini sudah dipinjam sebelumnya di booking yang sama
        $peminjaman = PeminjamanBarang::where('kode_booking', $request->kode_booking)
            ->where('barang_id', $request->barang_id)
            ->first();

        if ($peminjaman) {
            // Jika sudah ada, tambahkan jumlahnya
            $peminjaman->jumlah += $request->jumlah;
            $peminjaman->save();
        } else {
            // Jika belum ada, buat baru
            $peminjaman = PeminjamanBarang::create([
                'kode_booking' => $request->kode_booking,
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'marketing' => Auth::user()->nama,
                'created_by' => Auth::id(),
            ]);
        }

        // Kurangi stok barang
        $barang = ListBarang::find($request->barang_id);
        if ($barang) {
            $barang->jumlah -= $request->jumlah;
            $barang->save();
        }

        return response()->json(['success' => 'Barang berhasil ditambahkan!', 'peminjaman_id' => $peminjaman->id, 'jumlah' => $peminjaman->jumlah]);

    } catch (\Exception $e) {
        Log::error('Gagal menyimpan peminjaman: ' . $e->getMessage());
        return response()->json(['error' => 'Terjadi kesalahan saat menyimpan barang.', 'message' => $e->getMessage()], 500);
    }
}



    public function destroy($id)
    {
        $peminjaman = DB::table('peminjaman_barang')->where('id', $id)->first();

        if (!$peminjaman) {
            return response()->json(['error' => 'Data tidak ditemukan!'], 404);
        }

        // Kembalikan stok barang
        DB::table('list_barang')
            ->where('id', $peminjaman->barang_id)
            ->increment('jumlah', $peminjaman->jumlah);

        // Update deleted_by dan deleted_at daripada menghapus langsung
        DB::table('peminjaman_barang')
            ->where('id', $id)
            ->update([
                'deleted_by' => Auth::id(),
                'deleted_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }


    public function produksi(Request $request)
    {
        $filterDate = $request->input('date');
        $search = $request->input('search');
    
        $query = Booking::with(['ruangan', 'peminjaman']); // tambahkan relasi peminjaman jika diperlukan
    
        if ($filterDate) {
            $query->whereDate('tanggal', $filterDate);
        } else {
            // Tampilkan data hari ini dan besok jika tidak ada filter
            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();
    
            $query->whereBetween('tanggal', [$today, $tomorrow]);
        }
    
        if ($search) {
            $query->where('nama_event', 'LIKE', "%$search%");
        }
    
        $bookings = $query->orderBy('tanggal', 'asc')->get();
    
        return view('produksi.produksiList', compact('bookings'));
    }

}
