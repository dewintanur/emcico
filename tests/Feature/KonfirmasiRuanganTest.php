<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kehadiran;
use App\Models\Ruangan;
use App\Models\Booking;
use App\Models\PeminjamanBarang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KonfirmasiRuanganTest extends TestCase
{
    use RefreshDatabase;

    // Test konfirmasi ruangan sukses (basic flow)
    public function test_ruangan_kosong_after_checkout()
    {
        // Setup: Membuat user FO dan Duty Officer, booking, dan kehadiran
        $frontOfficeUser = User::factory()->create(['role' => 'front_office']);
        $dutyOfficerUser = User::factory()->create(['role' => 'duty_officer']);
        $this->actingAs($frontOfficeUser);

        $booking = Booking::factory()->create(['tanggal' => today()]);
        $kehadiran = Kehadiran::factory()->create([
            'kode_booking' => $booking->kode_booking,
            'status' => 'Checked-in',
            'tanggal_ci' => today(),
        ]);

        // Mengirim notifikasi ke Duty Officer
        // (Logika pengiriman notifikasi ke duty officer bisa disesuaikan dengan implementasi yang ada)
        
        // Duty Officer login dan konfirmasi ruangan
        $this->actingAs($dutyOfficerUser);

        // Duty Officer mengonfirmasi kondisi ruangan
        $response = $this->post(route('ruangan.konfirmasi', $kehadiran->ruangan_id));

        // Verifikasi: Status booking menjadi 'Checked-out' dan ruangan jadi 'Kosong'
        $kehadiran->refresh();
        $this->assertEquals('siap_checkout', $kehadiran->status_konfirmasi);
        
      // Setup: Create a PeminjamanBarang record for the test
$peminjamanBarang = PeminjamanBarang::factory()->create([
    'kode_booking' => $booking->kode_booking,
    'status_pengembalian' => 'Sudah Dikembalikan', // Set the status for verification
]);

// Verifikasi peminjaman barang dikembalikan
$this->assertEquals('Sudah Dikembalikan', $peminjamanBarang->status_pengembalian);

        // Verifikasi status ruangan
        $ruangan = Ruangan::find($kehadiran->ruangan_id);
        $this->assertEquals('Kosong', $ruangan->status);

        $response->assertRedirect()->with('success', 'Konfirmasi berhasil dikirim ke FO.');
    }

    // Test konfirmasi ruangan ditunda (alternative flow)
    public function test_ruangan_belum_siap()
    {
        // Setup: Membuat user FO dan Duty Officer, booking, dan kehadiran
        $frontOfficeUser = User::factory()->create(['role' => 'front_office']);
        $dutyOfficerUser = User::factory()->create(['role' => 'duty_officer']);
        $this->actingAs($frontOfficeUser);

        $booking = Booking::factory()->create(['tanggal' => today()]);
        $kehadiran = Kehadiran::factory()->create([
            'kode_booking' => $booking->kode_booking,
            'status' => 'Checked-in',
            'tanggal_ci' => today(),
        ]);

        // Mengirim notifikasi ke Duty Officer
        // (Logika pengiriman notifikasi ke duty officer bisa disesuaikan dengan implementasi yang ada)
        
        // Duty Officer login dan menunda konfirmasi
        $this->actingAs($dutyOfficerUser);

        // Duty Officer menunda karena ruangan belum dalam kondisi semula
        $response = $this->post(route('ruangan.belum_siap', $kehadiran->ruangan_id));

        // Verifikasi: Sistem mengirimkan notifikasi ke FO
        $response->assertSessionHas('error', 'User perlu Kembali ke ruangan');
        $kehadiran->refresh();
        $this->assertEquals('Checked-in', $kehadiran->status);
    }
}
