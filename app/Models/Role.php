<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nama'];

    /**
     * Define the relationship to users.
     * Each role can have many users.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_role');
    }
}
