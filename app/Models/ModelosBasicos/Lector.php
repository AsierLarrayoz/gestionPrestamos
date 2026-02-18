<?php

namespace App\Models\ModelosBasicos;

use Illuminate\Database\Eloquent\Model;

class Lector extends Model
{
    protected $table = 'lectores';
    protected $fillable = [
        'nombre',
        'identificador_unico',
        'almacen_id',
        'tipo'
    ];
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
}
