<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use App\Models\PeminjamanBarang;
use App\Notifications\DutyOfficerAssigned;
use App\Models\User;
use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KehadiranController extends Controller
{
    /**
     * Cek kode booking sebelum check-in.dari halaman user
     */
    public function checkBooking(Request $request)
    {
        $request->validate([
            'id_booking' => 'required'
        ]);
    
        $kodeBooking = $request->id_booking;
        $booking = Booking::where('kode_booking', $kodeBooking)
            ->whereDate('tanggal', today()) // ✅ Pastikan booking hanya untuk hari ini
            ->first();
    
        if (!$booking) {
            return back()->with('gagal', 'Kode booking tidak ditemukan atau tidak berlaku.');
        }
    
        $waktuSekarang = now()->format('H:i');
        $jamMulai = $booking->waktu_mulai;
        $jamSelesai = $booking->waktu_selesai;
    
        if ($waktuSekarang < $jamMulai) {
            return back()->with('gagal', "Check-in hanya bisa dilakukan setelah pukul $jamMulai.");
        }
    
        if ($waktuSekarang > $jamSelesai) {
            return back()->with('gagal', "Check-in sudah ditutup. Batas check-in adalah pukul $jamSelesai.");
        }
    
        $sudahCheckin = Kehadiran::where('kode_booking', $kodeBooking)
            ->whereDate('tanggal_ci', today())
            ->exists();
    
        if ($sudahCheckin) {
            return back()->with('gagal', 'Anda sudah melakukan check-in hari ini.');
        }
    
        return redirect()->route('isi_data')->with([
            'success' => 'Silakan isi data check-in Anda.',
            'booking' => $booking
        ]);
    }
    public function checkin($kodeBooking) 
{
    $booking = Booking::where('kode_booking', $kodeBooking)
        ->whereDate('tanggal', today())
        ->first();

    if (!$booking) {
        Log::warning('Kode booking tidak ditemukan atau tidak berlaku.', [
            'kode_booking' => $kodeBooking
        ]);
        return back()->with('gagal', 'Kode booking tidak ditemukan atau sudah kadaluarsa.');
    }

    $waktuSekarang = now()->format('H:i');
    $jamMulai = $booking->waktu_mulai;
    $jamSelesai = $booking->waktu_selesai;

    // Validasi waktu check-in (jam mulai dan selesai)
    if ($waktuSekarang < $jamMulai) {
        return back()->with('gagal', "Check-in hanya bisa dilakukan setelah pukul $jamMulai.");
    }

    if ($waktuSekarang > $jamSelesai) {
        return back()->with('gagal', "Check-in sudah ditutup. Batas check-in adalah pukul $jamSelesai.");
    }

    // Cek apakah sudah check-in
    $sudahCheckin = Kehadiran::where('kode_booking', $kodeBooking)
        ->whereDate('tanggal_ci', today())
        ->exists();

    if ($sudahCheckin) {
        return back()->with('gagal', 'Sudah dilakukan check-in hari ini.');
    }

    // Tambahan validasi: hanya boleh check-in jika booking sebelumnya sudah check-out
    $bookingSebelumnya = Booking::join('kehadiran', 'booking.kode_booking', '=', 'kehadiran.kode_booking')
        ->where('booking.tanggal', $booking->tanggal)
        ->where('booking.ruangan_id', $booking->ruangan_id)
        ->where('booking.lantai', $booking->lantai)
        ->where('booking.waktu_selesai', '<=', $booking->waktu_mulai)
        ->where('kehadiran.status', 'checked-out')
        ->orderBy('booking.waktu_selesai', 'desc')
        ->first();

    if (!$bookingSebelumnya && $waktuSekarang < $jamMulai) {
        return back()->with('gagal', 'Check-in hanya bisa dilakukan mulai pukul ' . $jamMulai . ', atau setelah booking sebelumnya selesai dan sudah check-out.');
    }

    // Log aktivitas
    Log::info('Kode booking ditemukan dan belum check-in hari ini.', ['booking' => $booking]);
    LogAktivitas::create([
        'user_id' => Auth::check() ? Auth::id() : null,
        'aktivitas' => 'Melakukan check-in',
        'waktu' => Carbon::now(),
    ]);

    return redirect()->route('isi_data')->with([
        'success' => 'Silakan isi data check-in Anda.',
        'booking' => $booking
    ]);
}

    
        // Fungsi untuk menangani hasil scan barcode
        public function scanBarcode(Request $request)
        {
            $kodeBooking = $request->kode_booking;
        
            // Ambil booking untuk hari ini
            $booking = Booking::where('kode_booking', $kodeBooking)
                ->whereDate('tanggal', today())
                ->first();
        
            if (!$booking) {
                return response()->json(['status' => 'error', 'message' => 'Kode booking tidak ditemukan atau tidak berlaku untuk hari ini.'], 404);
            }
        
            $waktuSekarang = now()->format('H:i');
            if ($waktuSekarang < $booking->waktu_mulai) {
                return response()->json(['status' => 'error', 'message' => "Check-in hanya bisa dilakukan setelah pukul {$booking->waktu_mulai}."], 403);
            }
        
            if ($waktuSekarang > $booking->waktu_selesai) {
                return response()->json(['status' => 'error', 'message' => "Check-in sudah ditutup. Batas check-in adalah pukul {$booking->waktu_selesai}."], 403);
            }
        
            $sudahCheckin = Kehadiran::where('kode_booking', $kodeBooking)
                ->whereDate('tanggal_ci', today())
                ->exists();
        
            if ($sudahCheckin) {
                return response()->json(['status' => 'error', 'message' => 'Kode booking ini sudah melakukan check-in.'], 400);
            }
        
            // Simpan ke session dan arahkan ke isi_data (lewat frontend redirect)
            session([
                'booking' => $booking,
                'success' => 'Silakan isi data check-in Anda.'
            ]);
        
            return redirect()->route('isi_data')->with([
                'success' => 'Silakan isi data check-in Anda.',
                'booking' => $booking
            ]);
        }
        

    /**
     * Menampilkan halaman untuk mengisi data check-in.
     */
    public function isi_data(Request $request)
    {
        // Ambil data dari session
        $booking = session('booking');

        // Jika tidak ada data booking di session, redirect kembali
        if (!$booking) {
            return redirect('/')->with('gagal', 'Silakan masukkan kode booking terlebih dahulu.');
        }

        return view('user.isi_data', compact('booking'));
    }

    /**
     * Simpan data check-in ke database dan arahkan ke form peminjaman barang jika diperlukan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_booking' => 'required|exists:booking,kode_booking',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:12',
            'signatureData' => 'required',
        ]);

        $booking = Booking::where('kode_booking', $request->kode_booking)->first();

        // Periksa apakah ada peminjaman barang terkait dengan booking ini
        $peminjaman = PeminjamanBarang::where('kode_booking', $booking->kode_booking)->exists();

        if ($peminjaman) {
            // Jika ada peminjaman, simpan data sementara ke session dan arahkan ke form peminjaman barang
            session([
                'checkin_data' => [
                    'kode_booking' => $request->kode_booking,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'signature' => $request->signatureData
                ]
            ]);
            session()->reflash();

            return redirect()->route('form.peminjaman', ['kode_booking' => $booking->kode_booking])
                ->with('checkin_data', [
                    'kode_booking' => $request->kode_booking,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'signatureData' => $request->signatureData,
                    'ruangan_id' => $booking->ruangan_id,
                    'lantai' => $booking->lantai,

                    // Tambahkan ini!
                ]);
        } else {
            // Jika tidak ada peminjaman, langsung simpan ke tabel kehadiran
            Kehadiran::create([
                'kode_booking' => $request->kode_booking,
                'nama_ci' => $request->name, // Sesuai dengan kolom tabel
                'no_ci' => $request->phone,  // Sesuai dengan kolom tabel
                'tanggal_ci' => now(), // Simpan tanggal saat ini
                'ruangan_id' => $booking->ruangan_id, // Ambil dari booking
                'lantai' => $booking->lantai, // Ambil dari booking
                'ttd' => $request->signatureData, // Simpan tanda tangan
                'duty_officer' => null, // Bisa diisi nanti
                'status' => 'Checked-in', // Status default
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (auth()->check() && auth()->user()->role == 'front_office') {
                return redirect()->route('fo.bookingList')->with('success', 'Check-in berhasil!');
            }


            return redirect('/')->with('success', 'Check-in berhasil!');
        }
    }

    /**
     * Simpan data check-in setelah setuju peminjaman barang.
     */
    public function simpanSetujuPeminjaman(Request $request)
    {
        // Ambil data check-in dari session
        $checkinData = session('checkin_data');

        if (!$checkinData) {
            return redirect('/')->with('gagal', 'Data check-in tidak ditemukan, silakan ulangi.');
        }

        // Cek apakah FO memilih Duty Officer (dari form FO)
        $dutyOfficer = $request->has('duty_officer') ? $request->input('duty_officer') : null;

        // Simpan data ke database
        Kehadiran::create([
            'kode_booking' => $checkinData['kode_booking'],
            'nama_ci' => $checkinData['name'],
            'no_ci' => $checkinData['phone'],
            'tanggal_ci' => now(),
            'ruangan_id' => $checkinData['ruangan_id'],
            'lantai' => $checkinData['lantai'],
            'ttd' => $checkinData['signatureData'],
            'duty_officer' => $dutyOfficer, // ✅ Jika FO pilih Duty Officer, simpan
            'status' => 'Checked-in',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Hapus data session setelah disimpan
        session()->forget('checkin_data');

        // Redirect sesuai role
        if (auth()->check() && auth()->user()->role === 'front_office') {
            return redirect()->route('fo.bookingList')->with('success', 'Check-in berhasil!');
        } else {
            return redirect('/')->with('success', 'Check-in dan peminjaman berhasil disetujui!');
        }
    }


    public function assignDutyOfficer(Request $request, $id)
    {
        Log::info("Assign Duty Officer dipanggil untuk booking ID: $id", ['request_data' => $request->all()]);
    
        $request->validate([
            'duty_officer' => 'required|exists:users,id' // Pastikan duty officer valid
        ]);
    
        // Cari booking berdasarkan ID
        $booking = Booking::find($id);
        if (!$booking) {
            Log::error("Booking tidak ditemukan untuk ID: $id");
            return back()->with('error', 'Booking tidak ditemukan.');
        }
    
        Log::info("Booking ditemukan", ['kode_booking' => $booking->kode_booking]);
    
        // ✅ Cari kehadiran berdasarkan kode_booking & tanggal terbaru
        $kehadiran = Kehadiran::where('kode_booking', $booking->kode_booking)
            ->whereDate('tanggal_ci', now()) // 📌 Ambil yang terbaru berdasarkan tanggal check-in
            ->first();
    
        if (!$kehadiran) {
            Log::error("Data kehadiran tidak ditemukan untuk kode_booking: {$booking->kode_booking} pada tanggal " . now()->toDateString());
            return back()->with('error', 'Data kehadiran untuk hari ini belum ada.');
        }
    
        Log::info("Data kehadiran ditemukan", ['kehadiran' => $kehadiran]);
    
        // ✅ Perbarui duty officer
        $kehadiran->duty_officer = $request->duty_officer;
        $kehadiran->save();
    
        Log::info("Duty Officer berhasil diperbarui", ['duty_officer' => $kehadiran->duty_officer]);
    
        // Kirim notifikasi ke Duty Officer yang dipilih (opsional)
        $dutyOfficer = User::find($request->duty_officer);
        if ($dutyOfficer) {
            Log::info("Mengirim notifikasi ke Duty Officer", ['duty_officer' => $dutyOfficer->id]);
            $dutyOfficer->notify(new DutyOfficerAssigned($booking));
        } else {
            Log::warning("Duty Officer dengan ID {$request->duty_officer} tidak ditemukan.");
        }
    
        return back()->with('success', 'Duty Officer berhasil diperbarui dan diberi notifikasi!');
    }
    
    public function checkout(Request $request)
    {
        // Cari data di tabel kehadiran berdasarkan kode_booking
        $kehadiran = Kehadiran::where('kode_booking', $request->kode_booking)
                    ->whereDate('tanggal_ci', now()) // Pastikan hanya checkout untuk check-in hari ini
                    ->first();
    
        if (!$kehadiran) {
            return redirect()->back()->with('error', 'Data check-in tidak ditemukan.');
        }
    
        // Simpan FO yang melakukan checkout
        if (auth()->check() && auth()->user()->role === 'front_office') {
            $kehadiran->fo_id = auth()->id();
        }
    
        // Ubah status check-out
        $kehadiran->status = 'Checked-out';
        // $kehadiran->tanggal_co = now();
        $kehadiran->save();
    
        return redirect()->back()->with('success', 'Checkout berhasil!');
    }
    

}
