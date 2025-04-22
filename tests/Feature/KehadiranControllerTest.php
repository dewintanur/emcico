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
        Carbon::setTestNow(Carbon::createFromTime(10, 0, 0)); // Set waktu sekarang
    }

    /** @test */
    public function user_can_check_in_with_valid_booking_code()
    {
        $booking = Booking::factory()->create([
            'kode_booking' => 'ABC123',
            'tanggal' => now()->format('Y-m-d'),
        ]);

        $response = $this->post(route('proses_checkin'), [
            'kode_booking' => 'ABC123',
            'nama_ci' => 'Budi',
            'no_ci' => '08123456789',
            'ttd' => 'data:image/png;base64,somebase64string',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('kehadiran', [
            'kode_booking' => 'ABC123',
            'nama_ci' => 'Budi',
            'no_ci' => '08123456789',
        ]);
    }

    /** @test */
    public function check_in_fails_if_already_checked_in()
    {
        $booking = Booking::factory()->create([
            'kode_booking' => 'DEF456',
            'tanggal' => now()->format('Y-m-d'),
        ]);

        Kehadiran::create([
            'kode_booking' => 'DEF456',
            'tanggal_ci' => now()->format('Y-m-d'),
            'nama_ci' => 'Sari',
            'no_ci' => '08123456700',
            'status' => 'Sedang Digunakan',
            'ttd' => 'data:image/png;base64,ttdsari',
        ]);

        $response = $this->post(route('proses_checkin'), [
            'kode_booking' => 'DEF456',
            'nama_ci' => 'Sari',
            'no_ci' => '08123456700',
            'ttd' => 'data:image/png;base64,ttdsari',
        ]);

        $response->assertSessionHasErrors(['kode_booking']);
    }

    /** @test */
    public function check_in_fails_if_booking_code_not_found()
    {
        $response = $this->post(route('proses_checkin'), [
            'kode_booking' => 'XYZ999',
            'nama_ci' => 'Andi',
            'no_ci' => '081200011122',
            'ttd' => 'data:image/png;base64,fake',
        ]);

        $response->assertSessionHasErrors(['kode_booking']);
    }

    /** @test */
    public function user_can_view_check_in_history()
    {
        $booking = Booking::factory()->create([
            'kode_booking' => 'HIS123',
            'tanggal' => now()->format('Y-m-d'),
        ]);

        Kehadiran::create([
            'kode_booking' => 'HIS123',
            'tanggal_ci' => now()->format('Y-m-d'),
            'nama_ci' => 'Rani',
            'no_ci' => '08567890987',
            'status' => 'Sedang Digunakan',
            'ttd' => 'data:image/png;base64,signaturedata',
        ]);

        $response = $this->get(route('riwayat.checkin'));

        $response->assertStatus(200);
        $response->assertSee('HIS123');
        $response->assertSee('Rani');
    }
}
