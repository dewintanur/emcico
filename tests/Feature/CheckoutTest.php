<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Models\Kehadiran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test check-out yang sukses.
     *
     * @return void
     */
    public function test_successful_checkout()
    {
        // Membuat user Front Office
        $frontOfficeUser = User::factory()->create(['role' => 'front_office']);

        // Login sebagai Front Office
        $this->actingAs($frontOfficeUser);

        // Membuat booking dan kehadiran untuk hari ini
        $booking = Booking::factory()->create(['tanggal' => today()]);
        $kehadiran = Kehadiran::factory()->create([
            'kode_booking' => $booking->kode_booking,
            'status' => 'Checked-in',
            'tanggal_ci' => today()
        ]);

        // Mengirim permintaan checkout
        $response = $this->post(route('checkout'), [
            'kode_booking' => $kehadiran->kode_booking,
        ]);

        // Cek apakah status kehadiran berubah menjadi checked-out
        $kehadiran->refresh();
        $this->assertEquals('Checked-out', $kehadiran->status);
        $response->assertRedirect()->with('success', 'Checkout berhasil!');
    }
    public function test_checkout_tunda_if_room_not_confirmed_by_duty_officer()
    {
        // Create a Front Office user
        $frontOfficeUser = User::factory()->create(['role' => 'front_office']);
    
        // Log in as Front Office
        $this->actingAs($frontOfficeUser);
    
        // Create booking and presence for today
        $booking = Booking::factory()->create(['tanggal' => today()]);
        $kehadiran = Kehadiran::factory()->create([
            'kode_booking' => $booking->kode_booking,
            'status' => 'Checked-in',
            'tanggal_ci' => today(),
            'duty_officer' => null, // Duty officer hasn't confirmed
        ]);
    
        // Send checkout request
        $response = $this->post(route('checkout'), [
            'kode_booking' => $kehadiran->kode_booking,
        ]);
    
        // Verify that the error message is set in the session
        $response->assertSessionHas('error', 'Ruangan belum dikonfirmasi, check-out ditunda');
        
        // Ensure status remains 'Checked-in'
        $kehadiran->refresh();
        $this->assertEquals('Checked-in', $kehadiran->status);
    }
    
}