<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_loads_for_fo_user()
    {
        // 1. Buat user role FO
        $user = User::factory()->create([
            'role' => 'front_office',
        ]);

        // 2. Login sebagai FO
        $this->actingAs($user);

        // 3. Akses halaman index
        $response = $this->get('/booking-list'); // ganti URL jika rutenya beda

        // 4. Pastikan halaman tampil dengan benar
        $response->assertStatus(200);
        $response->assertViewIs('FO.bookingList'); // sesuai nama view di controller
    }
}
