<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanRegister
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Разрешаем регистрацию только ФАДН
        if ($request->user() && $request->user()->isFadn()) {
            return $next($request);
        }

        abort(403, 'Недостаточно прав для доступа к этой странице');
    }
}
