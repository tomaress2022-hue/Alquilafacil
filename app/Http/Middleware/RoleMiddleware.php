<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Se usa $request->user() en lugar de auth()->user() para que funcione
        // correctamente tanto con el guard 'web' (sesión) como con 'sanctum' (API).
        if (!$request->user() || $request->user()->role !== $role) {
            if ($request->expectsJson() || $request->is('api/*')) {
                abort(403, 'No tienes permisos para acceder a este recurso.');
            }
            abort(403);
        }

        return $next($request);
    }
}