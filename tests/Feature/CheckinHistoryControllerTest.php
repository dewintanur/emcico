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
        $response = $this->get(route('riwayat.checkin.index'));

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
        $response = $this->get(route('riwayat.checkin.show', $kehadiran->kode_booking));

        // Assert: Memastikan halaman detail ditampilkan dengan data yang sesuai
        $response->assertStatus(200);
        $response->assertViewIs('riwayat.checkin_detail');
        $response->assertViewHas('kehadiran');
        $response->assertViewHas('kode_booking');
    }

    /** @test */
    public function it_exports_checkin_history_to_excel()
    {
        // Arrange: Login sebagai FrontOffice, Marketing, atau Admin
        $user = User::factory()->create();
        $this->actingAs($user);

        // Membuat data kehadiran untuk diuji
        Kehadiran::factory()->count(5)->create();

        // Act: Menekan tombol export untuk Excel
        $response = $this->get(route('riwayat.checkin.export.excel', ['tanggal' => now()->toDateString()]));

        // Assert: Memastikan file berhasil diunduh dalam format Excel
        $response->assertStatus(200);
        $this->assertContains('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertTrue(Storage::disk('local')->exists('exports/riwayat_checkin.xlsx'));

        // Optional: Memastikan data yang diekspor sesuai dengan yang ada di database
        $exportedData = Excel::toArray(new \App\Exports\RiwayatCheckinExport, storage_path('app/exports/riwayat_checkin.xlsx'));
        $this->assertCount(5, $exportedData[0]); // Pastikan jumlah data sesuai dengan yang ada di database
    }

    /** @test */
    public function it_exports_checkin_history_to_pdf()
    {
        // Arrange: Login sebagai FrontOffice, Marketing, atau Admin
        $user = User::factory()->create();
        $this->actingAs($user);

        // Membuat data kehadiran untuk diuji
        Kehadiran::factory()->count(5)->create();

        // Act: Menekan tombol export untuk PDF
        $response = $this->get(route('riwayat.checkin.export.pdf', ['tanggal' => now()->toDateString()]));

        // Assert: Memastikan file berhasil diunduh dalam format PDF
        $response->assertStatus(200);
        $this->assertContains('application/pdf', $response->headers->get('Content-Type'));
        $this->assertTrue(Storage::disk('local')->exists('exports/riwayat_checkin.pdf'));

        // Optional: Memastikan PDF dapat dibaca atau dimuat (bisa menggunakan library DomPDF untuk verifikasi konten)
        $pdfContent = Storage::disk('local')->get('exports/riwayat_checkin.pdf');
        $this->assertNotEmpty($pdfContent);
    }
}
