<?php

namespace App\Http\Controllers;

use App\Models\Sentence;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionAdminController extends Controller
{

    public function home()
    {
        return view('pages.region-admin.index');
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $regionId = $user->region_id;

        $query = Sentence::query()
            ->with(['translationForRegion' => function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            }])
            ->with('translations')
            ->when($request->search, function($q) use ($request) {
                $q->where('sentence', 'like', '%'.$request->search.'%');
            });

        $sentences = $query->paginate(50);

        return view('pages.region-admin.sentences', [
            'sentences' => $sentences,
            'statuses' => [
                'proofread' => Translation::STATUS_PROOFREAD, // 2
                'translated' => Translation::STATUS_TRANSLATED // 1
            ]
        ]);
    }

    public function markAsCompleted(Request $request)
    {
        $request->validate([
            'sentence_id' => 'required|exists:sentences,id',
            'status' => 'required|in:completed,available'
        ]);

        $user = auth()->user();
        $regionId = $user->region_id;

        DB::transaction(function () use ($request, $regionId) {
            if ($request->status === 'completed') {
                // Помечаем как переведенное
                Translation::updateOrCreate(
                    [
                        'sentence_id' => $request->sentence_id,
                        'region_id' => $regionId
                    ],
                    [
                        'status' => Translation::STATUS_PROOFREAD,
                        'proofreader_id' => auth()->id(),
                        'proofread_at' => now(),
                        'translated_text' => 'Помечено администратором как завершенное',
                        'locked_by' => null,
                        'locked_at' => null
                    ]
                );
            } else {
                // Возвращаем в доступные
                Translation::where('sentence_id', $request->sentence_id)
                    ->where('region_id', $regionId)
                    ->delete();
            }
        });

        return back()->with('success', 'Статус предложения обновлен');
    }

    public function bulkComplete(Request $request)
    {
        $request->validate([
            'sentence_ids' => 'required|array',
            'sentence_ids.*' => 'exists:sentences,id'
        ]);

        $user = auth()->user();
        $regionId = $user->region_id;

        DB::transaction(function () use ($request, $regionId) {
            foreach ($request->sentence_ids as $sentenceId) {
                Translation::updateOrCreate(
                    [
                        'sentence_id' => $sentenceId,
                        'region_id' => $regionId
                    ],
                    [
                        'status' => Translation::STATUS_PROOFREAD,
                        'proofreader_id' => auth()->id(),
                        'proofread_at' => now(),
                        'translated_text' => 'Помечено администратором как завершенное',
                        'locked_by' => null,
                        'locked_at' => null
                    ]
                );
            }
        });

        return back()->with('success', 'Выбранные предложения помечены как завершенные');
    }
}
