<?php

namespace App\Models\ModelosBasicos;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';
    protected $fillable = [
        'permiso_usuarios',
        'permiso_activos',
        'permiso_almacenes',
        'permiso_incidencias',
        'permiso_prestamos'
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
