<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'kode_booking' => $this->faker->unique()->regexify('BOOK[0-9]{3}'),
            'tanggal' => now()->format('Y-m-d'),
            'nama_event' => $this->faker->sentence(3),
            'nama_organisasi' => $this->faker->company,
            'kategori_event' => 'Workshop',
            'kategori_ekraf' => 'Musik',
            'jenis_event' => 'Internal',
            'ruangan_id' => Ruangan::factory(), // Jika menggunakan relasi dengan Ruangan
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
