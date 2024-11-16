<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamShift extends Model
{
    use HasFactory;
    protected $table = 'jam_shift';
    protected $fillable = [
        'jam_mulai',
        'jam_selesai', // Ensure this is here
        'id_outlet',
    ];

    public function kesediaan()
    {
        return $this->hasMany(Kesediaan::class, 'id_jadwal_shift');
    }
}
