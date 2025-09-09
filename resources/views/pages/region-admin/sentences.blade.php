@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Управление предложениями региона</h5>

                <!-- Форма поиска -->
                <form method="GET" action="{{ route('region-admin.sentences') }}" class="d-flex align-items-center gap-3">
                    <div class="dt-search d-flex align-items-center">
                        <input type="search" name="search" value="{{ request('search') }}"
                               class="form-control form-control-sm"
                               placeholder="Введите слово или фразу для поиска"
                               style="width: 300px;"
                               required>
                    </div>

                    <select name="status" class="form-select form-select-sm" style="width: 200px;">
                        <option value="">Все статусы</option>
                        <option value="{{ $translationStatuses['proofread'] }}" {{ request('status') == $translationStatuses['proofread'] ? 'selected' : '' }}>Завершено</option>
                        <option value="{{ $translationStatuses['translated'] }}" {{ request('status') == $translationStatuses['translated'] ? 'selected' : '' }}>В работе</option>
                        <option value="{{ $translationStatuses['completed_by_admin'] }}" {{ request('status') == $translationStatuses['completed_by_admin'] ? 'selected' : '' }}>Завершено админом</option>
                    </select>

                    <select name="limit" class="form-select form-select-sm" style="width: 100px;">
                        <option value="10" {{ $currentLimit == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $currentLimit == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $currentLimit == 50 ? 'selected' : '' }}>50</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ri-search-line"></i> Искать
                    </button>

                    <a href="{{ route('region-admin.sentences') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-refresh-line"></i> Сброс
                    </a>
                </form>
            </div>
        </div>

        @if(request('search'))
            <div class="alert alert-info">
                Результаты поиска для: "<strong>{{ request('search') }}</strong>"
                @if($sentences->total() > 0)
                    - найдено {{ $sentences->total() }} предложений
                @else
                    - ничего не найдено
                @endif
            </div>
        @endif

        <!-- Форма массовых действий -->
        <form method="POST" action="{{ route('region-admin.bulk-complete') }}" id="bulk-complete-form">
            @csrf
        </form>

        <form method="POST" action="{{ route('region-admin.bulk-make-available') }}" id="bulk-available-form">
            @csrf
        </form>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="submitBulkForm('complete')">
                        Пометить выбранные как завершенные
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="submitBulkForm('available')">
                        Вернуть выбранные в работу
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectAll()">
                        Выбрать все
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAll()">
                        Снять выделение
                    </button>
                </div>
                <div>
                    {{ $sentences->appends(request()->query())->links() }}
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>Предложение</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sentences as $sentence)
                            @php
                                $translation = $sentence->translationForRegion ? $sentence->translationForRegion->first() : null;
                                $isCompleted = $translation && in_array($translation->status, [
                                    $translationStatuses['proofread'],
                                    $translationStatuses['completed_by_admin']
                                ]);
                            @endphp
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="sentence_ids[]"
                                        value="{{ $sentence->id }}"
                                        class="sentence-checkbox"
                                        form="bulk-complete-form"
                                        {{ $isCompleted ? 'disabled' : '' }}
                                    >
                                </td>
                                <td>{{ $sentence->sentence }}</td>
                                <td>
                                    @if($translation)
                                        @if($translation->status === $translationStatuses['proofread'])
                                            <span class="badge bg-success">Завершено</span>
                                        @elseif($translation->status === $translationStatuses['completed_by_admin'])
                                            <span class="badge bg-info">Завершено админом</span>
                                        @elseif($translation->status === $translationStatuses['translated'])
                                            <span class="badge bg-warning">Ожидает проверки</span>
                                        @else
                                            <span class="badge bg-warning">В работе</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Не начато</span>
                                    @endif
                                </td>
                                <td>
                                    @if($isCompleted)
                                        <form method="POST" action="{{ route('region-admin.mark-available') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="sentence_id" value="{{ $sentence->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Вернуть в работу
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('region-admin.mark-completed') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="sentence_id" value="{{ $sentence->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                Переведено
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {{ $sentences->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <script>
        function submitBulkForm(action) {
            const checkboxes = document.querySelectorAll('.sentence-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Выберите хотя бы одно предложение');
                return;
            }

            if (action === 'complete') {
                document.getElementById('bulk-complete-form').submit();
            } else if (action === 'available') {
                document.getElementById('bulk-available-form').submit();
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
        .sentence-checkbox:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .badge {
            font-size: 0.75rem;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endsection
