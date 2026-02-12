<?php

namespace App\Models\ModelosBasicos;

use App\Models\ModelosBasicos\Marca;
use App\Models\Activo;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    protected $fillable = [
        'modelo',
        'marca_id'
    ];
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
    //Un modelo tiene muchos activos asociados
    public function activos()
    {
        return $this->hasMany(Activo::class);
    }
}
