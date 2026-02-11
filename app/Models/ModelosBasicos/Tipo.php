<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $fillable = [
        'tipo'
    ];
    public function activos()
    {
        return $this->hasMany(Activo::class);
    }
}
