<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListBarang extends Model
{
    use HasFactory;

    protected $table = 'list_barang'; // Nama tabel di database

    protected $fillable = [
        'nama_barang',
        'jumlah',
        'satuan',
    ];

    public $timestamps = true; // Supaya created_at & updated_at otomatis terisi
}
