@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                Перевод предложения (Регион: {{ auth()->user()->region->name }})
            </div>

            <div class="card-body">
                <div class="mb-4">
                    <h5>Оригинал:</h5>
                    <div class="p-3 bg-light rounded">
                        {{ $translation->sentence->sentence }}
                    </div>
                </div>

                <form method="POST" action="{{ route('translator.submit', ['translation' => $translation->id]) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Ваш перевод:</label>
                        <textarea name="translated_text" class="form-control" rows="5"
                        >{{ old('translated_text', $translation->translated_text ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            Отправить перевод
                        </button>

                        <form method="POST" action="{{ route('translator.skip', $translation) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('Вы уверены, что хотите пропустить это предложение?')">
                                Пропустить предложение
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
