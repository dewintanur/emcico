<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PeminjamanBarang;

class Booking extends Model
{
    protected $table = 'booking'; // Sesuaikan dengan nama tabel
    protected $fillable = [
        'kode_booking',
        'tanggal',
        'nama_event',
        'nama_organisasi',
        'kategori_event',
        'kategori_ekraf',
        'jenis_event',
        'ruangan_id',
        'lantai',
        'waktu_mulai',
        'waktu_selesai',
        'nama_pic',
        'no_pic',
        'status',
        'created_at',
        'updated_at',
    ];

    // Tambahkan casting untuk waktu
    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'waktu_mulai' => 'string',  // Ubah dari time ke string
        'waktu_selesai' => 'string', // Ubah dari time ke string
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function peminjaman()
    {
        return $this->hasMany(PeminjamanBarang::class, 'kode_booking', 'kode_booking');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'kode_booking', 'kode_booking');
    }

    public function barang()
    {
        return $this->belongsTo(ListBarang::class, 'barang_id', 'id');
    }
}
