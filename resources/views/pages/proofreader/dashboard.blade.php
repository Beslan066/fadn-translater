@extends('layouts.main')

@section('content')
    <div class="container">
        <h2>Панель корректора</h2>
        <div class="row gy-6">
            <div class="col-sm-3" style="height: 191px">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="avatar">
                            <div class="avatar-initial bg-success rounded-circle shadow-xs">
                                <i class="icon-base ri ri-verified-badge-fill icon-24px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-1">Проверено</h6>
                        <div class="d-flex flex-wrap mb-1 align-items-center">
                            <h4 class="mb-0 me-2">{{ $proofreadByMe->where('status', \App\Models\Translation::STATUS_PROOFREAD)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3" style="height: 191px">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="avatar">
                            <div class="avatar-initial bg-warning rounded-circle shadow-xs">
                                <i class="icon-base ri ri-time-fill icon-24px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-1">Ожидают проверки</h6>
                        <div class="d-flex flex-wrap mb-1 align-items-center">
                            <h4 class="mb-0 me-2">{{ $proofreadByMe->where('status', \App\Models\Translation::STATUS_TRANSLATED)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3" style="height: 191px">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="avatar">
                            <div class="avatar-initial bg-danger rounded-circle shadow-xs">
                                <i class="icon-base ri ri-prohibited-fill icon-24px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-1">Отклонено</h6>
                        <div class="d-flex flex-wrap mb-1 align-items-center">
                            <h4 class="mb-0 me-2">{{ $proofreadByMe->where('status', \App\Models\Translation::STATUS_REJECTED)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($translations->count() > 0)
            @foreach($translations as $translation)
                <div class="card mb-3 mt-2">
                    <div class="card-header">
                        Перевод от {{ $translation->translator->name ?? 'Неизвестный переводчик' }}
                        <span class="badge bg-secondary float-end">
                    {{ $translation->created_at->format('d.m.Y H:i') }}
                </span>
                    </div>
                    <div class="card-body">
                        <h5>Оригинал:</h5>
                        <div class="mb-3 p-3 bg-light rounded">
                            {{ $translation->sentence->sentence ?? 'Оригинальный текст отсутствует' }}
                        </div>

                        <h5>Перевод:</h5>
                        <div class="mb-3 p-3 bg-light rounded">
                            {{ $translation->translated_text ?? 'Перевод отсутствует' }}
                        </div>

                        <form method="POST" action="{{ route('proofreader.review', $translation) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Исправленный вариант (если требуется):</label>
                                <textarea name="proofread_text" class="form-control" rows="3"
                                >{{ old('proofread_text', $translation->translated_text) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Действие:</label>
                                <select name="action" class="form-select" required>
                                    <option value="">Выберите действие</option>
                                    <option value="approve">Принять перевод</option>
                                    <option value="reject">Вернуть на доработку</option>
                                </select>
                            </div>

                            <div class="mb-3" id="reject-reason" style="display: none;">
                                <label class="form-label">Причина возврата:</label>
                                <textarea name="reject_reason" class="form-control" rows="2"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Сохранить решение</button>
                        </form>
                    </div>
                </div>
            @endforeach

            {{ $translations->links() }}
        @else
            <div class="card mb-3 mt-2">
                <div class="card-body text-center">
                    <div class="empty-state">
                        <i class="icon ri-inbox-line display-4 text-muted"></i>
                        <h3 class="mt-3">Нет доступных переводов для проверки</h3>
                        <p class="text-muted">Все переводы проверены или в данный момент нет назначенных вам заданий.</p>
                    </div>
                </div>
            </div>
        @endif

        {{ $translations->links() }}

        @if(!isset($translations))
            <div class="card mb-3 mt-2">
                <div class="card-header">
                    <p>
                        На данный момент нет доступных переводов
                    </p>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.querySelector('select[name="action"]').addEventListener('change', function() {
            document.getElementById('reject-reason').style.display =
                this.value === 'reject' ? 'block' : 'none';
        });
    </script>
@endsection
