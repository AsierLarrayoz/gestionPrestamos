<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RequestLogController extends Controller
{
    public function index(Request $request)
    {
        $query = RequestLog::with('user')->orderBy('created_at', 'desc');

        $fecha = $request->input('fecha', Carbon::today()->format('Y-m-d'));
        $query->whereDate('created_at', $fecha);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            if ($request->status == 'error') {
                $query->where('status', '>=', 400);
            } else {
                $query->where('status', $request->status);
            }
        }

        $logs = $query->paginate(50)->withQueryString();
        $usuarios = User::orderBy('name')->get();

        return view('logs.index', compact('logs', 'usuarios', 'fecha'));
    }
}
