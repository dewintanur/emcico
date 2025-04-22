<?php

// database/factories/PeminjamanBarangFactory.php

namespace Database\Factories;

use App\Models\PeminjamanBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanBarangFactory extends Factory
{
    protected $model = PeminjamanBarang::class;

    public function definition()
    {
        return [
            'kode_booking' => $this->faker->word, // Use proper fake data here
            'nama_barang' => $this->faker->word,
            'jumlah' => $this->faker->randomNumber(),
            'marketing' => $this->faker->name,
            'created_by' => $this->faker->name,
            'status_pengembalian' => 'Belum Dikembalikan',
            'barang_id' => \App\Models\ListBarang::factory(), // Assuming you have a Barang factory

        ];
    }
}
