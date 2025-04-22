<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ListBarang;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListBarang>
 */
class ListBarangFactory extends Factory
{
    protected $model = ListBarang::class;

    public function definition(): array
    {
        return [
            'nama_barang' => $this->faker->randomElement([
                'Proyektor', 'Speaker Portable', 'Kabel HDMI', 'Mic Wireless', 'Stand Banner'
            ]),
            'jumlah' => $this->faker->numberBetween(1, 30),
            'satuan' => $this->faker->randomElement(['pcs', 'set(2)', 'unit']),
        ];
    }
}
