@extends('layouts.main')

@section('content')
    <div class="container">
        <h2>Панель корректора</h2>

        @foreach($translations as $translation)
            <div class="card mb-3">
                <div class="card-header">
                    Перевод от {{ $translation->translator->name }}
                    <span class="badge bg-secondary float-end">
                    {{ $translation->created_at->format('d.m.Y H:i') }}
                </span>
                </div>
                <div class="card-body">
                    <h5>Оригинал:</h5>
                    <div class="mb-3 p-3 bg-light rounded">
                        {{ $translation->sentence->sentence }}
                    </div>

                    <h5>Перевод:</h5>
                    <div class="mb-3 p-3 bg-light rounded">
                        {{ $translation->translated_text }}
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
    </div>

    <script>
        document.querySelector('select[name="action"]').addEventListener('change', function() {
            document.getElementById('reject-reason').style.display =
                this.value === 'reject' ? 'block' : 'none';
        });
    </script>
@endsection
