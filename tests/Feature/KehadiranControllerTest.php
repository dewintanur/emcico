<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Booking;
use App\Models\Kehadiran;
use Carbon\Carbon;

class KehadiranControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Default waktu sekarang
        Carbon::setTestNow(Carbon::createFromTime(10, 0, 0)); // pukul 10:00
    }

    /** @test */
    public function user_bisa_check_in_dengan_kode_booking_yang_valid()
    {
        $booking = Booking::factory()->create([
            'kode_booking' => 'ABC123',
            'tanggal' => now()->format('Y-m-d'),
        ]);

        $response = $this->post('/check-in', [
            'kode_booking' => 'ABC123',
            'nama' => 'Budi',
            'no_hp' => '08123456789',
            'ttd' => 'data:image/png;base64,somebase64string',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('kehadiran', [
            'kode_booking' => 'ABC123',
            'nama' => 'Budi',
            'no_hp' => '08123456789',
        ]);
    }

    /** @test */
    public function check_in_ditolak_jika_sudah_pernah_check_in()
    {
        $booking = Booking::factory()->create([
            'kode_booking' => 'DEF456',
            'tanggal' => now()->format('Y-m-d'),
        ]);

        Kehadiran::create([
            'kode_booking' => 'DEF456',
            'tanggal' => now()->format('Y-m-d'),
            'nama' => 'Sari',
            'no_hp' => '08123456700',
            'status' => 'Sedang Digunakan',
            'ttd' => 'data:image/png;base64,ttdsari',
        ]);

        $response = $this->post('/check-in', [
            'kode_booking' => 'DEF456',
            'nama' => 'Sari',
            'no_hp' => '08123456700',
            'ttd' => 'data:image/png;base64,ttdsari',
        ]);

        $response->assertSessionHasErrors(['kode_booking']);
    }

    /** @test */
    public function check_in_ditolak_jika_kode_booking_tidak_ditemukan()
    {
        $response = $this->post('/check-in', [
            'kode_booking' => 'XYZ999',
            'nama' => 'Andi',
            'no_hp' => '081200011122',
            'ttd' => 'data:image/png;base64,fake',
        ]);

        $response->assertSessionHasErrors(['kode_booking']);
    }

    /** @test */
    public function bisa_melihat_riwayat_check_in()
    {
        $booking = Booking::factory()->create([
            'kode_booking' => 'HIS123',
            'tanggal' => now()->format('Y-m-d'),
        ]);

        Kehadiran::create([
            'kode_booking' => 'HIS123',
            'tanggal' => now()->format('Y-m-d'),
            'nama' => 'Rani',
            'no_hp' => '08567890987',
            'status' => 'Sedang Digunakan',
            'ttd' => 'data:image/png;base64,signaturedata',
        ]);

        $response = $this->get('/riwayat-checkin');

        $response->assertStatus(200);
        $response->assertSee('HIS123');
        $response->assertSee('Rani');
    }
}
