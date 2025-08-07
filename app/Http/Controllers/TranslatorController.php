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
            ->when($request->search, function($query) use ($request) {
                $query->where('translated_text', 'like', '%'.$request->search.'%');
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
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
            ->whereIn('status', [Translation::STATUS_ASSIGNED, Translation::STATUS_REJECTED])
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
                        Translation::STATUS_PROOFREAD
                    ]);
            })
                ->whereDoesntHave('translations', function($q) use ($user) {
                    $q->where('region_id', $user->region_id)
                        ->where('translator_id', $user->id)
                        ->where('status', Translation::STATUS_ASSIGNED);
                })
                ->first();

            if (!$sentence) {
                return null;
            }

            // 2. Создаем/обновляем перевод для региона
            return $sentence->assignToRegion($user->region_id, $user);
        });
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
