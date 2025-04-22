<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Kehadiran;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class KehadiranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_booking' => Booking::factory(), // Memastikan kode_booking diambil dari booking yang ada
            'nama_ci' => $this->faker->name(),
            'ruangan_id' => $this->faker->randomNumber(1), // Pastikan sesuai dengan tipe data yang ada di tabel
            'no_ci' => $this->faker->phoneNumber(),
            'tanggal_ci' => Carbon::now()->format('Y-m-d'), // Tanggal check-in saat ini
            'ttd' => 'data:image/png;base64,' . base64_encode('signaturedata'), // Signature dummy
            'duty_officer' => User::factory(), // Mengambil user untuk duty officer
            'status' => 'Sedang Digunakan',
            'status_konfirmasi' => 'Terverifikasi', // Sesuaikan dengan status yang mungkin ada
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
