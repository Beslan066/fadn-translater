<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSentenceBatchJob;
use App\Models\Sentence;
use App\Models\Statistic;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class SentenceController extends Controller
{


    public function index(Request $request)
    {
        $search = $request->input('search');

        $sentences = Sentence::when($search, function($query) use ($search) {
            return $query->where('sentence', 'like', '%'.$search.'%');
        })->paginate(20);

        return view('pages.sentences.index', [
            'sentences' => $sentences,
            'search' => $search
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:txt|max:307200',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $chunkSize = 1000;
        $totalLines = 0;

        if (($handle = fopen($filePath, 'r')) !== false) {
            try {
                DB::beginTransaction();

                $batch = Bus::batch([])->dispatch();
                $currentBatch = [];

                while (($line = fgets($handle)) !== false) {
                    $trimmedLine = trim($line);

                    if (!empty($trimmedLine)) {
                        $currentBatch[] = $trimmedLine;

                        if (count($currentBatch) >= $chunkSize) {
                            $batch->add(new ProcessSentenceBatchJob($currentBatch));
                            $currentBatch = [];
                        }

                        $totalLines++;
                    }
                }

                if (count($currentBatch) > 0) {
                    $batch->add(new ProcessSentenceBatchJob($currentBatch));
                }

                fclose($handle);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Файл отправлен в очередь на обработку.',
                    'total_lines' => $totalLines,
                    'batch_id' => $batch->id,
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Ошибка при загрузке файла: ' . $e->getMessage(), [
                    'stack' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка обработки файла: ' . $e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Не удалось открыть файл.',
        ], 500);
    }

    public function getRandomSentence(Request $request)
    {
        $user = Auth::user();
        $regionId = $user->region_id;

        if (Session::has('current_sentence_id')) {
            $sentenceId = Session::get('current_sentence_id');
            $sentence = Sentence::find($sentenceId);

            if (!$sentence || $sentence->status != 'pending' || $sentence->locked_by != $user->id) {
                Session::forget('current_sentence_id');
                $sentence = null;
            }
        }

        if (isset($sentence)) {
            DB::transaction(function () use (&$sentence, $user, $regionId) {
                $sentence = Sentence::where('status', 'pending')
                    ->whereNull('locked_by')
                    ->whereDoesntHave('translations', function($query) use ($regionId) {
                        $query->where('region_id', $regionId);
                    })
                    ->inRandomOrder()
                    ->first();

                if ($sentence) {
                    $sentence->update(['locked_by' => $user->id]);
                    Session::put('current_sentence_id', $sentence->id);
                }
            });
        }

        return view('sentences.random', [
            'sentence' => $sentence,
            'region_id' => $regionId,
            'user' => $user,
        ]);
    }

    public function getRegionalStatistics(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'fadn') {
            $statistics = Statistic::with('region')
                ->orderBy('record_date', 'desc')
                ->get()
                ->groupBy('region_id');
        } else {
            $statistics = Statistic::where('region_id', $user->region_id)
                ->orderBy('record_date', 'desc')
                ->get();
        }

        return Inertia::render('Dashboard', [
            'statistics' => $statistics,
            'auth' => [
                'user' => $user,
            ],
        ]);
    }

    public function getRegionalTranslations(Request $request)
    {
        $user = Auth::user();
        $query = Translation::with(['sentence', 'user', 'proofreads.user'])
            ->orderBy('created_at', 'desc');

        if ($user->role !== 'fadn') {
            $query->where('region_id', $user->region_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $translations = $query->paginate(10);

        return view('pages.sentences.translations', [
            'translations' => $translations,
        ]);
    }

}
