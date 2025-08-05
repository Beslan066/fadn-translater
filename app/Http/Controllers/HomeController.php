<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Sentence;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {

        // Сводка
        $sentenceCount = Sentence::count();
        $usersCount = User::count();
        $translatesCount = Translation::query()->where('status', 2)->count();
        $regionsCount = Region::query()->where('is_active', 1)->count();

        // Пользователи с большим количеством переводов
        $topTranslators = User::withCount(['translations as translated_count' => function ($query) {
            $query->where('status', Translation::STATUS_PROOFREAD);
        }])
            ->orderByDesc('translated_count')
            ->take(5)
            ->get();

        // Топ регионы

        $topRegions = Region::withCount(['translations as translated_count' => function ($query) {
            $query->where('status', Translation::STATUS_PROOFREAD);
        }])
            ->whereNot('name', 'ФАДН')
            ->orderByDesc('translated_count')
            ->take(5)
            ->get();

        // Статисктика предложений

        $completedTranslations = Translation::query()->where('status', 2)->count();
        $inProgressTranslations = Translation::query()->where('status', 1)->count();
        $rejectedTranslations = Translation::query()->where('status', 3)->count();

        // Руководители регионов
        $supervisors = User::query()
            ->where('role', 'region_admin')
            ->where('is_active', 1)
            ->paginate(10);

        return view('welcome', [
            'supervisors' => $supervisors,
            'sentenceCount' => $sentenceCount,
            'usersCount' => $usersCount,
            'translatesCount' => $translatesCount,
            'regionsCount' => $regionsCount,
            'topTranslators' => $topTranslators,
            'topRegions' => $topRegions,
            'completedTranslations' => $completedTranslations,
            'inProgressTranslations' => $inProgressTranslations,
            'rejectedTranslations' => $rejectedTranslations,
        ]);
    }

    public function layout() {

        $authUser = auth()->user();
        return view('layouts.main', [
            'authUser' => $authUser,
        ]);
    }
}
