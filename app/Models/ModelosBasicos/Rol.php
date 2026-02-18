<?php

namespace App\Models\ModelosBasicos;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name', 'label'];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'permission_role', 'role_id', 'permission_id');
    }

    public function givePermissionTo($permisos)
    {
        return $this->permisos()->syncWithoutDetaching($permisos);
    }
}
