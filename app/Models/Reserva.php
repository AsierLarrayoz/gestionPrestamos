<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelosBasicos\Tipo;

class Reserva extends Model
{
    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'cantidad',
        'descripcion',
        'tipo_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function tipo()
    {
        return $this->belongsTo(Tipo::class);
    }
}
