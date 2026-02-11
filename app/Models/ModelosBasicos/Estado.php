<?php

namespace App\Models;

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
