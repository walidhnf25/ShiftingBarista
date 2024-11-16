<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kesediaan extends Model
{
    use HasFactory;
    protected $table = 'kesediaan';
    protected $fillable = [
        'id_jadwal_shift',
        'id_user',
    ];

    /**
     * Get the user that owns the Kesediaan
     *
     * @return \Illuminate\Database\Eloquent\Relations\
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_users');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalShift::class, 'id_jadwal_shift');
    }
}
