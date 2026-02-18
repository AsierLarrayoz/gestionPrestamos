<?php

namespace App\Models\ModelosBasicos;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';
    protected $fillable = [
        'nombre_rol',
        'permiso_usuarios_wr',
        'permiso_activos_wr',
        'permiso_almacenes_wr',
        'permiso_incidencias_wr',
        'permiso_prestamos_wr',
        'permiso_reservas_wr',
        'permiso_usuarios_r',
        'permiso_activos_r',
        'permiso_almacenes_r',
        'permiso_incidencias_r',
        'permiso_prestamos_r',
        'permiso_reservas_r',
        'permiso_log_r'

    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
