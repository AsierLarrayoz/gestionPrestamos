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
        return $this->belongsToMany(Activo::class, 'almacen_activos')
            ->withPivot('cantidad')
            ->withTimestamps();
    }
    public function almacen_activos()
    {
        return $this->hasMany(Almacen_activo::class);
    }
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }
}
