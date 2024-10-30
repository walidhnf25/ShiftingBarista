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
        'tipe_pekerjaan',
        'tanggal',
        'tipe_pekerjaan',
    ];
}
