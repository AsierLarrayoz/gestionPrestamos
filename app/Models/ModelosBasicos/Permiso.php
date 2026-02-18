<?php

namespace App\Models\ModelosBasicos;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    // Si tu tabla en BD se llama 'permissions' (inglés) y el modelo 'Permiso' (español):
    protected $table = 'permissions';

    protected $fillable = ['name', 'label'];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'permission_role', 'permission_id', 'role_id');
    }
}
