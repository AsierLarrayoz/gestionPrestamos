<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ModelosBasicos\Rol;

// Importamos todos los controladores
use App\Http\Controllers\HomeController; // <--- ¡IMPORTANTE añadir este!
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\SaludController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ConfiguracionController;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirigir la raíz al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Grupo protegido: Solo usuarios logueados pueden entrar aquí
Route::middleware(['auth'])->group(function () {

    // --- PANEL PRINCIPAL (Estadísticas en welcome) ---
    // Cambiamos la función anónima por el HomeController para que cargue los $stats
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Si quieres que la ruta /home también funcione, puedes dejarla así:
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // --- ACTIVOS ---
    Route::get('activos/modelos/{id}', [ActivoController::class, 'getModelosByMarca']);
    Route::resource('activos', ActivoController::class);

    // --- PRÉSTAMOS ---
    Route::get('prestamos/historial', [PrestamoController::class, 'historial'])->name('prestamos.historial');
    Route::resource('prestamos', PrestamoController::class)->except(['show', 'edit', 'update', 'destroy']);

    // --- INCIDENCIAS ---
    Route::resource('incidencias', IncidenciaController::class);

    // --- GESTIÓN DE ALMACENES ---
    Route::resource('almacenes', AlmacenController::class);

    // --- CONFIGURACIÓN (Usuarios y Roles) ---
    Route::resource('usuarios', ConfiguracionController::class)->names('configuracion');
    Route::resource('roles', RolController::class);

    // --- TABLAS MAESTRAS ---
    Route::resource('marcas', MarcaController::class);
    Route::resource('modelos', ModeloController::class);
    Route::resource('tipos', TipoController::class);
    Route::resource('niveles', NivelController::class);
    Route::resource('salud', SaludController::class);
    Route::resource('estados', EstadoController::class);
});
Route::get('/instalar-admin', function () {
    // 1. Creamos el rol de Administrador si no existe
    $rol = Rol::firstOrCreate(['rol' => 'Administrador']);
    Rol::firstOrCreate(['rol' => 'Trabajador']);

    // 2. Creamos tu usuario vinculado a ese rol
    $user = User::firstOrCreate(
        ['email' => 'admin@admin.com'], // Busca por email
        [
            'name'     => 'Asier Admin',
            'password' => Hash::make('12345678'), // Tu contraseña
            'rol_id'   => $rol->id
        ]
    );

    return "Usuario creado correctamente. Email: admin@admin.com | Pass: 12345678. YA PUEDES BORRAR ESTA RUTA.";
});

// Rutas de autenticación de Breeze (Login, Password Reset, etc.)
require __DIR__ . '/auth.php';
