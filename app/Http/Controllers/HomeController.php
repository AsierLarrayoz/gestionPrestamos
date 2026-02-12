<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;

class HomeController extends Controller
{

    public function index()
    {
        $stats = [
            'ultimos_prestamos'  => Prestamo::with(['activo.modelo', 'usuario'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('welcome', compact('stats'));
    }
}
