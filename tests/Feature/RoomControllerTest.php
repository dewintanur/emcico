<?php

namespace Tests\Feature;


use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\Kehadiran;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_menampilkan_halaman_room_list()
    {
        $user = User::factory()->create(['role' => 'duty_officer']);
        $this->actingAs($user);
        $response = $this->get(route('ruangan.index'));

        $response->assertStatus(200);
        $response->assertViewIs('FO.roomList');
        $response->assertViewHas('ruangan');
        $response->assertViewHas('lantaiOptions');
    }

    public function test_konfirmasi_berhasil_mengubah_status_konfirmasi()
    {
        $user = User::factory()->create(['role' => 'duty_officer']);
    $this->actingAs($user);
        $ruangan = Ruangan::factory()->create();
        $kehadiran = Kehadiran::factory()->create([
            'ruangan_id' => $ruangan->id,
            'status' => 'checked-in',
            'status_konfirmasi' => 'belum_siap_checkout'
        ]);

        $response = $this->post(route('ruangan.konfirmasi', $ruangan->id));

        $response->assertRedirect();
        $this->assertEquals('siap_checkout', $kehadiran->fresh()->status_konfirmasi);
    }

    public function test_belum_siap_berhasil_mengubah_status()
    {
        $user = User::factory()->create(['role' => 'duty_officer']);
        $this->actingAs($user);

        $ruangan = Ruangan::factory()->create();
        $kehadiran = Kehadiran::factory()->create([
            'ruangan_id' => $ruangan->id,
            'duty_officer' => $user->id,
        ]);

        $response = $this->post(route('ruangan.belum_siap', $ruangan->id));

        $response->assertRedirect();
        $this->assertEquals('belum_siap_checkout', $kehadiran->fresh()->status_konfirmasi);
    }

    public function test_import_csv_berhasil()
    {
        $user = User::factory()->create(['role' => 'it']);
$this->actingAs($user);

        Storage::fake('local');
        $csvContent = "nama_ruangan,lantai,tanggal,waktu_mulai,waktu_selesai,status\nR1,1,2025-04-23,08:00,10:00,Kosong";

        $file = UploadedFile::fake()->createWithContent('ruangan.csv', $csvContent);

        $response = $this->post(route('ruangan.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ruangan', ['nama_ruangan' => 'R1']);
    }
}
