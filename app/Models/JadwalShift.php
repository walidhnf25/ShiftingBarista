<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalShift extends Model
{
    use HasFactory;
    protected $table = 'jadwal_shift';
    protected $fillable = [
        'id_jam',
        'id_outlet',
        'id_tipe_pekerjaan',
        'tanggal',
        'status',
        'id_user',
        'task',
    ];

    public function tipePekerjaan()
    {
        return $this->belongsTo(TipePekerjaan::class, 'id_tipe_pekerjaan'); // Foreign key and local key
    }

    public function kesediaan()
    {
        return $this->hasMany(Kesediaan::class, 'id_jadwal_shift');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id'); // 'id_user' is the foreign key
    }

    public function jamShift()
    {
        return $this->belongsTo(JamShift::class, 'id_jam', 'id');
    }
}
