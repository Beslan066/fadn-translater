<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendingRegistrationController extends Controller
{
    public function index(Request $request)
    {
        // Если пользователь не авторизован - отправляем на логин
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Если пользователь активен - отправляем на главную
        if ($user->is_active) {
            return redirect()->route('home');
        }

        // Если пользователь неактивен - показываем страницу ожидания
        $region = Region::find($user->region_id);
        $regionAdmin = User::where('region_id', $user->region_id)
            ->where('role', 'region_admin')
            ->where('is_active', true)
            ->first();

        // Важно: возвращаем view, а не редирект
        return view('auth.pending', [
            'user' => $user,
            'region' => $region,
            'regionAdminEmail' => $regionAdmin?->email,
            'regionAdminName' => $regionAdmin?->name,
        ]);
    }
}
