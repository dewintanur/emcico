<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Tambahkan ini
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Ruangan extends Model
{
    use HasFactory; // <-- Tambahkan ini juga

    protected $table = 'ruangan';
    protected $fillable = [
        'nama_ruangan',
        'lantai',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'status'
    ];
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'ruangan_id', 'id');
    }
    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class, 'ruangan_id', 'id'); // Tambahkan relasi ke kehadiran
    }
}
