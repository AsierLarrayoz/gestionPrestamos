<?php

namespace App\Models;

use App\Models\ModelosBasicos\Permiso;
use App\Models\ModelosBasicos\Rol;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Prestamo;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'user_id');
    }

    // --- SISTEMA DE ROLES Y PERMISOS ---

    // Relación con Roles
    // Especificamos 'role_user' si usaste la migración anterior en inglés.
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'role_user', 'user_id', 'role_id');
    }

    // Relación con Permisos Directos
    // Especificamos 'permission_user'
    public function permissions() // Puedes renombrar esto a 'permisos' si prefieres todo en español
    {
        return $this->belongsToMany(Permiso::class, 'permission_user', 'user_id', 'permission_id');
    }

    /**
     * Valida si el usuario tiene acceso
     */
    public function hasPermission($permissionName)
    {
        // 1. Super Admin (ID 1) o Rol 'admin'
        if ($this->id === 1 || $this->hasRole('Super Administrador')) {
            return true;
        }

        // 2. Permisos directos (usamos la relación definida arriba)
        if ($this->permissions->contains('name', $permissionName)) {
            return true;
        }

        // 3. Permisos a través de roles
        foreach ($this->roles as $role) {
            if ($role->permisos->contains('name', $permissionName)) {
                return true;
            }
        }

        return false;
    }

    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }
}
