<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'kode_booking' => 'KB' . $this->faker->unique()->numerify('-014'), // contoh: KB123
            'tanggal' => now()->format('Y-m-d'),
            'nama_event' => $this->faker->sentence(3),
            'nama_organisasi' => $this->faker->company,
            'kategori_event' => 'Workshop',
            'kategori_ekraf' => 'Musik',
            'jenis_event' => 'Internal',
            'ruangan_id' => 1, // pastikan ada ruangan_id ini di DB atau sesuaikan dengan test
            'lantai' => '1',
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
            'nama_pic' => $this->faker->name,
            'no_pic' => $this->faker->phoneNumber,
            'status' => 'Approved',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
