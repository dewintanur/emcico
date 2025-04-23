<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kehadiran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CheckinHistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_checkin_history_page()
    {
        // Arrange: Login sebagai FrontOffice, Marketing, atau Admin
        $user = User::factory()->create();
        $this->actingAs($user);

        // Membuat data kehadiran untuk diuji
        Kehadiran::factory()->count(5)->create();

        // Act: Mengakses halaman checkin history
        $response = $this->get(route('riwayat.checkin'));

        // Assert: Memastikan halaman ditampilkan dengan data yang sesuai
        $response->assertStatus(200);
        $response->assertViewIs('riwayat.checkin');
        $response->assertViewHas('kehadiran');
    }

    /** @test */
    public function it_shows_checkin_detail_page()
    {
        // Arrange: Login sebagai FrontOffice, Marketing, atau Admin
        $user = User::factory()->create();
        $this->actingAs($user);

        // Membuat data kehadiran untuk diuji
        $kehadiran = Kehadiran::factory()->create();

        // Act: Mengakses halaman detail checkin dengan kode booking
        $response = $this->get(route('riwayat.checkin.detail', $kehadiran->kode_booking));

        // Assert: Memastikan halaman detail ditampilkan dengan data yang sesuai
        $response->assertStatus(200);
        $response->assertViewIs('riwayat.checkin_detail');
        $response->assertViewHas('kehadiran');
        $response->assertViewHas('kode_booking');
    }
   /** @test */
public function it_exports_checkin_history_to_excel()
{
    Storage::fake('local');
    Excel::fake();

    $user = User::factory()->create();
    $this->actingAs($user);

    Kehadiran::factory()->count(5)->create();

    $response = $this->get(route('riwayat.checkin.export.excel', ['tanggal' => now()->toDateString()]));

    $response->assertStatus(200);

    // Cek jika Excel diekspor (tanpa benar-benar menyimpan file)
    Excel::assertDownloaded('riwayat_checkin.xlsx');
}

      /** @test */
public function it_exports_checkin_history_to_pdf()
{
    Storage::fake('local');

    $user = User::factory()->create();
    $this->actingAs($user);

    Kehadiran::factory()->count(5)->create();

    $response = $this->get(route('riwayat.checkin.export.pdf', ['tanggal' => now()->toDateString()]));

    $response->assertStatus(200);
    // Cek isi response langsung (PDF header)
    $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
}

}
