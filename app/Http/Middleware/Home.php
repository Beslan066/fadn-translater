<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Home
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // ВАЖНО: Проверяем активность пользователя
        // Но пропускаем если текущий маршрут - registration.pending
        if (!$user->is_active && !$request->routeIs('registration.pending')) {
            return redirect()->route('registration.pending');
        }

        // Если пользователь активен, проверяем роли
        if ($user->is_active) {
            switch ($user->role) {
                case 'proofreader':
                    return redirect()->route('proofreader.dashboard');
                case 'translator':
                    return redirect()->route('translator.dashboard');
                case 'region_admin':
                    return redirect()->route('region-admin.index');
                case 'user':
                    return $next($request);
                case 'super_admin':
                case 'fadn':
                    return $next($request);
                default:
                    return abort(404);
            }
        }

        // Неактивные пользователи, которые пытаются зайти на registration.pending
        return $next($request);
    }
}
