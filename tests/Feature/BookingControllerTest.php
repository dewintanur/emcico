<?php
namespace Tests\Feature;

use App\Models\Kehadiran;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_booking_list_for_today()
    {
        $front_office = User::factory()->create([
            'role' => 'front_office', // Pastikan front_office dapat melihat daftar booking
        ]);

        // Membuat beberapa data booking untuk hari ini
        Booking::factory()->create([
            'tanggal' => today(),
            'kode_booking' => 'B12345',
            'nama_event' => 'Event 1',
        ]);
        Booking::factory()->create([
            'tanggal' => today(),
            'kode_booking' => 'B12346',
            'nama_event' => 'Event 2',
        ]);

        // Melakukan login sebagai front_office
        $this->actingAs($front_office);

        // Mengakses halaman booking list
        $response = $this->get(route('fo.bookingList'));

        // Memastikan bahwa booking list hari ini ditampilkan
        $response->assertStatus(200);
        $response->assertSee('Event 1');
        $response->assertSee('Event 2');
    }

    /** @test */
    public function front_office_can_filter_booking_by_status()
    {
        $front_office = User::factory()->create([
            'role' => 'front_office',
        ]);
    
        // Booking yang dianggap Approved karena belum ada kehadiran
        $booking1 = Booking::factory()->create([
            'tanggal' => today(),
            'kode_booking' => 'B12345',
        ]);
    
        // Booking yang sudah Booked (ada kehadiran)
        $booking2 = Booking::factory()->create([
            'tanggal' => today(),
            'kode_booking' => 'B12346',
        ]);
    
        Kehadiran::factory()->create([
            'kode_booking' => $booking2->kode_booking,
            'status' => 'Booked',
        ]);
    
        $this->actingAs($front_office);
    
        $response = $this->get(route('fo.bookingList', ['status' => 'Approved']));
    
        $response->assertStatus(200);
        $response->assertSee('B12345'); // Harus tampil karena belum ada data kehadiran
        $response->assertDontSee('B12346'); // Tidak tampil karena sudah ada kehadiran
    }
    
    /** @test */
    public function admin_can_search_booking_by_code_or_event_name()
    {
        $admin = User::factory()->create([
            'role' => 'front_office',
        ]);

        // Membuat data booking dengan nama event dan kode booking
        Booking::factory()->create([
            'tanggal' => today(),
            'kode_booking' => 'B12345',
            'nama_event' => 'Event 1',
        ]);
        Booking::factory()->create([
            'tanggal' => today(),
            'kode_booking' => 'B12346',
            'nama_event' => 'Event 2',
        ]);

        // Melakukan login sebagai admin
        $this->actingAs($admin);

        // Mengakses halaman booking list dengan pencarian
        $response = $this->get(route('fo.bookingList', ['search' => 'Event 1']));

        // Memastikan hanya booking dengan nama event 'Event 1' yang ditampilkan
        $response->assertStatus(200);
        $response->assertSee('Event 1');
        $response->assertDontSee('Event 2');
    }
}
