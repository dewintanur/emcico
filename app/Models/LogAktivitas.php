<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'aktivitas', 'waktu'];
    public $timestamps = false; // Matikan timestamps agar tidak mencari updated_at
    protected $casts = [
        'user_id' => 'integer',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
