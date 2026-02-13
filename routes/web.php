<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ModelosBasicos\Rol;

// Importamos todos los controladores
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
use App\Http\Controllers\ControllersBasicos\RolController;
use App\Http\Controllers\ConfiguracionController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Buscamos por nombre si hay un término, si no, traemos todos
    $usuarios = User::with('rol')
        ->when($request->buscar, function ($query, $buscar) {
            return $query->where('name', 'LIKE', "%{$buscar}%");
        })
        ->paginate(4)
        ->withQueryString(); // Mantiene el filtro al cambiar de página

    return view('auth.select_profile', compact('usuarios'));
});

// Grupo protegido: Solo usuarios logueados pueden entrar aquí
Route::middleware(['auth'])->group(function () {

    // Cambiamos la función anónima por el HomeController para que cargue los $stats
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::middleware(['admin'])->group(function () {
        Route::resource('usuarios', ConfiguracionController::class)->names('configuracion');
        Route::resource('roles', RolController::class);
        Route::resource('almacenes', AlmacenController::class);
        Route::resource('marcas', MarcaController::class);
        Route::resource('modelos', ModeloController::class);
        Route::resource('tipos', TipoController::class);
        Route::resource('niveles', NivelController::class);
        Route::resource('salud', SaludController::class);
        Route::resource('estados', EstadoController::class);
        // Rutas de creación rápida (AJAX) para los modales
        Route::post('/marcas/quick-store', [ActivoController::class, 'quickStoreMarca'])->name('marcas.quickStore');
        Route::post('/modelos/quick-store', [ActivoController::class, 'quickStoreModelo'])->name('modelos.quickStore');
        Route::post('/tipos/quick-store', [ActivoController::class, 'quickStoreTipo'])->name('tipos.quickStore');
        Route::post('/salud/quick-store', [ActivoController::class, 'quickStoreSalud'])->name('salud.quickStore');

        // Ruta para cargar modelos según marca
        Route::get('/get-modelos/{id}', [ActivoController::class, 'getModelosByMarca'])->name('activos.getModelos');
    });
    Route::get('activos/modelos/{id}', [ActivoController::class, 'getModelosByMarca']);
    Route::resource('activos', ActivoController::class);
    Route::get('prestamos/historial', [PrestamoController::class, 'historial'])->name('prestamos.historial');
    Route::resource('prestamos', PrestamoController::class)->except(['show', 'edit', 'update', 'destroy']);
    Route::resource('incidencias', IncidenciaController::class);
});

Route::get('/seleccion-perfil', function () {
    // Obtenemos los usuarios y sus roles para mostrarlos
    $usuarios = \App\Models\User::with('rol')->get();
    return view('auth.select_profile', compact('usuarios'));
})->name('profile.select');

/*Route::get('/instalar-admin', function () {
    // 1. Creamos el rol de Administrador si no existe
    $rol = Rol::firstOrCreate(['rol' => 'Administrador']);
    //$rol = Rol::firstOrCreate(['rol' => 'Trabajador']);

    // 2. Creamos tu usuario vinculado a ese rol
    $user = User::firstOrCreate(
        ['email' => 'admin@admin.com'], // Busca por email
        [
            'name'     => 'Asier ',
            'password' => Hash::make('12345678'), // Tu contraseña
            'rol_id'   => $rol->id
        ]
    );

    return "Usuario creado correctamente. Email: admin@admin.com | Pass: 12345678. YA PUEDES BORRAR ESTA RUTA.";
});*/

// Rutas de autenticación de Breeze (Login, Password Reset, etc.)
require __DIR__ . '/auth.php';
