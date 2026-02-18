<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ModelosBasicos\Permiso;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\ControllersBasicos\AlmacenController;
use App\Http\Controllers\ControllersBasicos\MarcaController;
use App\Http\Controllers\ControllersBasicos\ModeloController;
use App\Http\Controllers\ControllersBasicos\TipoController;
use App\Http\Controllers\ControllersBasicos\NivelController;
use App\Http\Controllers\ControllersBasicos\SaludController;
use App\Http\Controllers\ControllersBasicos\EstadoController;
use App\Http\Controllers\ControllersBasicos\PermisoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\RequestLogController;
use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

//RUTAS PÚBLICAS
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/error', function () {
    throw new \Exception('Test error 500');
});

//Solo para logueados
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // 
    Route::middleware(['admin:log_r'])->group(function () {
        Route::get('/logs', [RequestLogController::class, 'index'])->name('logs.index');
    });
    // USUARIOS Y CONFIGURACIÓN
    Route::middleware(['admin:usuarios_wr'])->group(function () {
        Route::post('/usuarios', [ConfiguracionController::class, 'store'])->name('configuracion.store');
        Route::put('/usuarios/{usuario}', [ConfiguracionController::class, 'update'])->name('configuracion.update');
        Route::delete('/usuarios/{usuario}', [ConfiguracionController::class, 'destroy'])->name('configuracion.destroy');

        Route::post('/permisos', [PermisoController::class, 'store'])->name('permisos.store');
        Route::put('/permisos/{permiso}', [PermisoController::class, 'update'])->name('permisos.update');
        Route::delete('/permisos/{permiso}', [PermisoController::class, 'destroy'])->name('permisos.destroy');
    });
    Route::middleware(['admin:usuarios_r'])->group(function () {
        Route::resource('usuarios', ConfiguracionController::class)->except('store', 'update', 'destroy')->names('configuracion');
        Route::resource('permisos', PermisoController::class)->except('store', 'update', 'destroy');
    });

    //ALMACENES
    Route::middleware(['admin:almacenes_wr'])->group(function () {
        Route::post('/almacenes', [AlmacenController::class, 'store'])->name('almacenes.store');
        Route::put('/almacenes/{almacene}', [AlmacenController::class, 'update'])->name('almacenes.update');
        Route::delete('/almacenes/{almacene}', [AlmacenController::class, 'destroy'])->name('almacenes.destroy');
    });
    Route::middleware(['admin:almacenes_r'])->group(function () {
        Route::resource('almacenes', AlmacenController::class)->except('store', 'update', 'delete');
    });

    //ACTIVOS
    Route::middleware(['admin:activos_wr'])->group(function () {
        Route::post('/activos', [ActivoController::class, 'store'])->name('activos.store');
        Route::put('/activos/{activo}', [ActivoController::class, 'update'])->name('activos.update');
        Route::delete('/activos/{activo}', [ActivoController::class, 'destroy'])->name('activos.destroy');

        Route::resource('marcas', MarcaController::class);
        Route::resource('modelos', ModeloController::class);
        Route::resource('tipos', TipoController::class);
        Route::resource('niveles', NivelController::class);
        Route::resource('salud', SaludController::class);
        Route::resource('estados', EstadoController::class);

        Route::post('/marcas/quick-store', [ActivoController::class, 'quickStoreMarca'])->name('marcas.quickStore');
        Route::post('/modelos/quick-store', [ActivoController::class, 'quickStoreModelo'])->name('modelos.quickStore');
        Route::post('/tipos/quick-store', [ActivoController::class, 'quickStoreTipo'])->name('tipos.quickStore');
        Route::post('/salud/quick-store', [ActivoController::class, 'quickStoreSalud'])->name('salud.quickStore');

        Route::get('/get-modelos/{id}', [ActivoController::class, 'getModelosByMarca'])->name('activos.getModelos');
    });
    Route::middleware(['admin:activos_r'])->group(function () {
        Route::resource('activos', ActivoController::class)->except('store', 'update', 'destroy');
        Route::get('/activos/{id}/print-qr', [ActivoController::class, 'printQr'])->name('activos.print-qr');
    });


    //PRÉSTAMOS
    Route::middleware(['admin:prestamos_wr'])->group(function () {
        Route::post('/prestamos', [PrestamoController::class, 'store'])->name('prestamos.store');
        Route::resource('reservas', ReservaController::class);
    });
    Route::middleware(['admin:prestamos_r'])->group(function () {
        Route::get('prestamos/historial', [PrestamoController::class, 'historial'])->name('prestamos.historial');
        Route::get('/activos/check-scan', [PrestamoController::class, 'prestamoCantidad'])->name('activos.prestamoCantidad');
        Route::resource('prestamos', PrestamoController::class)->except(['show', 'edit', 'update', 'destroy', 'store']);
    });


    //INCIDENCIAS
    Route::middleware(['admin:incidencias_wr'])->group(function () {
        Route::post('/incidencias', [IncidenciaController::class, 'store'])->name('incidencias.store');
        Route::delete('/incidencias/{incidencia}', [IncidenciaController::class, 'destroy'])->name('incidencias.destroy');
        Route::put('/incidencias/{incidencia}', [IncidenciaController::class, 'update'])->name('incidencias.update');
        Route::post('/niveles/quick-store', [App\Http\Controllers\IncidenciaController::class, 'quickStoreNivel'])->name('niveles.quick_store');
        Route::post('/estados/quick-store', [App\Http\Controllers\IncidenciaController::class, 'quickStoreEstado'])->name('estados.quick_store');
    });
    Route::middleware(['admin:incidencias_r'])->group(function () {
        Route::resource('incidencias', IncidenciaController::class)->except('store', 'update', 'destroy');
    });
});



require __DIR__ . '/auth.php';
