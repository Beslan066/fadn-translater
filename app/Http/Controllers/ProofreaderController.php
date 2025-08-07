<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProofreaderController extends Controller
{

    public function index()
    {

    }

    public function translations()
    {
        $translations = auth()->user()->proofreadByMe()->paginate(10);

        return view('pages.proofreader.translations', [
            'translations' => $translations,
        ]);
    }

    public function dashboard()
    {
        $proofreadByMe = auth()->user()->proofreadByMe()->get();

        $translations = Translation::where('region_id', auth()->user()->region_id)
            ->where('status', Translation::STATUS_TRANSLATED)
            ->with(['sentence', 'translator'])
            ->paginate(10);

        return view('pages.proofreader.dashboard',
            [
                'proofreadByMe' => $proofreadByMe,
                'translations' => $translations,
            ]
        );
    }

    public function reviewTranslation(Request $request, Translation $translation)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'proofread_text' => 'required_if:action,approve',
            'reject_reason' => 'required_if:action,reject'
        ]);

        DB::transaction(function () use ($translation, $request) {
            if ($request->action === 'approve') {
                $translation->update([
                    'proofread_text' => $request->proofread_text,
                    'status' => Translation::STATUS_PROOFREAD,
                    'proofreader_id' => auth()->id(),
                    'proofread_at' => now()
                ]);

                // Обновляем статус предложения
                $translation->sentence->update([
                    'status' => Sentence::STATUS_PROOFREAD
                ]);
            } else {
                $translation->update([
                    'status' => Translation::STATUS_REJECTED,
                    'proofreader_id' => auth()->id(),
                    'rejected_at' => now(),
                    'reject_reason' => $request->reject_reason
                ]);

                // Возвращаем предложение в доступные
                $translation->sentence->update([
                    'status' => Sentence::STATUS_AVAILABLE,
                    'locked_by' => null,
                    'locked_at' => null
                ]);
            }
        });

        return back()->with('success', 'Решение сохранено!');
    }
}
