<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelosBasicos\Almacen;
use App\Models\ModelosBasicos\Tipo;
use App\Models\ModelosBasicos\Modelo;
use App\Models\ModelosBasicos\Salud;


class Activo extends Model
{
    protected $fillable = [
        'serial_number',
        //'observaciones',
        'cantidad',
        'is_serialized',
        'modelo_id',
        'salud_id',
        'tipo_id',
        'uuid',
        'rfid_code'
    ];
    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }
    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }
    public function salud()
    {
        return $this->belongsTo(Salud::class); // Asegúrate de tener el modelo Salud.php
    }
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class);
    }
    public function almacenes()
    {
        return $this->belongsToMany(Almacen::class, 'almacen_activos')
            ->withPivot('cantidad')
            ->withTimestamps();
    }
    public function almacen_activos()
    {
        return $this->hasMany(Almacen_activo::class);
    }
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
    public function prestamoActivo()
    {
        return $this->prestamos()->whereNull('fecha_devuelto')->first();
    }
}
