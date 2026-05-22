@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ri-map-pin-line me-2"></i>
                    Переводы региона: {{ $region->name }}
                </h5>
                <a href="{{ route('regions.index') }}" class="btn btn-label-secondary">
                    <i class="ri-arrow-left-line me-1"></i> Назад к регионам
                </a>
            </div>
        </div>

        <div class="card-datatable text-nowrap">
            <div class="table-responsive">
                <table class="datatables-basic table table-bordered" style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 25%;">Оригинальное предложение</th>
                        <th style="width: 25%;">Перевод</th>
                        <th style="width: 12%;">Переводчик</th>
                        <th style="width: 12%;">Корректор</th>
                        <th style="width: 8%;">Статус</th>
                        <th style="width: 5%;">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($translations as $translation)
                        @php
                            $statusMap = [
                                $translation::STATUS_ASSIGNED => ['text' => 'Назначен', 'color' => 'info'],
                                $translation::STATUS_TRANSLATED => ['text' => 'На проверке корректором', 'color' => 'warning'],
                                $translation::STATUS_PROOFREAD => ['text' => 'Переведено', 'color' => 'success'],
                                $translation::STATUS_COMPLETED_BY_ADMIN => ['text' => 'Завершен админом', 'color' => 'primary'],
                                $translation::STATUS_REJECTED => ['text' => 'Отклонен', 'color' => 'danger'],
                            ];

                            $statusInfo = $statusMap[$translation->status] ?? ['text' => 'Неизвестно', 'color' => 'secondary'];
                        @endphp
                        <tr>
                            <td class="align-middle">{{ $translation->id }}</td>
                            <td class="text-wrap align-middle">
                                {{ $translation->sentence->sentence ?? '—' }}
                            </td>
                            <td class="align-middle">
                                @if($translation->sentence && $translation->sentence->otherSentence)
                                    <span class="badge bg-label-warning">Дополнительный</span>
                                @else
                                    <span class="badge bg-label-info">Основной</span>
                                @endif
                            </td>
                            <td class="text-wrap align-middle">
                                {{ $translation->translated_text ?? '—' }}
                            </td>
                            <td class="align-middle">{{ $translation->translator->name ?? '—' }}</td>
                            <td class="align-middle">{{ $translation->proofreader->name ?? '—' }}</td>
                            <td class="align-middle">
                <span class="badge rounded-pill bg-label-{{ $statusInfo['color'] }}">
                    {{ $statusInfo['text'] }}
                </span>
                            </td>
                            <td class="align-middle">
                                {{ $translation->translated_at ? $translation->translated_at->format('d.m.Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="ri-file-copy-line fs-1 text-muted"></i>
                                <p class="mt-2 mb-0 text-muted">
                                    Нет переводов для региона "{{ $region->name }}"
                                </p>
                                <small class="text-muted">
                                    Переводы появятся, когда переводчики начнут работу
                                </small>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($translations->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">
                            Показано {{ $translations->firstItem() ?? 0 }} - {{ $translations->lastItem() ?? 0 }}
                            из {{ $translations->total() }} переводов
                        </small>
                    </div>
                    <div>
                        {{ $translations->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .text-wrap {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }

        .align-middle {
            vertical-align: middle !important;
        }

        .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .table th {
            vertical-align: middle;
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .badge {
            font-weight: 500;
            font-size: 0.75rem;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .card-header .btn-label-secondary {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .card-header .btn-label-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .card-title {
            color: white;
        }

        .card-title i {
            color: white;
        }

        /* Фикс для пагинации */
        .card-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.125);
            background-color: rgba(0, 0, 0, 0.02);
        }

        .pagination {
            margin-bottom: 0;
        }
    </style>
@endpush
