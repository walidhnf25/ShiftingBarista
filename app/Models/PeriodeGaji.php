<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeGaji extends Model
{
    use HasFactory;
    protected $table = 'periode_gaji';
    protected $fillable = [
        'id_periode_gaji',
        'nama_periode_gaji',
        'tgl_mulai',
        'tgl_akhir',
    ];

}
