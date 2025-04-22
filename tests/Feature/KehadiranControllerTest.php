<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Kehadiran;
use App\Models\PeminjamanBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;

class KehadiranControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_isi_data_berhasil()
    {
        // Membuat user dengan role Front Office
        $user = User::factory()->create([
            'role' => 'front_office', // Sesuaikan dengan nama role yang ada di tabel users
        ]);
    
        // Melakukan login sebagai Front Office
        $this->actingAs($user);
    
        // Buat booking palsu
        $booking = Booking::factory()->create([
            'kode_booking' => 'ABC123',
        ]);
    
        // Simulasi kirim data form check-in
        $response = $this->withoutMiddleware(['role:front_office'])->post(route('proses_checkin'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
            'kode_booking' => 'ABC123',
            'signatureData' => 'data:image/png;base64,dummy_signature_string',
        ]);
        
    
        // Cek redirect
        $response->assertRedirect(route('fo.bookingList'));
    
        // Cek kehadiran masuk database
        $this->assertDatabaseHas('kehadiran', [
            'nama_ci' => 'John Doe',  // Sesuaikan dengan kolom yang ada di tabel kehadiran
            'no_ci' => '081234567890',
            'kode_booking' => 'ABC123',
        ]);
    }
    
    /**
     * Test pengecekan booking yang valid.
     */
    public function testCheckBookingValid()
{
    $booking = Booking::factory()->create([
        'kode_booking' => 'BOOK123',
        'tanggal' => now()->toDateString(),
        'waktu_mulai' => now()->subMinutes(10)->format('H:i'), // sudah bisa check-in
        'waktu_selesai' => now()->addHour()->format('H:i'),
    ]);

    Session::start();
    $response = $this
        ->withSession(['_token' => csrf_token()])
        ->post(route('check'), [
            'id_booking' => 'BOOK123',
        ]);

    $response->assertRedirect(route('isi_data'));
    $response->assertSessionHas('success', 'Silakan isi data check-in Anda.');
}


    /**
     * Test pengecekan booking yang tidak valid.
     */
    public function testCheckBookingInvalid()
    {
        // Melakukan request pengecekan booking yang tidak valid
        $response = $this->post(route('check'), [
            'id_booking' => 'INVALID123',
        ]);

        // Memastikan bahwa response error dengan pesan yang sesuai
        $response->assertSessionHas('gagal', 'Kode booking tidak ditemukan atau tidak berlaku.');
    }

    /**
     * Test untuk memverifikasi check-in berhasil dari bookinglist.
     */
    public function testCheckinSuccessFromBookingList()
    {
        // Menyiapkan data booking yang valid
        $booking = Booking::factory()->create([
            'kode_booking' => 'BOOK123',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => now()->subMinutes(10)->format('H:i'), // sudah bisa check-in
            'waktu_selesai' => now()->addHour()->format('H:i'),
        ]);

        // Menyiapkan data kehadiran yang valid (gunakan factory Kehadiran)
        $kehadiran = Kehadiran::factory()->create([
            'kode_booking' => $booking->kode_booking,
            'nama_ci' => 'Pengunjung',
            'no_ci' => '1234567890',
            'ttd' => 'signature_data_example',
            'status' => 'Checked-in',
        ]);

        // Melakukan request untuk check-in dari halaman booking list
        $response = $this->post(route('proses_checkin'), [
            'kode_booking' => 'BOOK123',
            'name' => 'Pengunjung',
            'phone' => '089678394874',  // Ganti dengan nomor yang sesuai
            'signatureData' => 'signature_data_example',
        ]);

        // Memastikan bahwa check-in berhasil dan disimpan
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Check-in berhasil!');
        $this->assertDatabaseHas('kehadiran', [
            'kode_booking' => 'BOOK123',
            'nama_ci' => 'Pengunjung',
            'status' => 'Checked-in',
        ]);
    }

    /**
     * Test untuk memverifikasi check-in berhasil dari scan barcode.
     */
    public function testCheckinSuccessFromScanBarcode()
    {
        // Pastikan tidak ada middleware yang menghalangi
        $this->withoutMiddleware();
    
        // Siapkan data booking yang valid
        $booking = Booking::factory()->create([
            'kode_booking' => 'BOOK123',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => now()->subMinutes(10)->format('H:i'), // sudah bisa check-in
            'waktu_selesai' => now()->addHour()->format('H:i'),
        ]);
    
        // Melakukan request untuk check-in melalui scan barcode
        $response = $this->post(route('scan.barcode'), [
            'kode_booking' => 'BOOK123',
        ]);
    
        // Memastikan response redirect ke halaman isi data
        $response->assertRedirect(route('isi_data'));
    }
    /**
     * Test untuk peminjaman barang setelah check-in.
     */
 
public function testPeminjamanBarangSuccess()
{
    // Siapkan data booking yang valid
    $booking = Booking::factory()->create([
        'kode_booking' => 'BOOK123',
        'tanggal' => now()->toDateString(),
          'waktu_mulai' => now()->subMinutes(10)->format('H:i'), // sudah bisa check-in
            'waktu_selesai' => now()->addHour()->format('H:i'),
    ]);

   // Siapkan data peminjaman barang
PeminjamanBarang::create([
    'kode_booking' => 'BOOK123',
    'barang_id' => 1,
    'jumlah' => 2,
    'marketing' => 2,
    'created_by' => 4
]);
$user = User::factory()->create([
    'role' => 'front_office', // Sesuaikan dengan role yang ada di tabel users
]);

// Melakukan login sebagai front_office
$this->actingAs($user);

// Simulasikan data check-in
$response = $this->post(route('proses_checkin'), [
    'kode_booking' => 'BOOK123',
    'name' => 'John Doe',
    'phone' => '081234567890',
    'signatureData' => 'signature_data_example',
]);

// Pastikan redirect ke form peminjaman barang dengan kode_booking yang benar
$response->assertRedirect(route('form.peminjaman', ['kode_booking' => 'BOOK123']));

// Memastikan peminjaman barang ada di database dengan nama_barang yang benar
$this->assertDatabaseHas('peminjaman_barang', [
    'kode_booking' => 'BOOK123',
]);

}


    /**
     * Test untuk scan barcode dengan kode booking yang valid.
     */
    public function testScanBarcodeValid()
    {
        $this->withoutMiddleware('auth'); // tergantung kebutuhan

        // Menyiapkan data booking yang valid
        $booking = Booking::factory()->create([
            'kode_booking' => 'BOOK123',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => now()->subMinutes(10)->format('H:i'), // sudah bisa check-in
        'waktu_selesai' => now()->addHour()->format('H:i'),
        ]);

        // Melakukan request scan barcode dengan kode booking yang valid
        $response = $this->post(route('scan.barcode'), [
            'kode_booking' => 'BOOK123',
        ]);

        // Memastikan bahwa response redirect ke halaman isi data
        $response->assertRedirect(route('isi_data'));
    }

    /**
     * Test untuk scan barcode dengan kode booking tidak valid.
     */
    public function testScanBarcodeInvalid()
    {
        // Melakukan request scan barcode dengan kode booking yang tidak valid
        $response = $this->post(route('scan.barcode'), [
            'kode_booking' => 'INVALID123',
        ]);

        // Memastikan response error dengan pesan yang sesuai
        $response->assertStatus(404);
        $response->assertJson(['status' => 'error', 'message' => 'Kode booking tidak ditemukan atau tidak berlaku untuk hari ini.']);
    }
}
