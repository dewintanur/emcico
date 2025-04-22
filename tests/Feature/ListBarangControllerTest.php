<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ListBarang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ListBarangControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test jika admin dapat menambah barang dengan data valid.
     *
     * @return void
     */
    public function test_admin_can_add_barang_with_valid_data()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'role' => 'admin', // Pastikan role admin
        ]);

        // Simulasi login sebagai admin
        $this->actingAs($admin);

        // Data yang valid untuk menambah barang
        $data = [
            'nama_barang' => 'Barang Test',
            'jumlah' => 10,
            'satuan' => 'pcs',
        ];

        // Melakukan post request ke endpoint store
        $response = $this->post(route('list_barang.store'), $data);

        // Memastikan barang baru disimpan dan redirect ke daftar barang
        $response->assertRedirect(route('list_barang.index'));
        $response->assertSessionHas('success', 'Barang berhasil ditambahkan!');

        // Memastikan barang tersimpan di database
        $this->assertDatabaseHas('list_barang', [
            'nama_barang' => 'Barang Test',
            'jumlah' => 10,
            'satuan' => 'pcs',
        ]);
    }

    /**
     * Test jika admin gagal menambah barang dengan data tidak valid.
     *
     * @return void
     */
    public function test_admin_cannot_add_barang_with_invalid_data()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'role' => 'admin', // Pastikan role admin
        ]);

        // Simulasi login sebagai admin
        $this->actingAs($admin);

        // Data yang invalid (misalnya nama barang kosong)
        $data = [
            'nama_barang' => '', // Nama barang kosong
            'jumlah' => 10,
            'satuan' => 'pcs',
        ];

        // Melakukan post request ke endpoint store
        $response = $this->post(route('list_barang.store'), $data);

        // Memastikan sistem menampilkan pesan error
        $response->assertSessionHasErrors('nama_barang');
    }

    /**
     * Test jika admin gagal menambah barang dengan jumlah yang kurang dari 1.
     *
     * @return void
     */
    public function test_admin_cannot_add_barang_with_invalid_quantity()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'role' => 'admin', // Pastikan role admin
        ]);

        // Simulasi login sebagai admin
        $this->actingAs($admin);

        // Data dengan jumlah kurang dari 1
        $data = [
            'nama_barang' => 'Barang Test',
            'jumlah' => 0, // Jumlah barang 0
            'satuan' => 'pcs',
        ];

        // Melakukan post request ke endpoint store
        $response = $this->post(route('list_barang.store'), $data);

        // Memastikan sistem menampilkan pesan error jumlah barang
        $response->assertSessionHasErrors('jumlah');
    }
}
