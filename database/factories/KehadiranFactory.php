<?php
// database/factories/KehadiranFactory.php
namespace Database\Factories;

use App\Models\Kehadiran;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class KehadiranFactory extends Factory
{
    protected $model = Kehadiran::class;

    public function definition(): array
    {
        $booking = Booking::factory()->create();

        return [
            'kode_booking' => Booking::factory(), // Relasi dengan Booking
            'nama_ci' => $this->faker->name(),
            'ruangan_id' => $booking->ruangan_id, // sesuaikan dengan ruangan di booking
            'no_ci' => $this->faker->phoneNumber(),
            'tanggal_ci' => Carbon::now()->format('Y-m-d'),
            'ttd' => 'data:image/png;base64,' . base64_encode('signaturedata'), // Base64 dummy signature
            'duty_officer' => User::factory(), // Relasi dengan User
            'status' => 'Checked-in',
            'status_konfirmasi' => 'belum_konfirmasi',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
