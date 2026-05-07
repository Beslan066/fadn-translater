@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <!-- Компактный блок фильтрации -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('region-admin.sentences') }}" class="row g-3 align-items-end" id="filterForm">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ri-search-line me-1"></i> Поиск
                        </label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Поиск по предложению..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ri-stack-line me-1"></i> Статус
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Все статусы</option>
                            <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Не начато</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Назначены</option>
                            <option value="translated" {{ request('status') == 'translated' ? 'selected' : '' }}>На проверке</option>
                            <option value="proofread" {{ request('status') == 'proofread' ? 'selected' : '' }}>Завершены (корректором)</option>
                            <option value="completed_by_admin" {{ request('status') == 'completed_by_admin' ? 'selected' : '' }}>Завершены (администратором)</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Отклонены</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">
                            <i class="ri-list-settings-line me-1"></i> Записей
                        </label>
                        <select name="limit" class="form-select">
                            <option value="10" {{ $currentLimit == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ $currentLimit == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ $currentLimit == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $currentLimit == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-search-line"></i> Применить
                            </button>
                            <a href="{{ route('region-admin.sentences') }}" class="btn btn-outline-secondary">
                                <i class="ri-refresh-line"></i> Сброс
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(request('search') || request('status'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="ri-information-line me-2"></i>
                Применены фильтры:
                @if(request('search')) поиск "<strong>{{ request('search') }}</strong>" @endif
                @if(request('status'))
                    @php
                        $statusLabels = [
                            'not_started' => 'Не начато',
                            'assigned' => 'Назначены',
                            'translated' => 'На проверке',
                            'proofread' => 'Завершены (корректором)',
                            'completed_by_admin' => 'Завершены (администратором)',
                            'rejected' => 'Отклонены'
                        ];
                    @endphp
                    статус "<strong>{{ $statusLabels[request('status')] ?? request('status') }}</strong>"
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="window.location.href='{{ route('region-admin.sentences') }}'"></button>
            </div>
        @endif

        <!-- Форма массовых действий -->
        <form method="POST" action="{{ route('region-admin.bulk-complete') }}" id="bulk-complete-form">
            @csrf
            <input type="hidden" name="sentence_ids" id="bulk-complete-ids">
        </form>

        <form method="POST" action="{{ route('region-admin.bulk-make-available') }}" id="bulk-available-form">
            @csrf
            <input type="hidden" name="sentence_ids" id="bulk-available-ids">
        </form>

        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="submitBulkForm('complete')">
                        <i class="ri-checkbox-circle-line me-1"></i> Пометить выбранные как завершенные
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="submitBulkForm('available')">
                        <i class="ri-refresh-line me-1"></i> Вернуть выбранные в работу
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectAll()">
                        <i class="ri-checkbox-line me-1"></i> Выбрать все
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAll()">
                        <i class="ri-checkbox-blank-line me-1"></i> Снять выделение
                    </button>
                </div>
                <div class="text-muted">
                    <small>Найдено: {{ $sentences->total() }} записей</small>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th width="50%">Предложение</th>
                            <th width="150">Статус</th>
                            <th width="200">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sentences as $sentence)
                            @php
                                // Правильно получаем перевод для региона
                                $translation = $sentence->translationForRegion;

                                // Если translationForRegion возвращает коллекцию, берем первый элемент
                                if ($translation instanceof \Illuminate\Support\Collection) {
                                    $translation = $translation->first();
                                }

                                // Определяем статус
                                if (!$translation) {
                                    $statusText = 'Не начато';
                                    $statusColor = 'secondary';
                                    $actionType = 'complete';
                                    $actionText = 'Пометить завершенным';
                                    $actionClass = 'btn-outline-success';
                                    $disabled = false;
                                } else {
                                    $statusCode = $translation->status;

                                    if ($statusCode === $translationStatuses['assigned']) {
                                        $statusText = 'Назначен';
                                        $statusColor = 'info';
                                        $actionType = 'none';
                                        $actionText = 'В процессе перевода';
                                        $actionClass = 'btn-outline-secondary';
                                        $disabled = true;
                                    } elseif ($statusCode === $translationStatuses['translated']) {
                                        $statusText = 'На проверке';
                                        $statusColor = 'warning';
                                        $actionType = 'none';
                                        $actionText = 'Ожидает проверки';
                                        $actionClass = 'btn-outline-secondary';
                                        $disabled = true;
                                    } elseif ($statusCode === $translationStatuses['proofread']) {
                                        $statusText = 'Завершен (корректором)';
                                        $statusColor = 'success';
                                        $actionType = 'available';
                                        $actionText = 'Вернуть в работу';
                                        $actionClass = 'btn-outline-danger';
                                        $disabled = false;
                                    } elseif ($statusCode === $translationStatuses['completed_by_admin']) {
                                        $statusText = 'Завершен (администратором)';
                                        $statusColor = 'primary';
                                        $actionType = 'available';
                                        $actionText = 'Вернуть в работу';
                                        $actionClass = 'btn-outline-danger';
                                        $disabled = false;
                                    } elseif ($statusCode === $translationStatuses['rejected']) {
                                        $statusText = 'Отклонен';
                                        $statusColor = 'danger';
                                        $actionType = 'complete';
                                        $actionText = 'Пометить завершенным';
                                        $actionClass = 'btn-outline-success';
                                        $disabled = false;
                                    } else {
                                        $statusText = 'Не начато';
                                        $statusColor = 'secondary';
                                        $actionType = 'complete';
                                        $actionText = 'Пометить завершенным';
                                        $actionClass = 'btn-outline-success';
                                        $disabled = false;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        value="{{ $sentence->id }}"
                                        class="sentence-checkbox"
                                        {{ $disabled ? 'disabled' : '' }}
                                    >
                                </td>
                                <td>
                                    {{ $sentence->sentence }}
                                    @if($sentence->otherSentence)
                                        <br><span class="badge bg-secondary mt-1">Дополнительный</span>
                                    @endif
                                    @if($translation && $translation->translator)
                                        <br><small class="text-muted">Переводчик: {{ $translation->translator->name }}</small>
                                    @endif
                                    @if($translation && $translation->proofreader)
                                        <br><small class="text-muted">Корректор: {{ $translation->proofreader->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}">{{ $statusText }}</span>
                                </td>
                                <td>
                                    @if($actionType == 'complete')
                                        <button type="button"
                                                class="btn btn-sm {{ $actionClass }}"
                                                onclick="markAsCompleted({{ $sentence->id }})">
                                            <i class="ri-checkbox-circle-line me-1"></i> {{ $actionText }}
                                        </button>
                                    @elseif($actionType == 'available')
                                        <button type="button"
                                                class="btn btn-sm {{ $actionClass }}"
                                                onclick="markAsAvailable({{ $sentence->id }})">
                                            <i class="ri-refresh-line me-1"></i> {{ $actionText }}
                                        </button>
                                    @else
                                        <span class="text-muted">{{ $actionText }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {{ $sentences->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <script>
        let selectedIds = [];

        function submitBulkForm(action) {
            const checkboxes = document.querySelectorAll('.sentence-checkbox:checked:not(:disabled)');
            if (checkboxes.length === 0) {
                alert('Выберите хотя бы одно предложение');
                return;
            }

            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (action === 'complete') {
                document.getElementById('bulk-complete-ids').value = JSON.stringify(ids);
                if (confirm(`Вы уверены, что хотите пометить ${ids.length} предложение(ий) как завершенные?`)) {
                    document.getElementById('bulk-complete-form').submit();
                }
            } else if (action === 'available') {
                document.getElementById('bulk-available-ids').value = JSON.stringify(ids);
                if (confirm(`Вы уверены, что хотите вернуть ${ids.length} предложение(ий) в работу?`)) {
                    document.getElementById('bulk-available-form').submit();
                }
            }
        }

        function markAsCompleted(sentenceId) {
            if (confirm('Пометить предложение как завершенное?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("region-admin.mark-completed") }}';
                form.innerHTML = `
                    @csrf
                <input type="hidden" name="sentence_id" value="${sentenceId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function markAsAvailable(sentenceId) {
            if (confirm('Вернуть предложение в работу?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("region-admin.mark-available") }}';
                form.innerHTML = `
                    @csrf
                <input type="hidden" name="sentence_id" value="${sentenceId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function selectAll() {
            document.querySelectorAll('.sentence-checkbox:not(:disabled)').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAll() {
            document.querySelectorAll('.sentence-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        document.getElementById('select-all').addEventListener('change', function(e) {
            document.querySelectorAll('.sentence-checkbox:not(:disabled)').forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });
    </script>

    <style>
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .sentence-checkbox:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .table > :not(caption) > * > * {
            vertical-align: middle;
            padding: 0.75rem;
        }
    </style>
@endsection
