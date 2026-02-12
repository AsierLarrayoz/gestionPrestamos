<?php

namespace App\Models\ModelosBasicos;

use App\Models\Incidencia;

use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    protected $table = 'niveles';
    protected $fillable = [
        'nivel'
    ];
    public function incidencias()
    {
        return $this->hasMany(Incidencia::class);
    }
}
