<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\Kehadiran;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_page_redirects_if_not_logged_in()
    {
        $response = $this->get('/booking-list');
        $response->assertStatus(302); // redirect karena belum login
    }

    /** @test */
    public function check_in_succeeds_with_valid_booking_code()
    {
        // Arrange
        $booking = Booking::factory()->create([
            'kode_booking' => 'ABC123',
        ]);

        // Act
        $response = $this->post(route('proses_checkin'), [
            'kode_booking' => 'ABC123',
            'nama_ci' => 'Test User',
            'no_ci' => '08123456789',
            'instansi' => 'Universitas Testing',
            'email' => 'test@example.com',
            'alamat' => 'Jalan Uji Coba No. 1',
            'ttd' => 'data:image/png;base64,somebase64string',
        ]);

        // Assert
        $response->assertStatus(302);
        $this->assertDatabaseHas('kehadiran', [
            'kode_booking' => 'ABC123',
            'nama_ci' => 'Test User',
        ]);
    }

    /** @test */
    public function check_in_fails_if_booking_not_found()
    {
        $response = $this->post(route('proses_checkin'), [
            'kode_booking' => 'TIDAKADA',
            'nama_ci' => 'Test User',
            'no_ci' => '08123456789',
            'instansi' => 'Universitas Testing',
            'email' => 'test@example.com',
            'alamat' => 'Jalan Uji Coba No. 1',
            'ttd' => 'data:image/png;base64,somebase64string',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['kode_booking']);
    }

    /** @test */
    public function check_in_fails_if_already_checked_in()
    {
        $booking = Booking::factory()->create([
            'kode_booking' => 'DEF456',
        ]);

        Kehadiran::create([
            'kode_booking' => 'DEF456',
            'nama_ci' => 'Test User',
            'no_ci' => '08123456789',
            'instansi' => 'Universitas Testing',
            'email' => 'test@example.com',
            'alamat' => 'Jalan Uji Coba No. 1',
            'ttd' => 'data:image/png;base64,fakeimage',
        ]);

        $response = $this->post(route('proses_checkin'), [
            'kode_booking' => 'DEF456',
            'nama_ci' => 'Test User',
            'no_ci' => '08123456789',
            'instansi' => 'Universitas Testing',
            'email' => 'test@example.com',
            'alamat' => 'Jalan Uji Coba No. 1',
            'ttd' => 'data:image/png;base64,fakeimage',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['kode_booking']);
    }
}
