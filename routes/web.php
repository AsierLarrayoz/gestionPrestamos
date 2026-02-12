<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Importamos todos los controladores
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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirigir la raíz al login o al home
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de Autenticación (Login, Logout, Reset Password)
// 'register' => false impide que cualquiera se registre desde fuera.
// Solo el admin crea usuarios desde Configuración.

// Grupo protegido: Solo usuarios logueados pueden entrar aquí
Route::middleware(['auth'])->group(function () {

    // Panel Principal (Dashboard)
    Route::get('/home', function () {
        return view('dashboard');
    })->name('home');

    // --- ACTIVOS ---
    // Ruta AJAX para cargar modelos dinámicamente al seleccionar marca
    Route::get('activos/modelos/{id}', [ActivoController::class, 'getModelosByMarca']);
    Route::resource('activos', ActivoController::class);

    // --- PRÉSTAMOS ---
    // IMPORTANTE: La ruta 'historial' debe ir ANTES del resource para que no confunda 'historial' con un ID.
    Route::get('prestamos/historial', [PrestamoController::class, 'historial'])->name('prestamos.historial');
    Route::resource('prestamos', PrestamoController::class)->except(['show', 'edit', 'update', 'destroy']);
    // Nota: Si en el futuro necesitas editar préstamos, quita el 'except'.

    // --- INCIDENCIAS ---
    Route::resource('incidencias', IncidenciaController::class);

    // --- GESTIÓN DE ALMACENES ---
    Route::resource('almacenes', AlmacenController::class);

    // --- CONFIGURACIÓN (Usuarios y Roles) ---
    // Usamos 'users' en la URL pero el controlador es ConfiguracionController
    Route::resource('usuarios', ConfiguracionController::class)->names('configuracion');
    Route::resource('roles', RolController::class);

    // --- TABLAS MAESTRAS (Auxiliares) ---
    // Estas rutas asumen que tienes controladores estándar (como el de Almacén) para cada uno.
    Route::resource('marcas', MarcaController::class);
    Route::resource('modelos', ModeloController::class);
    Route::resource('tipos', TipoController::class);
    Route::resource('niveles', NivelController::class);
    Route::resource('salud', SaludController::class); // Salud es singular en tu modelo
    Route::resource('estados', EstadoController::class);
});

require __DIR__ . '/auth.php';
