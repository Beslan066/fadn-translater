@extends('layouts.main')

@section('content')
    <div class="container">
        <h2>Панель переводчика</h2>

        <div class="card mb-4">
            <div class="card-header">Мои текущие переводы</div>
            <div class="card-body">
                @foreach($assignedSentences as $sentence)
                    <div class="mb-3">
                        <h5>{{ Str::limit($sentence->sentence, 100) }}</h5>
                        <a href="{{ route('translator.sentence.show', $sentence) }}" class="btn btn-sm btn-primary">
                            Продолжить перевод
                        </a>
                    </div>
                @endforeach
                {{ $assignedSentences->links() }}
            </div>
        </div>

        <div class="card">
            <div class="card-header">Доступные для перевода предложения</div>
            <div class="card-body">
                @foreach($sentences as $sentence)
                    <div class="mb-3">
                        <h5>{{ Str::limit($sentence->sentence, 100) }}</h5>
                        <span class="badge bg-secondary">Сложность: {{ $sentence->complexity }}/5</span>
                        <a href="{{ route('translator.sentence.show', $sentence) }}" class="btn btn-sm btn-success">
                            Начать перевод
                        </a>
                    </div>
                @endforeach
                {{ $sentences->links() }}
            </div>
        </div>
    </div>
@endsection
