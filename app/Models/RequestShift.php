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
        return $this->belongsTo(TipePekerjaan::class, 'id_tipe_pekerjaan', 'id'); // Foreign key and local key
    }
}
