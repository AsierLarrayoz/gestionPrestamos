<?php

namespace App\Models\ModelosBasicos;

use App\Models\Activo;
use App\Models\Almacen_activo;
use App\Models\Prestamo;




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
