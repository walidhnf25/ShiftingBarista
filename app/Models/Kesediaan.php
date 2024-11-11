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
}
