<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    protected $table = 'kehadiran'; // Sesuaikan dengan nama tabel
    protected $fillable = [
        'kode_booking',
        'nama_ci',
        'ruangan_id',
        'no_ci',
        'tanggal_ci',
        'ttd',
        'duty_officer',
        'status',
        'status_konfirmasi',
        'created_at',
        'updated_at'
    ];
    public function dutyOfficer()
    {
        return $this->belongsTo(User::class, 'duty_officer'); // Menghubungkan dengan tabel users
    }
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'kode_booking', 'kode_booking'); // Relasi ke Booking
    }
    public function fo()
{
    return $this->belongsTo(User::class, 'fo_id');
}
public function peminjaman()
{
    return $this->hasOne(PeminjamanBarang::class, 'kode_booking', 'kode_booking');
}
public function marketing()
{
    return $this->belongsTo(User::class, 'marketing_id'); // Pastikan di tabel peminjaman_barang ada kolom marketing_id
}

}
