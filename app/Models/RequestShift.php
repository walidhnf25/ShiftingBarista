<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestShift extends Model
{
    use HasFactory;
    protected $table = 'jadwal_shift';
    protected $fillable = [
        'id_jadwal_shift',
        'id_user',
    ];

    public function tipePekerjaan()
    {
        return $this->belongsTo(TipePekerjaan::class, 'id_tipe_pekerjaan'); // Foreign key and local key
    }

    public function jadwalShift()
    {
        return $this->belongsTo(JadwalShift::class, 'id_jadwal_shift');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
