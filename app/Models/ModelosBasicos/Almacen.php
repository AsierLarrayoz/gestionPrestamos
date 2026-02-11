<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacenes';
    protected $fillable = [
        'almacen'
    ];
    public function activos()
    {
        return $this->hasMany(Activo::class);
    }
}
