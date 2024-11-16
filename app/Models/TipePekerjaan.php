<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipePekerjaan extends Model
{
    use HasFactory;
    protected $table = 'tipe_pekerjaan';
    protected $fillable = [
        'tipe_pekerjaan', // Ensure this is here
    ];

    public function jadwalShifts()
    {
        return $this->hasMany(JadwalShift::class, 'id_tipe_pekerjaan');
    }
}
