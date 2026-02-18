<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LogRequestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('log_request_id', Str::uuid()->toString());
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($request->is('debugbar*') || $request->is('_debugbar*')) {
            return;
        }

        $payload = $request->all();
        $this->maskSensitiveData($payload);

        DB::table('request_logs')->insert([
            'request_id'   => $request->attributes->get('log_request_id'),
            'method'       => $request->method(),
            'url'          => Str::limit($request->fullUrl(), 2048),
            'user_id'      => Auth::id(),
            'ip'           => $request->ip(),
            'payload'      => json_encode($payload),
            'session_data' => json_encode($request->hasSession() ? $request->session()->all() : []),
            'status'       => $response->getStatusCode(),
            // --- CAMBIO AQUÍ: Extraemos detalles técnicos ---
            'error'        => $this->getExceptionDetails($response),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    /**
     * Extrae el mensaje de error técnico y la traza en lugar del HTML
     */
    private function getExceptionDetails(Response $response): ?string
    {
        if (!$response->isClientError() && !$response->isServerError()) {
            return null;
        }

        // Si Laravel adjuntó una excepción a la respuesta (común en 500)
        if (isset($response->exception) && $response->exception instanceof \Throwable) {
            $e = $response->exception;

            return sprintf(
                "Exception: %s\nMessage: %s\nFile: %s\nLine: %d\nTrace: %s",
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                Str::limit($e->getTraceAsString(), 1000)
            );
        }

        if ($response->getStatusCode() === 422) {
            return $response->getContent();
        }

        $content = $response->getContent();
        if (str_contains($content, '<html')) {
            return "Error " . $response->getStatusCode() . ": Se devolvió una página HTML de error (sin excepción capturable).";
        }

        return Str::limit($content, 2048);
    }

    private function maskSensitiveData(&$data)
    {
        $fields = ['password', 'password_confirmation', 'old_password', 'token', 'credit_card'];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '********';
            }
        }
    }
}
