<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Response;

class ThrottleRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $key = 'register:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            
            // Verificar si es una petición fetch que espera JSON
            if ($request->expectsJson()) {
                $response = [
                    'ok' => false,
                    'message' => "Demasiados intentos de registro. Intenta nuevamente en {$seconds} segundos.",
                    'errors' => null,
                    'data' => null
                ];
                return response()->json($response, 429); // 429 Too Many Requests
            }
            
            // Para peticiones tradicionales (fallback)
            return back()->withErrors([
                'throttle' => "Demasiados intentos de registro. Intenta nuevamente en {$seconds} segundos."
            ])->withInput();
        }
        
        RateLimiter::hit($key, 30); // 30 segundos
        
        return $next($request);
    }
}
