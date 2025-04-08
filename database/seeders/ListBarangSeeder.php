<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ListBarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('list_barang')->truncate(); // optional: bersihkan dulu

        $now = Carbon::now();

        DB::table('list_barang')->insert([
            [
                'nama_barang' => 'Proyektor',
                'jumlah' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_barang' => 'Speaker Portable',
                'jumlah' => 8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_barang' => 'Kabel HDMI',
                'jumlah' => 15,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_barang' => 'Mic Wireless',
                'jumlah' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_barang' => 'Stand Banner',
                'jumlah' => 12,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
