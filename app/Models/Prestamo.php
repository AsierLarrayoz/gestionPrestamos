<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelosBasicos\Almacen;

class Prestamo extends Model
{
    protected $fillable = [
        'fecha_prestado',
        'fecha_devuelto',
        'cantidad_prestada',
        'cantidad_devuelta',
        'descripcion',
        'activo_id',
        'user_id',
        'almacen_prestado_id',
        'almacen_devuelto_id'

    ];
    protected $casts = [
        'fecha_prestado' => 'datetime',
        'fecha_devuelto' => 'datetime',
    ];
    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function almacenPrestado()
    {
        return $this->belongsTo(Almacen::class, 'almacen_prestado_id');
    }
    public function almacenDevuelto()
    {
        return $this->belongsTo(Almacen::class, 'almacen_devuelto_id');
    }
}
