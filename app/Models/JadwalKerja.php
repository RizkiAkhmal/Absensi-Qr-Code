<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKerja extends Model
{
    use HasFactory;

     protected $fillable = [
        'id_user',
        'hari',
        'jam_masuk',
        'jam_pulang',
        'is_libur',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
