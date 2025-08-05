<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('region')
            ->withTrashed() // включаем удаленных пользователей
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.user.index', [
            'users' => $users,
        ]);
    }

    public function create() {

        $roles = User::ROLES;
        $regions = Region::all();
        $user = Auth::user();

        return view('pages.user.create', [
            'regions' => $regions,
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        //Обработка поля чекбокса
        $data['is_active'] = $request->has('is_active') ? 1 : 0;


        //Обработка аватара
        if ($request->hasFile('avatar')) {
            // Сохраняем в storage/app/public/avatars
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }


         //Только ФАДН может создавать администраторов регионов
        if ($data['role'] === 'region_admin' && !auth()->user()->isFadn() && auth()->user()->role !== 'super_admin') {
            abort(403, 'Недостаточно прав для создания администратора региона');
        }

        // Администраторы регионов могут создавать только переводчиков и корректоров
        if (auth()->user()->isRegionAdmin() &&
            !in_array($data['role'], ['translator', 'proofreader'])) {
            abort(403, 'Недостаточно прав для создания пользователя с этой ролью');
        }

        // Проверка что пользователь создается в своем регионе
        if (auth()->user()->isRegionAdmin() &&
            $data['region_id'] != auth()->user()->region_id) {
            abort(403, 'Нельзя создавать пользователей в других регионах');
        }

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        $this->authorizeView($user);
        return response()->json($user->load('region'));
    }

    public function  edit(User $user) {
        $roles = User::ROLES;
        $regions = Region::all();

        return view('pages.user.edit', [
            'regions' => $regions,
            'user' => $user,
            'roles' => $roles,
        ]);
    }
    public function update(UpdateRequest $request, User $user)
    {
        $data = $request->validated();

        // Обработка пароля (если не указан - оставляем старый)
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        // Обработка аватара
        if ($request->hasFile('avatar')) {
            // Удаляем старый аватар, если он есть
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }

            // Сохраняем новый аватар
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Пользователь успешно обновлен');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }

    public function stats(User $user)
    {
        $this->authorizeView($user);

        $stats = [
            'translations' => $user->translations()->count(),
            'proofreads' => $user->proofreads()->count(),
            'approved_translations' => $user->translations()
                ->where('status', 'approved')
                ->count(),
        ];

        return response()->json($stats);
    }

    protected function authorizeView(User $user)
    {
        // ФАДН видит всех
        if (auth()->user()->isFadn()) return;

        // Администратор региона видит только пользователей своего региона
        if (auth()->user()->isRegionAdmin() &&
            $user->region_id === auth()->user()->region_id) return;

        // Пользователь видит только себя
        if ($user->id === auth()->id()) return;

        abort(403, 'Недостаточно прав для просмотра этого пользователя');
    }

    protected function validateRoleChange(User $user, string $newRole)
    {
        // Только ФАДН может назначать администраторов регионов
        if ($newRole === 'region_admin' && !auth()->user()->isFadn()) {
            abort(403, 'Недостаточно прав для назначения этой роли');
        }

        // Администратор региона не может назначать администраторов
        if (auth()->user()->isRegionAdmin() &&
            in_array($newRole, ['fadn', 'region_admin'])) {
            abort(403, 'Недостаточно прав для назначения этой роли');
        }

        // Нельзя понижать роль администратора региона
        if ($user->isRegionAdmin() && $newRole !== 'region_admin' && !auth()->user()->isFadn()) {
            abort(403, 'Нельзя изменить роль администратора региона');
        }
    }

    protected function validateRegionChange(User $user, int $newRegionId)
    {
        // ФАДН может перемещать между регионами
        if (auth()->user()->isFadn()) return;

        // Администратор региона может перемещать только в своем регионе (фактически не может)
        if (auth()->user()->isRegionAdmin() &&
            $newRegionId !== auth()->user()->region_id) {
            abort(403, 'Нельзя перемещать пользователей в другие регионы');
        }
    }

    public function archive(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'Пользователь успешно архивирован');
    }

    public function restore($id)
    {
        User::withTrashed()->where('id', $id)->restore();
        return redirect()->back()->with('success', 'Пользователь успешно восстановлен');
    }

    public function forceDelete($id)
    {
        User::withTrashed()->where('id', $id)->forceDelete();
        return redirect()->back()->with('success', 'Пользователь полностью удален');
    }
}
