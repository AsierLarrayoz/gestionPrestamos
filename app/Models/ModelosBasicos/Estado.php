<?php

namespace App\Models\ModelosBasicos;

use App\Models\Incidencia;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = [
        'estado'
    ];
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
}
