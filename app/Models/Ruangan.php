<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Ruangan extends Model
{
    protected $table = 'ruangan';

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'ruangan_id', 'id');
    }
    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class, 'ruangan_id', 'id'); // Tambahkan relasi ke kehadiran
    }
}
