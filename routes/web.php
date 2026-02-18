<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Controladores
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\RequestLogController;
use App\Http\Controllers\ControllersBasicos\AlmacenController;
use App\Http\Controllers\ControllersBasicos\MarcaController;
use App\Http\Controllers\ControllersBasicos\ModeloController;
use App\Http\Controllers\ControllersBasicos\TipoController;
use App\Http\Controllers\ControllersBasicos\NivelController;
use App\Http\Controllers\ControllersBasicos\SaludController;
use App\Http\Controllers\ControllersBasicos\EstadoController;
use App\Http\Controllers\ControllersBasicos\LectorController;
use App\Http\Controllers\ControllersBasicos\PermisoController;

// --- RUTAS PÚBLICAS ---
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});
Route::get('/lectores', [LectorController::class, 'index'])->name('lectores.index');
Route::get('/lectores/create', [LectorController::class, 'create'])->name('lectores.create');
Route::post('/lectores', [LectorController::class, 'store'])->name('lectores.store');
Route::get('/lectores/{id}/edit', [LectorController::class, 'edit'])->name('lectores.edit');
Route::put('/lectores/{id}', [LectorController::class, 'update'])->name('lectores.update');
Route::delete('/lectores/{lector}', [LectorController::class, 'destroy'])->name('lectores.destroy');


// --- SOLO USUARIOS LOGUEADOS ---
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // --- 1. LOGS DEL SISTEMA ---
    Route::middleware(['admin:logs.leer'])->group(function () {
        Route::get('/logs', [RequestLogController::class, 'index'])->name('logs.index');
    });

    // --- 2. USUARIOS Y CONFIGURACIÓN ---
    // Escritura (Crear, Editar, Borrar)
    Route::middleware(['admin:usuarios.escribir'])->group(function () {
        // Usuarios
        Route::get('/usuarios/create', [ConfiguracionController::class, 'create'])->name('configuracion.create');
        Route::post('/usuarios', [ConfiguracionController::class, 'store'])->name('configuracion.store');
        Route::get('/usuarios/{usuario}/edit', [ConfiguracionController::class, 'edit'])->name('configuracion.edit');
        Route::put('/usuarios/{usuario}', [ConfiguracionController::class, 'update'])->name('configuracion.update');
        Route::delete('/usuarios/{usuario}', [ConfiguracionController::class, 'destroy'])->name('configuracion.destroy');

        // Roles y Permisos
        Route::resource('permisos', PermisoController::class)->except(['index', 'show']);
    });
    // Lectura (Ver listado)
    Route::middleware(['admin:usuarios.leer'])->group(function () {
        Route::get('/usuarios', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
    });

    // --- 3. ALMACENES ---
    Route::middleware(['admin:almacenes.escribir'])->group(function () {
        Route::post('/almacenes', [AlmacenController::class, 'store'])->name('almacenes.store');
        Route::get('/almacenes/create', [AlmacenController::class, 'create'])->name('almacenes.create'); // Si tienes vista create
        Route::get('/almacenes/{almacen}/edit', [AlmacenController::class, 'edit'])->name('almacenes.edit');
        Route::put('/almacenes/{almacen}', [AlmacenController::class, 'update'])->name('almacenes.update');
        Route::delete('/almacenes/{almacen}', [AlmacenController::class, 'destroy'])->name('almacenes.destroy');
    });
    Route::middleware(['admin:almacenes.leer'])->group(function () {
        Route::resource('almacenes', AlmacenController::class)->only(['index', 'show']);
    });

    // --- 4. ACTIVOS ---
    Route::middleware(['admin:activos.escribir'])->group(function () {
        // Rutas explícitas para evitar conflictos con {id}
        Route::get('/activos/create', [ActivoController::class, 'create'])->name('activos.create');
        Route::post('/activos', [ActivoController::class, 'store'])->name('activos.store');
        Route::get('/activos/{activo}/edit', [ActivoController::class, 'edit'])->name('activos.edit');
        Route::put('/activos/{activo}', [ActivoController::class, 'update'])->name('activos.update');
        Route::delete('/activos/{activo}', [ActivoController::class, 'destroy'])->name('activos.destroy');

        // Recursos auxiliares (Marcas, Modelos, etc.)
        Route::resource('marcas', MarcaController::class);
        Route::resource('modelos', ModeloController::class);
        Route::resource('tipos', TipoController::class);
        Route::resource('niveles', NivelController::class);
        Route::resource('salud', SaludController::class);
        Route::resource('estados', EstadoController::class);

        // Rutas AJAX Quick Store
        Route::post('/marcas/quick-store', [ActivoController::class, 'quickStoreMarca'])->name('marcas.quickStore');
        Route::post('/modelos/quick-store', [ActivoController::class, 'quickStoreModelo'])->name('modelos.quickStore');
        Route::post('/tipos/quick-store', [ActivoController::class, 'quickStoreTipo'])->name('tipos.quickStore');
        Route::post('/salud/quick-store', [ActivoController::class, 'quickStoreSalud'])->name('salud.quickStore');

        // Carga dinámica (puede ser de lectura también, pero usualmente se usa al crear/editar)
        Route::get('/get-modelos/{id}', [ActivoController::class, 'getModelosByMarca'])->name('activos.getModelos');
    });
    Route::middleware(['admin:activos.leer'])->group(function () {
        Route::resource('activos', ActivoController::class)->only(['index', 'show']);
        Route::get('/activos/{id}/print-qr', [ActivoController::class, 'printQr'])->name('activos.print-qr');
    });

    // --- 5. PRÉSTAMOS ---
    Route::middleware(['admin:prestamos.escribir'])->group(function () {
        Route::get('/prestamos/create', [PrestamoController::class, 'create'])->name('prestamos.create');
        Route::post('/prestamos', [PrestamoController::class, 'store'])->name('prestamos.store');
        // Reservas
        Route::resource('reservas', ReservaController::class);
    });
    Route::middleware(['admin:prestamos.leer'])->group(function () {
        Route::get('prestamos/historial', [PrestamoController::class, 'historial'])->name('prestamos.historial');
        Route::get('/activos/check-scan', [PrestamoController::class, 'prestamoCantidad'])->name('activos.prestamoCantidad');
        Route::resource('prestamos', PrestamoController::class)->only(['index', 'show']);
    });

    // --- 6. INCIDENCIAS ---
    Route::middleware(['admin:incidencias.escribir'])->group(function () {
        Route::post('/incidencias', [IncidenciaController::class, 'store'])->name('incidencias.store');
        Route::put('/incidencias/{incidencia}', [IncidenciaController::class, 'update'])->name('incidencias.update');
        Route::delete('/incidencias/{incidencia}', [IncidenciaController::class, 'destroy'])->name('incidencias.destroy');

        // AJAX Quick Store
        Route::post('/niveles/quick-store', [IncidenciaController::class, 'quickStoreNivel'])->name('niveles.quick_store');
        Route::post('/estados/quick-store', [IncidenciaController::class, 'quickStoreEstado'])->name('estados.quick_store');
    });
    Route::middleware(['admin:incidencias.leer'])->group(function () {
        Route::resource('incidencias', IncidenciaController::class)->except(['store', 'update', 'destroy']);
    });
}); // Fin Middleware Auth

require __DIR__ . '/auth.php';
