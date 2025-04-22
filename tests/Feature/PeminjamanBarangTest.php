<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\ListBarang;
use App\Models\PeminjamanBarang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PeminjamanBarangTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_bisa_menambahkan_barang()
    {
        $user = User::factory()->create(['role' => 'marketing']);
        $barang = ListBarang::factory()->create(['jumlah' => 10]);
        $booking = Booking::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/peminjaman/store', [
            'kode_booking' => $booking->kode_booking,
            'barang_id' => $barang->id,
            'jumlah' => 2,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('peminjaman_barang', [
            'kode_booking' => $booking->kode_booking,
            'barang_id' => $barang->id,
            'jumlah' => 2,
        ]);
    }

    public function test_marketing_bisa_menghapus_peminjaman_barang()
    {
        $user = User::factory()->create(['role' => 'marketing']);
        $barang = ListBarang::factory()->create(['jumlah' => 5]);
        $booking = Booking::factory()->create();

        $this->actingAs($user);

        $peminjaman = PeminjamanBarang::create([
            'kode_booking' => $booking->kode_booking,
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'marketing' => $user->nama,
            'created_by' => $user->id,
        ]);

        $response = $this->deleteJson("/peminjaman/{$peminjaman->id}");

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('peminjaman_barang', [
            'id' => $peminjaman->id,
            'deleted_by' => $user->id,
        ]);
    }
}
