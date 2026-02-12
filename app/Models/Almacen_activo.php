<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen_activo extends Model
{
    protected $fillable = [
        'almacen_id',
        'activo_id',
        'cantidad'
    ];
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }
}
