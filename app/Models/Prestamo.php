<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable = [
        'fecha_prestado',
        'fecha_devuelto',
        'cantidad_prestada',
        'cantidad_devuelta',
        'descripcion',
        'activo_id',
        'user_id'
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
}
