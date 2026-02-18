<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ModelosBasicos\Rol;     // Usamos tus modelos en español
use App\Models\ModelosBasicos\Permiso; // Usamos tus modelos en español

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definimos la lista de permisos del sistema (Nombre técnico => Nombre legible)
        $permisosBase = [
            // Usuarios
            ['name' => 'usuarios.leer',     'label' => 'Ver Usuarios'],
            ['name' => 'usuarios.escribir', 'label' => 'Crear/Editar Usuarios'],

            // Activos
            ['name' => 'activos.leer',      'label' => 'Ver Activos'],
            ['name' => 'activos.escribir',  'label' => 'Crear/Editar Activos'],

            // Almacenes
            ['name' => 'almacenes.leer',    'label' => 'Ver Almacenes'],
            ['name' => 'almacenes.escribir', 'label' => 'Gestionar Almacenes'],

            // Incidencias
            ['name' => 'incidencias.leer',    'label' => 'Ver Incidencias'],
            ['name' => 'incidencias.escribir', 'label' => 'Gestionar Incidencias'],

            // Préstamos
            ['name' => 'prestamos.leer',    'label' => 'Ver Préstamos'],
            ['name' => 'prestamos.escribir', 'label' => 'Gestionar Préstamos'],

            // Reservas
            ['name' => 'reservas.leer',     'label' => 'Ver Reservas'],
            ['name' => 'reservas.escribir', 'label' => 'Gestionar Reservas'],

            // Logs (El nuevo que añadimos)
            ['name' => 'logs.leer',         'label' => 'Ver Logs del Sistema'],
        ];

        // 2. Insertamos los permisos en la BBDD
        foreach ($permisosBase as $p) {
            Permiso::create($p);
        }

        // 3. Creamos el Rol de "Super Administrador"
        $rolAdmin = Rol::create([
            'name' => 'Super Administrador',
            'label' => 'Acceso Total al Sistema'
        ]);

        // 4. Le asignamos TODOS los permisos al rol de Admin
        $todosLosPermisos = Permiso::all();
        $rolAdmin->permisos()->sync($todosLosPermisos);

        // 5. Creamos tu Usuario Admin
        $user = User::create([
            'name' => 'Asier',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'), // Tu contraseña de siempre
        ]);

        // 6. Asignamos el Rol al Usuario (Usando la tabla pivote)
        $user->roles()->attach($rolAdmin->id);

        // --- OPCIONAL: Crear un Rol de prueba "Técnico" con menos permisos ---
        $rolTecnico = Rol::create(['name' => 'Técnico', 'label' => 'Solo Activos e Incidencias']);
        // Buscamos permisos específicos
        $permisosTecnico = Permiso::whereIn('name', ['activos.leer', 'activos.escribir', 'incidencias.leer', 'incidencias.escribir'])->get();
        $rolTecnico->permisos()->sync($permisosTecnico);

        echo "Base de datos inicializada correctamente con Roles y Permisos RBAC.\n";
    }
}
