<?php

namespace App\Models\ModelosBasicos;

use Illuminate\Database\Eloquent\Model;

class Lector extends Model
{
    protected $fillable = [
        'estado'
    ];
    public function incidencias()
    {
        return $this->belongsTo(Almacen::class);
    }
}
