<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeminjamanBarang extends Model
{
    use HasFactory;

    use SoftDeletes; // Tambahkan ini

    protected $table = 'peminjaman_barang'; // Sesuaikan dengan nama tabel
    protected $fillable = [
        'kode_booking', 
        'barang_id', 
        'jumlah',
        'marketing', // Tambahkan ini
        'created_by',
        'deleted_by',
        'status_pengembalian'
    ];
    
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'kode_booking', 'kode_booking');
    }
    public function barang()
    {
        return $this->belongsTo(ListBarang::class, 'barang_id', 'id');
    }
    
    
}
