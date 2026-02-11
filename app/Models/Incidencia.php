<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $fillable = [
        'descripcion',
        'fecha_incidencia',
        'estado_id',
        'nivel_id',
        'user_id',
        'prestamo_id',
        'activo:id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }
    public function activo()
    {
        return $this->belongsTo(Activo::class);
    }
}
