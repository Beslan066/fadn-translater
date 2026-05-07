<?php

namespace App\Http\Controllers;

use App\Models\Proofread;
use App\Models\Region;
use App\Models\Sentence;
use App\Models\Translation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionAdminController extends Controller
{

    public function home()
    {
        $region = Region::query()->where('id', auth()->user()->region_id)->first();

        // Статистика по переводам
        $translationStats = Translation::where('region_id', $region->id)
            ->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as assigned,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as translated,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as proofread,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected
        ', [
                Translation::STATUS_ASSIGNED,
                Translation::STATUS_TRANSLATED,
                Translation::STATUS_PROOFREAD,
                Translation::STATUS_REJECTED
            ])
            ->first();

        // Активные переводчики региона
        $translators = $region->translators()
            ->withCount(['translations as completed_translations' => function($query) use ($region) {
                $query->where('status', Translation::STATUS_PROOFREAD)
                    ->where('region_id', $region->id);
            }])
            ->orderBy('completed_translations', 'desc')
            ->get();

        // Активные корректоры региона
        $proofreaders = $region->proofreaders()
            ->withCount(['proofreadByMe as completed_proofreads' => function($query) use ($region) {
                $query->where('status', Translation::STATUS_PROOFREAD)
                    ->where('region_id', $region->id);
            }])
            ->orderBy('completed_proofreads', 'desc')
            ->get();

        $translatorsCount = $translators->count();
        $proofreadersCount = $proofreaders->count();

        // Топ переводчиков по завершенным переводам (STATUS_PROOFREAD) - исправленный запрос
        $topTranslators = User::where('role', 'translator')
            ->where('region_id', auth()->user()->region_id)
            ->whereHas('translations', function($query) {
                $query->where('status', Translation::STATUS_PROOFREAD);
            })
            ->withCount(['translations as completed_translations' => function($query) {
                $query->where('status', Translation::STATUS_PROOFREAD);
            }])
            ->orderBy('completed_translations', 'desc')
            ->take(5)
            ->get();

        // Топ корректоров по количеству проверенных предложений - исправленный запрос
        $topProofreaders = User::where('role', 'proofreader')
            ->where('region_id', auth()->user()->region_id)
            ->whereHas('proofreadByMe', function($query) {
                $query->where('status', Translation::STATUS_PROOFREAD);
            })
            ->withCount(['proofreadByMe as proofreads_count' => function($query) {
                $query->where('status', Translation::STATUS_PROOFREAD);
            }])
            ->orderBy('proofreads_count', 'desc')
            ->take(5)
            ->get();

        // Неподтвержденные пользователи
        $users = User::query()
            ->where('is_active', 0)
            ->where('region_id', auth()->user()->region_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.region-admin.index', [
            'proofreadersCount' => $proofreadersCount,
            'translatorsCount' => $translatorsCount,
            'topTranslators' => $topTranslators,
            'topProofreaders' => $topProofreaders,
            'users' => $users,
            'region' => $region,
            'translatedTranslations' => $translationStats->translated ?? 0,
            'completedTranslations' => $translationStats->proofread ?? 0,
            'rejectedTranslations' => $translationStats->rejected ?? 0,
            'translators' => $translators,
            'proofreaders' => $proofreaders,
        ]);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $regionId = $user->region_id;

        $request->validate([
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $query = Sentence::query()
            ->with(['translations' => function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            }]);

        // УЛУЧШЕННЫЙ ПОИСК
        if ($request->search) {
            $searchTerm = trim($request->search);
            $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);
            $searchTerm = preg_replace('/[^\p{L}\p{N}\s]/u', '', $searchTerm);

            $words = explode(' ', $searchTerm);
            $words = array_filter($words, function($word) {
                return mb_strlen(trim($word)) > 2;
            });

            if (!empty($words)) {
                $query->where(function($q) use ($words) {
                    foreach ($words as $word) {
                        $cleanWord = trim($word);
                        if (!empty($cleanWord)) {
                            $q->orWhere('sentence', 'LIKE', '%' . $cleanWord . '%');
                        }
                    }
                });
            }
        }

        // Фильтр по статусу - ИСПРАВЛЕННЫЙ
        if ($request->status) {
            switch ($request->status) {
                case 'not_started':
                    $query->whereDoesntHave('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId);
                    });
                    break;
                case 'assigned':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_ASSIGNED);
                    });
                    break;
                case 'translated':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_TRANSLATED);
                    });
                    break;
                case 'proofread':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_PROOFREAD);
                    });
                    break;
                case 'completed_by_admin':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_COMPLETED_BY_ADMIN);
                    });
                    break;
                case 'rejected':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_REJECTED);
                    });
                    break;
            }
        }

        $limit = min($request->limit ?? 20, 50);
        $sentences = $query->paginate($limit);

        // Добавляем computed атрибут для каждого предложения
        foreach ($sentences as $sentence) {
            $sentence->translationForRegion = $sentence->translations->first();
        }

        return view('pages.region-admin.sentences', [
            'sentences' => $sentences,
            'translationStatuses' => [
                'proofread' => Translation::STATUS_PROOFREAD,
                'translated' => Translation::STATUS_TRANSLATED,
                'completed_by_admin' => Translation::STATUS_COMPLETED_BY_ADMIN,
                'assigned' => Translation::STATUS_ASSIGNED,
                'rejected' => Translation::STATUS_REJECTED
            ],
            'filters' => $request->all(),
            'currentLimit' => $limit
        ]);
    }

    public function allTranslations(Request $request)
    {
        $user = auth()->user();
        $regionId = $user->region_id;
        $region = Region::find($regionId);

        $request->validate([
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        $query = Translation::query()
            ->with(['sentence', 'translator', 'proofreader'])
            ->where('region_id', $regionId)
            // Исключаем статус ASSIGNED (назначен)
            ->where('status', '!=', Translation::STATUS_ASSIGNED);

        // Фильтр по статусу (исключая ASSIGNED, но оставляем остальные)
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }

        // Фильтр по исполнителю (переводчику или корректору)
        if ($request->assigned_to) {
            $query->where(function($q) use ($request) {
                $q->where('translator_id', $request->assigned_to)
                    ->orWhere('proofreader_id', $request->assigned_to);
            });
        }

        // Поиск по тексту предложения или перевода
        if ($request->search) {
            $searchTerm = '%' . addcslashes($request->search, '%_') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->whereHas('sentence', function($q2) use ($searchTerm) {
                    $q2->where('sentence', 'LIKE', $searchTerm);
                })->orWhere('translated_text', 'LIKE', $searchTerm);
            });
        }

        // Фильтр по дате
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $limit = min($request->limit ?? 30, 100);
        $translations = $query->orderBy('created_at', 'desc')->paginate($limit);

        // Получаем список пользователей региона для фильтра
        $users = User::where('region_id', $regionId)
            ->whereIn('role', ['translator', 'proofreader'])
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        // Статистика по статусам (исключая ASSIGNED из статистики если нужно)
        $stats = [
            'total' => Translation::where('region_id', $regionId)->where('status', '!=', Translation::STATUS_ASSIGNED)->count(),
            Translation::STATUS_TRANSLATED => Translation::where('region_id', $regionId)->where('status', Translation::STATUS_TRANSLATED)->count(),
            Translation::STATUS_PROOFREAD => Translation::where('region_id', $regionId)->where('status', Translation::STATUS_PROOFREAD)->count(),
            Translation::STATUS_REJECTED => Translation::where('region_id', $regionId)->where('status', Translation::STATUS_REJECTED)->count(),
            Translation::STATUS_COMPLETED_BY_ADMIN => Translation::where('region_id', $regionId)->where('status', Translation::STATUS_COMPLETED_BY_ADMIN)->count(),
        ];

        // Кросс-платформенный расчет среднего времени перевода
        $avgTime = null;
        $translationsWithTime = Translation::where('region_id', $regionId)
            ->whereNotNull('translated_at')
            ->whereNotNull('assigned_at')
            ->get(['assigned_at', 'translated_at']);

        if ($translationsWithTime->count() > 0) {
            $totalHours = 0;
            foreach ($translationsWithTime as $trans) {
                $totalHours += $trans->assigned_at->diffInHours($trans->translated_at);
            }
            $avgTime = round($totalHours / $translationsWithTime->count(), 1);
        }

        return view('pages.region-admin.all-translations', [
            'translations' => $translations,
            'filters' => $request->all(),
            'currentLimit' => $limit,
            'users' => $users,
            'stats' => $stats,
            'region' => $region,
            'avgTime' => $avgTime,
            'statuses' => [
                // Убрал STATUS_ASSIGNED из списка статусов для фильтра
                Translation::STATUS_TRANSLATED => 'Переведен (ждет проверки)',
                Translation::STATUS_PROOFREAD => 'Проверен',
                Translation::STATUS_REJECTED => 'Отклонен',
                Translation::STATUS_COMPLETED_BY_ADMIN => 'Завершен админом',
            ],
            'statusColors' => [
                Translation::STATUS_TRANSLATED => 'warning',
                Translation::STATUS_PROOFREAD => 'success',
                Translation::STATUS_REJECTED => 'danger',
                Translation::STATUS_COMPLETED_BY_ADMIN => 'info',
            ]
        ]);
    }
    public function markAsCompleted(Request $request)
    {
        $request->validate([
            'sentence_id' => 'required|exists:sentences,id'
        ]);

        $user = auth()->user();
        $regionId = $user->region_id;

        DB::transaction(function () use ($request, $regionId, $user) {
            Translation::updateOrCreate(
                [
                    'sentence_id' => $request->sentence_id,
                    'region_id' => $regionId
                ],
                [
                    'status' => Translation::STATUS_COMPLETED_BY_ADMIN,
                    'proofreader_id' => $user->id,
                    'proofread_at' => now(),
                    'translated_text' => 'Помечено администратором как завершенное',
                    'locked_by' => null,
                    'locked_at' => null
                ]
            );
        });

        return back()->with('success', 'Предложение помечено как завершенное');
    }

    public function markAsAvailable(Request $request)
    {
        $request->validate([
            'sentence_id' => 'required|exists:sentences,id'
        ]);

        $user = auth()->user();
        $regionId = $user->region_id;

        DB::transaction(function () use ($request, $regionId) {
            Translation::where('sentence_id', $request->sentence_id)
                ->where('region_id', $regionId)
                ->delete();
        });

        return back()->with('success', 'Предложение снова доступно для перевода');
    }

    public function bulkComplete(Request $request)
    {
        $request->validate([
            'sentence_ids' => 'required|array',
            'sentence_ids.*' => 'exists:sentences,id'
        ]);

        $user = auth()->user();
        $regionId = $user->region_id;

        DB::transaction(function () use ($request, $regionId, $user) {
            foreach ($request->sentence_ids as $sentenceId) {
                Translation::updateOrCreate(
                    [
                        'sentence_id' => $sentenceId,
                        'region_id' => $regionId
                    ],
                    [
                        'status' => Translation::STATUS_COMPLETED_BY_ADMIN,
                        'proofreader_id' => $user->id,
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

    public function bulkMakeAvailable(Request $request)
    {
        $request->validate([
            'sentence_ids' => 'required|array',
            'sentence_ids.*' => 'exists:sentences,id'
        ]);

        $user = auth()->user();
        $regionId = $user->region_id;

        DB::transaction(function () use ($request, $regionId) {
            Translation::whereIn('sentence_id', $request->sentence_ids)
                ->where('region_id', $regionId)
                ->delete();
        });

        return back()->with('success', 'Выбранные предложения снова доступны для перевода');
    }


    public function users(Request $request)
    {
        $currentRegionId = auth()->user()->region_id;

        $users = User::query()
            ->with(['region', 'translations' => function($query) use ($currentRegionId) {
                $query->where('region_id', $currentRegionId);
            }])
            ->where('region_id', $currentRegionId) // Жесткая привязка к региону
            ->whereIn('role', ['translator', 'proofreader'])
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                        ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            })
            ->when($request->status === 'active', function($query) {
                $query->where('is_active', true)->whereNull('deleted_at');
            })
            ->when($request->status === 'inactive', function($query) {
                $query->where('is_active', false)->whereNull('deleted_at');
            })
            ->when($request->status === 'deleted', function($query) {
                $query->onlyTrashed();
            })
            ->when($request->role, function($query, $role) {
                $query->where('role', $role);
            })
            // Фильтры переводов только для переводчиков
            ->when($request->role === 'translator' && $request->translation_status, function($query) use ($request, $currentRegionId) {
                $query->whereHas('translations', function($q) use ($request, $currentRegionId) {
                    $q->where('status', $request->translation_status)
                        ->where('region_id', $currentRegionId);
                });
            })
            ->when($request->role === 'translator' && $request->translations_count === 'on_review', function($query) use ($currentRegionId) {
                $query->whereHas('translations', function($q) use ($currentRegionId) {
                    $q->where('status', Translation::STATUS_TRANSLATED)
                        ->where('region_id', $currentRegionId);
                });
            })
            ->when($request->role === 'translator' && $request->translations_count === 'approved', function($query) use ($currentRegionId) {
                $query->whereHas('translations', function($q) use ($currentRegionId) {
                    $q->where('status', Translation::STATUS_PROOFREAD)
                        ->where('region_id', $currentRegionId);
                });
            })
            ->when($request->role === 'translator' && $request->translations_count === 'rejected', function($query) use ($currentRegionId) {
                $query->whereHas('translations', function($q) use ($currentRegionId) {
                    $q->where('status', Translation::STATUS_REJECTED)
                        ->where('region_id', $currentRegionId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pages.region-admin.users', [
            'users' => $users,
            'filters' => $request->all(),
        ]);
    }

    public function otherSentences(Request $request)
    {
        $user = auth()->user();
        $regionId = $user->region_id;
        $region = Region::find($regionId);

        $request->validate([
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $query = Sentence::query()
            ->where('otherSentence', '>', 0)  // Только дополнительные предложения
            ->with(['translations' => function($q) use ($regionId) {
                $q->where('region_id', $regionId);
            }]);

        // Поиск
        if ($request->search) {
            $searchTerm = trim($request->search);
            $searchTerm = preg_replace('/\s+/', ' ', $searchTerm);
            $searchTerm = preg_replace('/[^\p{L}\p{N}\s]/u', '', $searchTerm);

            $words = explode(' ', $searchTerm);
            $words = array_filter($words, function($word) {
                return mb_strlen(trim($word)) > 2;
            });

            if (!empty($words)) {
                $query->where(function($q) use ($words) {
                    foreach ($words as $word) {
                        $cleanWord = trim($word);
                        if (!empty($cleanWord)) {
                            $q->orWhere('sentence', 'LIKE', '%' . $cleanWord . '%');
                        }
                    }
                });
            }
        }

        // Фильтр по статусу
        if ($request->status) {
            switch ($request->status) {
                case 'not_started':
                    $query->whereDoesntHave('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId);
                    });
                    break;
                case 'assigned':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_ASSIGNED);
                    });
                    break;
                case 'translated':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_TRANSLATED);
                    });
                    break;
                case 'proofread':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_PROOFREAD);
                    });
                    break;
                case 'completed_by_admin':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_COMPLETED_BY_ADMIN);
                    });
                    break;
                case 'rejected':
                    $query->whereHas('translations', function($q) use ($regionId) {
                        $q->where('region_id', $regionId)
                            ->where('status', Translation::STATUS_REJECTED);
                    });
                    break;
            }
        }

        // Фильтр по типу дополнительного предложения
        if ($request->other_type) {
            $query->where('otherSentence', $request->other_type);
        }

        $limit = min($request->limit ?? 20, 50);
        $sentences = $query->paginate($limit);

        // Добавляем перевод для каждого предложения
        foreach ($sentences as $sentence) {
            $sentence->translationForRegion = $sentence->translations->first();
        }

        // Статистика по типам дополнительных предложений (без фильтра по региону, так как предложения общие)
        $stats = [
            'total' => Sentence::where('otherSentence', '>', 0)->count(),
            'type1' => Sentence::where('otherSentence', 1)->count(),
            'type2' => Sentence::where('otherSentence', 2)->count(),
            'type3' => Sentence::where('otherSentence', 3)->count(),
        ];

        return view('pages.region-admin.other-sentences', [
            'sentences' => $sentences,
            'region' => $region,
            'stats' => $stats,
            'translationStatuses' => [
                'proofread' => Translation::STATUS_PROOFREAD,
                'translated' => Translation::STATUS_TRANSLATED,
                'completed_by_admin' => Translation::STATUS_COMPLETED_BY_ADMIN,
                'assigned' => Translation::STATUS_ASSIGNED,
                'rejected' => Translation::STATUS_REJECTED
            ],
            'filters' => $request->all(),
            'currentLimit' => $limit
        ]);
    }
}
