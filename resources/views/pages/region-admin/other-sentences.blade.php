@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <!-- Заголовок -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="ri-database-2-line me-2"></i>
                    Дополнительный корпус предложений - {{ $region->name }}
                </h5>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('region-admin.otherSentences') }}" class="row g-3 align-items-end">
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
                            <i class="ri-price-tag-line me-1"></i> Тип предложения
                        </label>
                        <select name="other_type" class="form-select">
                            <option value="">Все типы ({{ $stats['total'] }})</option>
                            <option value="1" {{ request('other_type') == 1 ? 'selected' : '' }}>Тип 1 ({{ $stats['type1'] }})</option>
                            <option value="2" {{ request('other_type') == 2 ? 'selected' : '' }}>Тип 2 ({{ $stats['type2'] }})</option>
                            <option value="3" {{ request('other_type') == 3 ? 'selected' : '' }}>Тип 3 ({{ $stats['type3'] }})</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ri-stack-line me-1"></i> Статус перевода
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Все статусы</option>
                            <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Не начато</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Назначен переводчику</option>
                            <option value="translated" {{ request('status') == 'translated' ? 'selected' : '' }}>На проверке</option>
                            <option value="proofread" {{ request('status') == 'proofread' ? 'selected' : '' }}>Завершен</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Отклонен</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-search-line"></i> Применить
                            </button>
                            <a href="{{ route('region-admin.otherSentences') }}" class="btn btn-outline-secondary">
                                <i class="ri-refresh-line"></i> Сброс
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Информация о фильтрах -->
        @if(request('search') || request('status') || request('other_type'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="ri-information-line me-2"></i>
                Применены фильтры:
                @if(request('search')) поиск "<strong>{{ request('search') }}</strong>" @endif
                @if(request('other_type')) тип "<strong>{{ request('other_type') }}</strong>" @endif
                @if(request('status'))
                    статус "<strong>
                        @switch(request('status'))
                            @case('not_started') Не начато @break
                            @case('assigned') Назначен @break
                            @case('translated') На проверке @break
                            @case('proofread') Завершен @break
                            @case('rejected') Отклонен @break
                        @endswitch
                    </strong>"
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                        onclick="window.location.href='{{ route('region-admin.otherSentences') }}'"></button>
            </div>
        @endif

        <!-- Таблица предложений -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="ri-table-line me-2"></i>
                        Список дополнительных предложений
                    </h5>
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
                            <th width="80">ID</th>
                            <th width="100">Тип</th>
                            <th width="35%">Предложение</th>
                            <th width="35%">Перевод</th>
                            <th width="120">Статус</th>
                            <th width="150">Информация</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($sentences as $sentence)
                            @php
                                $translation = $sentence->translationForRegion;

                                if (!$translation) {
                                    $statusText = 'Не начато';
                                    $statusColor = 'secondary';
                                    $statusIcon = 'ri-stop-circle-line';
                                    $info = 'Требуется перевод';
                                } elseif ($translation->status === $translationStatuses['assigned']) {
                                    $statusText = 'Назначен';
                                    $statusColor = 'info';
                                    $statusIcon = 'ri-user-line';
                                    $info = 'Переводчик: ' . ($translation->translator->name ?? 'Неизвестен');
                                } elseif ($translation->status === $translationStatuses['translated']) {
                                    $statusText = 'На проверке';
                                    $statusColor = 'warning';
                                    $statusIcon = 'ri-time-line';
                                    $info = 'Ожидает проверки корректора';
                                } elseif ($translation->status === $translationStatuses['proofread']) {
                                    $statusText = 'Завершен';
                                    $statusColor = 'success';
                                    $statusIcon = 'ri-checkbox-circle-line';
                                    $info = 'Проверен корректором';
                                } elseif ($translation->status === $translationStatuses['completed_by_admin']) {
                                    $statusText = 'Завершен админом';
                                    $statusColor = 'primary';
                                    $statusIcon = 'ri-shield-check-line';
                                    $info = 'Завершен администратором';
                                } elseif ($translation->status === $translationStatuses['rejected']) {
                                    $statusText = 'Отклонен';
                                    $statusColor = 'danger';
                                    $statusIcon = 'ri-close-circle-line';
                                    $info = 'Требуется доработка';
                                } else {
                                    $statusText = 'Неизвестно';
                                    $statusColor = 'secondary';
                                    $statusIcon = 'ri-question-line';
                                    $info = '—';
                                }
                            @endphp
                            <tr>
                                <td>{{ $sentence->id }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <i class="ri-price-tag-line me-1"></i> Тип {{ $sentence->otherSentence }}
                                    </span>
                                </td>
                                <td>
                                    <div style="max-width: 400px;">
                                        {{ $sentence->sentence }}
                                    </div>
                                </td>
                                <td>
                                    <div style="max-width: 400px;">
                                        @if($translation && $translation->translated_text)
                                            {{ Str::limit($translation->translated_text, 150) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColor }}">
                                        <i class="{{ $statusIcon }} me-1"></i> {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="ri-information-line me-1"></i> {{ $info }}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="ri-inbox-line fs-1"></i>
                                        <p class="mt-3 mb-0">Дополнительные предложения не найдены</p>
                                        <small>Попробуйте изменить параметры фильтрации</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($sentences->hasPages())
                <div class="card-footer">
                    {{ $sentences->appends(request()->query())->links('pagination::bootstrap-5') }}
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

        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
        }

        .table > :not(caption) > * > * {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endpush
