<?php

namespace App\Models\ModelosBasicos;

use App\Models\ModelosBasicos\Modelo;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $fillable = [
        'marca'
    ];
    public function modelos()
    {
        return $this->hasMany(Modelo::class);
    }
}
