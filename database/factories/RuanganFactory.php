<?php

namespace Database\Factories;

use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Factories\Factory;

class RuanganFactory extends Factory
{
    protected $model = Ruangan::class;

    public function definition(): array
    {
        return [
            'nama_ruangan' => $this->faker->randomElement([
                'Stage Outdoor',
                'Teras Tengah',
                'Teras Utara',
                'Coworking Space 1',
                'Studio Musik & Recording',
                'Workshop Seni',
                'Lab Komputer',
                'Open Public Space Utara',
            ]),
            'lantai' => $this->faker->numberBetween(1, 4),
            'tanggal' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'waktu_mulai' => $this->faker->time('H:i:s'),
            'waktu_selesai' => $this->faker->time('H:i:s'),
            'status' => $this->faker->randomElement(['Kosong', 'Dipesan', 'Sedang Digunakan']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
