<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Kehadiran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class CheckinTest extends TestCase
{
    use RefreshDatabase;

    // Test untuk memverifikasi apakah kode booking valid dan check-in bisa dilakukan
    public function test_checkin_with_valid_booking()
    {
        // Membuat booking untuk hari ini
        $booking = Booking::factory()->create([
            'kode_booking' => 'BOOK123',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
        ]);

        // Kirim request check-in dengan kode booking yang valid
        $response = $this->post('/checkin', [
            'id_booking' => 'BOOK123',
        ]);

        // Pastikan response berhasil mengarahkan ke halaman pengisian data check-in
        $response->assertRedirect(route('isi_data'));
        $response->assertSessionHas('success', 'Silakan isi data check-in Anda.');
    }

    // Test jika kode booking tidak valid
    public function test_checkin_with_invalid_booking()
    {
        // Mengirim kode booking yang tidak ada
        $response = $this->post('/checkin', [
            'id_booking' => 'INVALIDBOOKING',
        ]);

        // Pastikan response kembali dengan pesan error
        $response->assertRedirect('/');
        $response->assertSessionHas('gagal', 'Kode booking tidak ditemukan atau tidak berlaku.');
    }

    // Test jika waktu check-in belum bisa dilakukan
    public function test_checkin_before_start_time()
    {
        // Membuat booking untuk hari ini dengan waktu check-in dimulai setelah 12:00
        $booking = Booking::factory()->create([
            'kode_booking' => 'BOOK123',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '12:00',
            'waktu_selesai' => '14:00',
        ]);

        // Kirim request check-in sebelum waktu mulai
        $response = $this->post('/checkin', [
            'id_booking' => 'BOOK123',
        ]);

        // Pastikan response memberi tahu bahwa check-in hanya bisa dilakukan setelah waktu yang ditentukan
        $response->assertRedirect('/');
        $response->assertSessionHas('gagal', 'Check-in hanya bisa dilakukan setelah pukul 12:00.');
    }

    // Test jika pengunjung sudah melakukan check-in
    public function test_checkin_already_done()
    {
        // Membuat booking untuk hari ini
        $booking = Booking::factory()->create([
            'kode_booking' => 'BOOK123',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
        ]);

        // Simulasi pengunjung sudah melakukan check-in
        Kehadiran::create([
            'kode_booking' => 'BOOK123',
            'nama_ci' => 'John Doe',
            'no_ci' => '08123456789',
            'tanggal_ci' => now(),
            'status' => 'Checked-in',
        ]);

        // Kirim request check-in lagi untuk booking yang sama
        $response = $this->post('/checkin', [
            'id_booking' => 'BOOK123',
        ]);

        // Pastikan response memberi tahu bahwa check-in sudah dilakukan
        $response->assertRedirect('/');
        $response->assertSessionHas('gagal', 'Anda sudah melakukan check-in hari ini.');
    }

    // Test untuk memastikan bahwa data check-in berhasil disimpan
    public function test_checkin_data_submission()
    {
        // Membuat booking untuk hari ini
        $booking = Booking::factory()->create([
            'kode_booking' => 'BOOK123',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
        ]);

        // Kirim request check-in dengan kode booking yang valid
        $this->post('/checkin', [
            'id_booking' => 'BOOK123',
        ]);

        // Mengirimkan data check-in
        $response = $this->post('/checkin/store', [
            'kode_booking' => 'BOOK123',
            'name' => 'John Doe',
            'phone' => '08123456789',
            'signatureData' => 'data-signature',
        ]);

        // Pastikan data check-in disimpan dan respon sukses
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Check-in berhasil!');
        
        // Memeriksa apakah data check-in sudah ada di database
        $this->assertDatabaseHas('kehadiran', [
            'kode_booking' => 'BOOK123',
            'nama_ci' => 'John Doe',
            'no_ci' => '08123456789',
            'status' => 'Checked-in',
        ]);
    }
}
