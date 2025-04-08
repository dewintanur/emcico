<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $ruangan = [
            ['id' => 1, 'lantai' => 1, 'nama_ruangan' => 'Stage Outdoor', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '10:00:00', 'waktu_selesai' => '12:00:00'],
            ['id' => 2, 'lantai' => 2, 'nama_ruangan' => 'Teras Tengah', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 3, 'lantai' => 2, 'nama_ruangan' => 'Teras Utara', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '11:00:00', 'waktu_selesai' => '13:00:00'],
            ['id' => 4, 'lantai' => 4, 'nama_ruangan' => 'Coworking Space 1', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '09:00:00', 'waktu_selesai' => '17:00:00'],
            ['id' => 5, 'lantai' => 4, 'nama_ruangan' => 'Studio Musik & Recording', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 6, 'lantai' => 4, 'nama_ruangan' => 'Workshop Seni', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 7, 'lantai' => 4, 'nama_ruangan' => 'Lab Komputer', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 8, 'lantai' => 4, 'nama_ruangan' => 'Open Public Space Utara', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 9, 'lantai' => 5, 'nama_ruangan' => 'Coworking Space 1', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 10, 'lantai' => 5, 'nama_ruangan' => 'Coworking Space 2', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 11, 'lantai' => 5, 'nama_ruangan' => 'Amphitheater 1', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 12, 'lantai' => 5, 'nama_ruangan' => 'Amphitheater 2', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 13, 'lantai' => 5, 'nama_ruangan' => 'Studio Foto', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 14, 'lantai' => 5, 'nama_ruangan' => 'Ruang Fashion', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 15, 'lantai' => 6, 'nama_ruangan' => 'Open Public Space Utara', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 16, 'lantai' => 8, 'nama_ruangan' => 'Rooftop', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 17, 'lantai' => 2, 'nama_ruangan' => 'Teras Selatan', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 18, 'lantai' => 2, 'nama_ruangan' => 'Open Public Space Utara', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 19, 'lantai' => 2, 'nama_ruangan' => 'Creative City Planning Gallery', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 20, 'lantai' => 2, 'nama_ruangan' => 'Ruang Podcast', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 21, 'lantai' => 3, 'nama_ruangan' => 'Multifunction Room', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 22, 'lantai' => 3, 'nama_ruangan' => 'Open Public Space Utara 2', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 23, 'lantai' => 3, 'nama_ruangan' => 'Ruang Meeting', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 24, 'lantai' => 3, 'nama_ruangan' => 'Open Public Space Barat', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 25, 'lantai' => 3, 'nama_ruangan' => 'Ruang Kelas', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 26, 'lantai' => 3, 'nama_ruangan' => 'Food Lab', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 27, 'lantai' => 3, 'nama_ruangan' => 'Multi Purpose Area', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 28, 'lantai' => 4, 'nama_ruangan' => 'Multi Purpose Area', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
            ['id' => 29, 'lantai' => 6, 'nama_ruangan' => 'Perpustakaan 1', 'status' => 'Kosong', 'tanggal' => '2025-04-06', 'waktu_mulai' => '00:00:00', 'waktu_selesai' => '00:00:00'],
        ];

        DB::table('ruangan')->insert($ruangan);
    }
}
