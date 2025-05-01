<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PeminjamanBarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CheckinHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ListBarangController;

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\Kehadiran;

// **💻 Halaman Awal**
Route::get('/', function () {
    return view('user/input_kode');
})->name('inputkode.show');

Route::get('/scan-barcode', function () {
    return view('barcode.scan');
})->name('barcode.scan');
Route::post('/scan-barcode', [KehadiranController::class, 'scanBarcode'])->name('scan.barcode');

// **🔍 Pengecekan Booking**
Route::post('/check', [KehadiranController::class, 'checkBooking'])->name('check');
Route::get('/checkin/{kode_booking}', [KehadiranController::class, 'checkin'])->name('checkin');
Route::get('/isi_data', [KehadiranController::class, 'isi_data'])->name('isi_data');
Route::post('/proses_checkin', [KehadiranController::class, 'store'])->name('proses_checkin');

// **📋 Form Peminjaman Barang**
Route::get('/form-peminjaman/{kode_booking}', [PeminjamanBarangController::class, 'index'])->name('form.peminjaman');
Route::post('/simpan-setuju-peminjaman', [KehadiranController::class, 'simpanSetujuPeminjaman'])->name('simpan.setuju.peminjaman');

// **🔑 Login & Logout**
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// **📂 Grup Route yang Butuh Autentikasi**
Route::middleware(['auth', 'maintainRole'])->group(function () {

    // **👤 Profile (Bisa Diakses Semua Pengguna)**
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // **🟢 Front Office**
    Route::middleware('role:front_office')->group(function () {
        Route::get('/booking-list', [BookingController::class, 'index'])->name('fo.bookingList');
        Route::post('/assign-dutyofficer/{id}', [KehadiranController::class, 'assignDutyOfficer'])->name('assign.dutyofficer');
        Route::get('/export/pdf', [BookingController::class, 'exportPDF'])->name('booking.export.pdf');
        Route::get('/export/csv', [BookingController::class, 'exportCSV'])->name('booking.export.csv');
        Route::post('/checkout', [KehadiranController::class, 'checkout'])->name('checkout');
    });

    // **🟠 Duty Officer**
    Route::middleware('role:duty_officer')->group(function () {
        Route::post('/ruangan/{id}/konfirmasi', [RuanganController::class, 'konfirmasi'])->name('ruangan.konfirmasi');
        Route::post('/ruangan/{id}/belum-siap', [RuanganController::class, 'belumSiap'])->name('ruangan.belum_siap');

    });

    // **🔵 Marketing**
    Route::middleware('role:marketing,admin,it')->group(function () {
        Route::get('/marketing/peminjaman-list', [PeminjamanBarangController::class, 'listPeminjaman'])->name('marketing.peminjaman');
        Route::post('/peminjaman/store', [PeminjamanBarangController::class, 'store'])->name('peminjaman.store');
        Route::delete('/peminjaman/destroy/{id}', [PeminjamanBarangController::class, 'destroy'])->name('peminjaman.destroy');
        Route::get('/marketing/riwayat', [PeminjamanBarangController::class, 'historyPeminjaman'])->name('marketing.riwayat');
    });

    // **🟣 Produksi**
    Route::middleware('role:produksi,it')->group(function () {
        Route::get('/produksi/peminjaman', [PeminjamanBarangController::class, 'produksi'])->name('produksi.peminjaman');
    });

    // **🔴 IT Admin (Full Akses)**
    Route::middleware('role:it,admin')->group(function () { // Tambahkan admin di sini
        Route::get('/it', [UserController::class, 'index'])->name('users.index');
        Route::get('/it/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/generate-barcode', [BarcodeController::class, 'index'])->name('barcode.index');
        Route::post('/generate-barcode', [BarcodeController::class, 'generateAndSend'])->name('barcode.send');
        Route::get('booking/import', [ImportController::class, 'showImportForm'])->name('booking.import');
        Route::post('booking/import', [ImportController::class, 'import']);
        Route::get('/import-ruangan', [RuanganController::class, 'showImportForm'])->name('ruangan.import.form');
        Route::post('/import-ruangan', [RuanganController::class, 'import'])->name('ruangan.import');

        Route::get('/list_barang', [ListBarangController::class, 'index'])->name('list_barang.index');
        Route::get('/list_barang/create', [ListBarangController::class, 'create'])->name('list_barang.create');
        Route::post('/list_barang/store', [ListBarangController::class, 'store'])->name('list_barang.store');
        Route::delete('/list_barang/{id}', [ListBarangController::class, 'destroy'])->name('list_barang.destroy');
        Route::resource('list_barang', App\Http\Controllers\ListBarangController::class);

    });

    // **👁️‍🗨️ IT Hanya Bisa Melihat (Read-Only)**
    Route::middleware('readonly')->group(function () {
        Route::middleware('role:front_office,it,admin')->group(function () {
            Route::get('/booking-list', [BookingController::class, 'index'])->name('fo.bookingList');
        });

        Route::middleware('role:front_office,duty_officer,it,admin')->group(function () {
            Route::get('/ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
        });

        Route::middleware('role:marketing,it,admin')->group(function () {
            Route::get('/marketing/peminjaman-list', [PeminjamanBarangController::class, 'listPeminjaman'])->name('marketing.peminjaman');
        });

        Route::middleware('role:produksi,it,admin')->group(function () {
            Route::get('/produksi/peminjaman', [PeminjamanBarangController::class, 'produksi'])->name('produksi.peminjaman');
        });
    });

    // **📢 Notifikasi**
    Route::get('/notifikasi/read/{id}', function ($id) {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return back();
    })->name('notifikasi.read');
    Route::post('/notifikasi/{id}/read', [BookingController::class, 'read'])->name('notifikasi.read');
    Route::get('/booking-status', function () {
        $today = Carbon::now()->toDateString();
        $kehadiran = Kehadiran::select('kode_booking', 'status', 'status_konfirmasi')
            ->whereDate('tanggal_ci', $today)
            ->orderByDesc('updated_at')
            ->get();
        return response()->json(['data' => $kehadiran]);
    })->name('booking.status');
    Route::get('/notifications/unread', function () {
        $user = auth()->user();
        $unread = $user->unreadNotifications ?? collect();
        return response()->json([
            'ada_notifikasi' => $unread->count() > 0,
            'jumlah' => $unread->count()
        ]);
    });
    
    // **📜 Riwayat Check-in (Bisa Diakses Semua)**
    Route::get('/riwayat-checkin', [CheckinHistoryController::class, 'index'])->name('riwayat.checkin');
    Route::get('/riwayat-checkin/{kode_booking}', [CheckinHistoryController::class, 'show'])->name('riwayat.checkin.detail');
    Route::get('/riwayat-checkin/export/excel', [CheckinHistoryController::class, 'exportExcel'])->name('riwayat.checkin.export.excel');
    Route::get('/riwayat-checkin/export/pdf', [CheckinHistoryController::class, 'exportPDF'])->name('riwayat.checkin.export.pdf');
    Route::post('/notifications/{id}/read', [BookingController::class, 'read'])->name('notifications.read');

});
