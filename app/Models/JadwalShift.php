<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalShift extends Model
{
    use HasFactory;
    protected $table = 'jadwal_shift';
    protected $fillable = [
        'jam_kerja',
        'id_outlet',
        'id_tipe_pekerjaan',
        'tanggal_mulai',
        'tanggal_akhir',
        'status',
    ];

    public function tipePekerjaan()
    {
        return $this->belongsTo(TipePekerjaan::class, 'id_tipe_pekerjaan', 'id'); // Foreign key and local key
    }

}
