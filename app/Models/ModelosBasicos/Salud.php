<?php

namespace App\Models\ModelosBasicos;

use App\Models\Activo;

use Illuminate\Database\Eloquent\Model;

class Salud extends Model
{
    protected $table = 'salud';
    protected $fillable = [
        'salud'
    ];
    public function activos()
    {
        return $this->hasMany(Activo::class);
    }
}
