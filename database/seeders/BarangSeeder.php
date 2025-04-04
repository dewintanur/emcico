<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ListBarang;

class BarangSeeder extends Seeder
{
    public function run()
    {
        $barangs = [
            ['nama_barang' => 'Meja Innola', 'jumlah' => 5, 'satuan' => 'pcs'],
            ['nama_barang' => 'Kursi Merah', 'jumlah' => 30, 'satuan' => 'pcs'],
            ['nama_barang' => 'Kursi Kayu', 'jumlah' => 2, 'satuan' => 'pcs'],
            ['nama_barang' => 'Gawangan', 'jumlah' => 9, 'satuan' => 'pcs'],
            ['nama_barang' => 'Gawangan Kecil', 'jumlah' => 9, 'satuan' => 'pcs'],
            ['nama_barang' => 'Jemuran', 'jumlah' => 4, 'satuan' => 'pcs'],
            ['nama_barang' => 'Sound System Primatech (Type)', 'jumlah' => 1, 'satuan' => 'set (2) pcs'],
            ['nama_barang' => 'Mixer Primatech', 'jumlah' => 1, 'satuan' => 'pcs'],
            ['nama_barang' => 'Mic Wireles Primatech (Type)', 'jumlah' => 1, 'satuan' => 'set (2) pcs'],
        ];

        foreach ($barangs as $barang) {
            ListBarang::create($barang);
        }
    }
}

