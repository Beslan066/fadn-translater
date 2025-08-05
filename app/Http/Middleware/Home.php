<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Home
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        switch ($user->role) {
            case 'proofreader':
                return redirect()->route('proofreader.index');
            case 'translator':
                return redirect()->route('translator.index');
            case 'region_admin':
                return redirect()->route('region-admin.index');
            case 'super_admin':
            case 'fadn':
                return $next($request); // Разрешаем доступ к запрошенному маршруту
            default:
                return abort(404);
        }
    }
}
