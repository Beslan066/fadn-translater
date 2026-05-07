<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslatorController extends Controller
{
    public function index() {

        $translations = auth()->user()->translations()->get();

        return view('pages.translator.index', [
            'translations' => $translations,
        ]);
    }

    public function translations(Request $request)
    {
        $translations = auth()->user()->translations()
            ->whereIn('status', [
                Translation::STATUS_TRANSLATED,    // 1 - На проверке
                Translation::STATUS_PROOFREAD,     // 2 - Подтвержден
                Translation::STATUS_REJECTED,      // 3 - Отклонен
                Translation::STATUS_COMPLETED_BY_ADMIN // 4 - Завершен администратором
            ])
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('sentence', function($q) use ($request) {
                    $q->where('sentence', 'like', '%'.$request->search.'%');
                })
                    ->orWhere('translated_text', 'like', '%'.$request->search.'%');
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->with(['sentence', 'translator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('pages.translator.translations', [
            'translations' => $translations,
        ]);
    }

    public function dashboard()
    {
        $user = auth()->user();


        // 1. Ищем активный перевод пользователя
        $translation = $this->getActiveTranslation($user);

        // 2. Если нет - создаем новый
        if (!$translation) {
            $translation = $this->assignNewTranslation($user);
            if (!$translation) {
                return view('pages.translator.no-sentences');
            }
        }

        return view('pages.translator.sentence', [
            'translation' => $translation,
        ]);
    }

    protected function getActiveTranslation(User $user): ?Translation
    {
        return Translation::where('translator_id', $user->id)
            ->where('status', Translation::STATUS_ASSIGNED)
            ->where('locked_by', $user->id)
            ->where('locked_at', '>', now()->subHours(2))
            ->with('sentence')
            ->first();
    }

    protected function assignNewTranslation(User $user): ?Translation
    {
        return DB::transaction(function () use ($user) {
            $sentence = Sentence::whereDoesntHave('translations', function($q) use ($user) {
                $q->where('region_id', $user->region_id)
                    ->whereIn('status', [
                        Translation::STATUS_TRANSLATED,
                        Translation::STATUS_PROOFREAD,
                        Translation::STATUS_COMPLETED_BY_ADMIN
                    ]);
            })
                ->whereDoesntHave('translations', function($q) use ($user) {
                    // Проверяем активные назначения ДРУГИМ переводчикам
                    $q->where('region_id', $user->region_id)
                        ->where('status', Translation::STATUS_ASSIGNED)
                        ->where('locked_by', '!=', $user->id) // Назначено другому
                        ->where('locked_at', '>', now()->subHours(2)); // Блокировка активна
                })
                ->whereDoesntHave('translations', function($q) use ($user) {
                    // Проверяем, не назначено ли этому пользователю уже
                    $q->where('region_id', $user->region_id)
                        ->where('translator_id', $user->id)
                        ->where('status', Translation::STATUS_ASSIGNED);
                })
                ->first();

            if (!$sentence) {
                return null;
            }

            return $sentence->assignToRegion($user->region_id, $user);
        });
    }

    protected function getAvailableSentence(User $user): ?Sentence
    {
        return Sentence::where(function($query) use ($user) {
            // Нет завершенных переводов для региона
            $query->whereDoesntHave('translations', function($q) use ($user) {
                $q->where('region_id', $user->region_id)
                    ->whereIn('status', [
                        Translation::STATUS_TRANSLATED,
                        Translation::STATUS_PROOFREAD,
                        Translation::STATUS_COMPLETED_BY_ADMIN
                    ]);
            });

            // Нет активных назначений другим переводчикам
            $query->whereDoesntHave('translations', function($q) use ($user) {
                $q->where('region_id', $user->region_id)
                    ->where('status', Translation::STATUS_ASSIGNED)
                    ->where('translator_id', '!=', $user->id)
                    ->where('locked_at', '>', now()->subHours(2));
            });

            // Нет активного назначения этому переводчику
            $query->whereDoesntHave('translations', function($q) use ($user) {
                $q->where('region_id', $user->region_id)
                    ->where('translator_id', $user->id)
                    ->where('status', Translation::STATUS_ASSIGNED);
            });
        })
            ->where('status', Sentence::STATUS_AVAILABLE)
            ->first();
    }

    protected function getCurrentTranslation(User $user): ?Translation
    {
        return Translation::where('translator_id', $user->id)
            ->whereIn('status', [Translation::STATUS_ASSIGNED, Translation::STATUS_REJECTED])
            ->where('locked_by', $user->id)
            ->where('locked_at', '>', now()->subHours(2))
            ->first();
    }

    public function submitTranslation(Request $request, Translation $translation)
    {
        $request->validate(['translated_text' => 'required|string|min:5']);

        DB::transaction(function () use ($translation, $request) {
            $translation->update([
                'translated_text' => $request->translated_text,
                'status' => Translation::STATUS_TRANSLATED,
                'translated_at' => now(),
                'locked_by' => null,
                'locked_at' => null
            ]);

            $translation->sentence->update([
                'status' => Sentence::STATUS_TRANSLATED,
                'locked_by' => null,
                'locked_at' => null
            ]);
        });

        return redirect()->route('translator.dashboard');
    }

    public function skipSentence(Translation $translation)
    {
        DB::transaction(function () use ($translation) {
            $translation->update([
                'locked_by' => null,
                'locked_at' => null
            ]);
        });

        return redirect()->route('translator.dashboard');
    }
}
