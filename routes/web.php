<?php

use App\Http\Controllers\ConfiguracionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('add',ConfiguracionController::)
