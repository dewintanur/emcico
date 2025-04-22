<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Imports\BookingImport;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test import booking with valid CSV.
     */
    public function test_admin_can_import_booking_with_valid_file()
    {
        Excel::fake(); // Fake Excel agar tidak benar-benar menjalankan import

        // Buat user admin dan login
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Simulasi upload file CSV
        $file = UploadedFile::fake()->create('booking_data.csv', 100, 'text/csv');

        // Kirim request import sebagai admin
        $response = $this->actingAs($admin)->post(route('booking.import'), [
            'file' => $file,
        ]);

        // Pastikan redirect dengan pesan sukses
        $response->assertRedirect(route('booking.import'));
        $response->assertSessionHas('success', 'Data booking berhasil diimpor!');

        // Verifikasi BookingImport dijalankan
        Excel::assertImported('booking_data.csv', function (BookingImport $import) {
            return true;
        });
    }

    /**
     * Test import booking with invalid file type.
     */
    public function test_admin_cannot_import_booking_with_invalid_file()
    {
        // Buat user admin dan login
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Simulasi upload file TXT (bukan CSV)
        $file = UploadedFile::fake()->create('booking_data.txt', 100, 'text/plain');

        $response = $this->actingAs($admin)->post(route('booking.import'), [
            'file' => $file,
        ]);

        // Validasi gagal, harus ada error pada input file
        $response->assertSessionHasErrors('file');
    }
}
