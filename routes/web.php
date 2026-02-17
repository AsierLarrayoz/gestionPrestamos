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
use App\Http\Controllers\ReservaController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

// --- RUTAS PÚBLICAS / REDIRECCIÓN ---
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// --- RUTAS PROTEGIDAS (SOLO LOGUEADOS) ---
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // 1. MÓDULO DE USUARIOS Y CONFIGURACIÓN (Permiso: usuarios)
    Route::middleware(['admin:usuarios'])->group(function () {
        Route::resource('usuarios', ConfiguracionController::class)->names('configuracion');
        Route::resource('permisos', PermisoController::class); // Antes llamado 'roles'
    });

    // 2. MÓDULO DE ALMACENES (Permiso: almacenes)
    Route::middleware(['admin:almacenes'])->group(function () {
        Route::resource('almacenes', AlmacenController::class);
    });

    // 3. MÓDULO DE ACTIVOS E INVENTARIO (Permiso: activos)
    Route::middleware(['admin:activos'])->group(function () {
        Route::resource('activos', ActivoController::class);

        // Auxiliares de Activos
        Route::resource('marcas', MarcaController::class);
        Route::resource('modelos', ModeloController::class);
        Route::resource('tipos', TipoController::class);
        Route::resource('niveles', NivelController::class);
        Route::resource('salud', SaludController::class);
        Route::resource('estados', EstadoController::class);
        Route::get('/activos/{id}/print-qr', [ActivoController::class, 'printQr'])->name('activos.print-qr');

        // Rutas AJAX para modales en creación de activos
        Route::post('/marcas/quick-store', [ActivoController::class, 'quickStoreMarca'])->name('marcas.quickStore');
        Route::post('/modelos/quick-store', [ActivoController::class, 'quickStoreModelo'])->name('modelos.quickStore');
        Route::post('/tipos/quick-store', [ActivoController::class, 'quickStoreTipo'])->name('tipos.quickStore');
        Route::post('/salud/quick-store', [ActivoController::class, 'quickStoreSalud'])->name('salud.quickStore');

        // Carga dinámica de modelos
        Route::get('/get-modelos/{id}', [ActivoController::class, 'getModelosByMarca'])->name('activos.getModelos');
    });

    // 4. MÓDULO DE PRÉSTAMOS (Permiso: prestamos)
    Route::middleware(['admin:prestamos'])->group(function () {
        Route::get('prestamos/historial', [PrestamoController::class, 'historial'])->name('prestamos.historial');
        Route::resource('prestamos', PrestamoController::class)->except(['show', 'edit', 'update', 'destroy']);
        Route::get('/activos/check-scan', [PrestamoController::class, 'prestamoCantidad'])->name('activos.prestamoCantidad');
        Route::resource('reservas', ReservaController::class);
    });

    // 5. MÓDULO DE INCIDENCIAS (Permiso: incidencias)
    Route::middleware(['admin:incidencias'])->group(function () {
        Route::resource('incidencias', IncidenciaController::class);
        // Rutas AJAX para creación rápida en Incidencias
        Route::post('/niveles/quick-store', [App\Http\Controllers\IncidenciaController::class, 'quickStoreNivel'])->name('niveles.quick_store');
        Route::post('/estados/quick-store', [App\Http\Controllers\IncidenciaController::class, 'quickStoreEstado'])->name('estados.quick_store');
    });
});

// --- RUTA DE INSTALACIÓN (DESCOMENTAR SOLO SI ES NECESARIO) ---
/*
Route::get('/instalar-admin', function () {
    $permisosAdmin = Permiso::firstOrCreate([
        'permiso_usuarios'    => true,
        'permiso_activos'     => true,
        'permiso_almacenes'   => true,
        'permiso_incidencias' => true,
        'permiso_prestamos'   => true,
    ]);

    User::firstOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'name'        => 'Asier',
            'password'    => Hash::make('12345678'),
            'permisos_id' => $permisosAdmin->id
        ]
    );

    return "Admin instalado.";
});
*/

require __DIR__ . '/auth.php';
