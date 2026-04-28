@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <!-- Скрывающийся блок фильтров -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ri-filter-3-line me-2"></i>
                        Фильтры
                        @if(request('status') || request('assigned_to') || request('search') || request('date_from') || request('date_to'))
                            <span class="badge bg-primary ms-2">Активны</span>
                        @endif
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleFiltersBtn">
                        <i class="ri-arrow-down-s-line" id="toggleIcon"></i>
                        <span id="toggleText">Показать фильтры</span>
                    </button>
                </div>
            </div>

            <div id="filtersContainer" style="display: none;">
                <div class="card-body">
                    <form method="GET" action="{{ route('region-admin.all-translations') }}" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="ri-stack-line me-1"></i>Статус
                            </label>
                            <select name="status" class="form-select">
                                <option value="">Все статусы</option>
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="ri-user-line me-1"></i>Исполнитель
                            </label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Все</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role === 'translator' ? 'Переводчик' : 'Корректор' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri-search-line me-1"></i>Поиск
                            </label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Поиск по предложению или переводу..."
                                   value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="ri-calendar-line me-1"></i>Дата от
                            </label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="ri-calendar-line me-1"></i>Дата до
                            </label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                <i class="ri-list-settings-line me-1"></i>Записей
                            </label>
                            <select name="limit" class="form-select">
                                <option value="10" {{ $currentLimit == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ $currentLimit == 20 ? 'selected' : '' }}>20</option>
                                <option value="30" {{ $currentLimit == 30 ? 'selected' : '' }}>30</option>
                                <option value="50" {{ $currentLimit == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $currentLimit == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-search-line"></i> Применить
                            </button>
                            <button type="reset" class="btn btn-outline-secondary" id="resetFiltersBtn">
                                <i class="ri-refresh-line"></i> Сбросить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Таблица переводов -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="ri-table-line me-2"></i>
                        Список переводов
                        <span class="text-muted fs-6 ms-2">(регион: {{ $region->name }})</span>
                    </h5>
                </div>
                <div class="text-muted">
                    <small>Найдено: {{ $translations->total() }} записей</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th width="50">ID</th>
                            <th width="30%">Предложение</th>
                            <th width="30%">Перевод</th>
                            <th>Переводчик</th>
                            <th>Корректор</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th width="60">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($translations as $translation)
                            <tr>
                                <td>{{ $translation->sentence_id }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $translation->sentence->sentence ?? 'N/A' }}">
                                        {{ Str::limit($translation->sentence->sentence ?? 'Предложение удалено', 100) }}
                                    </div>
                                    @if($translation->sentence && $translation->sentence->otherSentence)
                                        <span class="badge bg-secondary mt-1">Дополнительный</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $translation->translated_text ?? '—' }}">
                                        {{ Str::limit($translation->translated_text ?? '—', 100) }}
                                    </div>
                                    @if($translation->status == 3 && $translation->reject_reason)
                                        <span class="badge bg-danger mt-1">Причина: {{ Str::limit($translation->reject_reason, 50) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($translation->translator)
                                        <span class="badge bg-info">{{ $translation->translator->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($translation->proofreader)
                                        <span class="badge bg-success">{{ $translation->proofreader->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusText = $statuses[$translation->status] ?? 'Неизвестно';
                                        $statusColor = $statusColors[$translation->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span>
                                </td>
                                <td>{{ $translation->created_at ? $translation->created_at->format('d.m.Y') : '—' }}<br>
                                    <small class="text-muted">{{ $translation->created_at ? $translation->created_at->format('H:i') : '' }}</small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#translationModal{{ $translation->id }}"
                                            title="Просмотр деталей">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Модальное окно с деталями -->
                            <div class="modal fade" id="translationModal{{ $translation->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title">
                                                <i class="ri-file-info-line me-2"></i>
                                                Детали перевода #{{ $translation->id }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="fw-bold text-primary">
                                                    <i class="ri-chat-quote-line me-1"></i>Оригинальное предложение:
                                                </label>
                                                <div class="border rounded p-3 bg-light mt-2">
                                                    {{ $translation->sentence->sentence ?? 'Предложение удалено' }}
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold text-success">
                                                    <i class="ri-translate-2 me-1"></i>Перевод:
                                                </label>
                                                <div class="border rounded p-3 mt-2">
                                                    {{ $translation->translated_text ?? '—' }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="fw-bold text-info">
                                                        <i class="ri-user-line me-1"></i>Переводчик:
                                                    </label>
                                                    <div class="mt-1">
                                                        @if($translation->translator)
                                                            <span class="badge bg-info fs-6">{{ $translation->translator->name }}</span>
                                                            <small class="text-muted d-block mt-1">ID: {{ $translation->translator->id }}</small>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="fw-bold text-success">
                                                        <i class="ri-user-settings-line me-1"></i>Корректор:
                                                    </label>
                                                    <div class="mt-1">
                                                        @if($translation->proofreader)
                                                            <span class="badge bg-success fs-6">{{ $translation->proofreader->name }}</span>
                                                            <small class="text-muted d-block mt-1">ID: {{ $translation->proofreader->id }}</small>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="fw-bold">
                                                        <i class="ri-calendar-event-line me-1"></i>Создан:
                                                    </label>
                                                    <p class="mt-1">{{ $translation->created_at ? $translation->created_at->format('d.m.Y H:i:s') : '—' }}</p>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="fw-bold">
                                                        <i class="ri-time-line me-1"></i>Переведен:
                                                    </label>
                                                    <p class="mt-1">{{ $translation->translated_at ? $translation->translated_at->format('d.m.Y H:i:s') : '—' }}</p>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="fw-bold">
                                                        <i class="ri-checkbox-circle-line me-1"></i>Проверен:
                                                    </label>
                                                    <p class="mt-1">{{ $translation->proofread_at ? $translation->proofread_at->format('d.m.Y H:i:s') : '—' }}</p>
                                                </div>
                                            </div>
                                            @if($translation->reject_reason)
                                                <div class="mb-3">
                                                    <label class="fw-bold text-danger">
                                                        <i class="ri-alert-line me-1"></i>Причина отклонения:
                                                    </label>
                                                    <div class="border rounded p-3 bg-light mt-2">
                                                        {{ $translation->reject_reason }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if($translation->locked_by)
                                                <div class="mb-3">
                                                    <label class="fw-bold text-warning">
                                                        <i class="ri-lock-line me-1"></i>Блокировка:
                                                    </label>
                                                    <div class="border rounded p-3 bg-light mt-2">
                                                        Заблокирован пользователем ID: {{ $translation->locked_by }}
                                                        @if($translation->locked_at)
                                                            <br><small>{{ $translation->locked_at->format('d.m.Y H:i:s') }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="ri-close-line me-1"></i>Закрыть
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="ri-inbox-line fs-1"></i>
                                        <p class="mt-3 mb-0">Переводы не найдены</p>
                                        <small>Попробуйте изменить параметры фильтрации</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($translations->hasPages())
                <div class="card-footer">
                    {{ $translations->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table > :not(caption) > * > * {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .badge {
            font-weight: 500;
            padding: 0.35rem 0.65rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        /* Анимация для фильтров */
        #filtersContainer {
            overflow: hidden;
            transition: all 0.3s ease-out;
        }

        #filtersContainer.show {
            display: block !important;
        }

        /* Иконки в карточках статистики */
        .bg-opacity-10 {
            --bs-bg-opacity: 0.1;
        }

        /* Стили для модального окна */
        .modal-content {
            border: none;
            border-radius: 0.5rem;
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        /* Responsive таблица */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .badge {
                font-size: 0.7rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Управление видимостью фильтров с сохранением состояния
            const filtersContainer = document.getElementById('filtersContainer');
            const toggleBtn = document.getElementById('toggleFiltersBtn');
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleText = document.getElementById('toggleText');

            // Проверяем, есть ли активные фильтры
            const hasActiveFilters = {{ request('status') || request('assigned_to') || request('search') || request('date_from') || request('date_to') ? 'true' : 'false' }};

            // Загружаем сохраненное состояние из localStorage
            const savedState = localStorage.getItem('filtersVisibility');

            // Показываем фильтры если: есть активные фильтры ИЛИ сохранено состояние 'visible'
            if (hasActiveFilters || savedState === 'visible') {
                filtersContainer.style.display = 'block';
                toggleIcon.className = 'ri-arrow-up-s-line';
                toggleText.textContent = 'Скрыть фильтры';
                filtersContainer.classList.add('show');
            } else {
                filtersContainer.style.display = 'none';
                toggleIcon.className = 'ri-arrow-down-s-line';
                toggleText.textContent = 'Показать фильтры';
                filtersContainer.classList.remove('show');
            }

            // Обработчик кнопки переключения
            toggleBtn.addEventListener('click', function() {
                if (filtersContainer.style.display === 'none') {
                    filtersContainer.style.display = 'block';
                    toggleIcon.className = 'ri-arrow-up-s-line';
                    toggleText.textContent = 'Скрыть фильтры';
                    localStorage.setItem('filtersVisibility', 'visible');
                    setTimeout(() => {
                        filtersContainer.classList.add('show');
                    }, 10);
                } else {
                    filtersContainer.style.display = 'none';
                    toggleIcon.className = 'ri-arrow-down-s-line';
                    toggleText.textContent = 'Показать фильтры';
                    localStorage.setItem('filtersVisibility', 'hidden');
                    filtersContainer.classList.remove('show');
                }
            });

            // Кнопка сброса фильтров
            const resetBtn = document.getElementById('resetFiltersBtn');
            if (resetBtn) {
                resetBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = document.getElementById('filterForm');
                    const inputs = form.querySelectorAll('select, input');
                    inputs.forEach(input => {
                        if (input.type !== 'submit' && input.type !== 'reset') {
                            if (input.tagName === 'SELECT') {
                                input.value = '';
                            } else if (input.type === 'text' || input.type === 'date') {
                                input.value = '';
                            }
                        }
                    });
                    // Сабмитим форму после сброса
                    form.submit();
                });
            }
        });
    </script>
@endpush
