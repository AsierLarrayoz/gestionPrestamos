<?php

namespace App\Models\ModelosBasicos;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $fillable = [
        'rol'
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
