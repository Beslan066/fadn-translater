
@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="alert alert-info">
            <h4>Нет доступных предложений для перевода</h4>
            <p>В настоящее время все предложения для вашего региона обрабатываются другими переводчиками.</p>
            <p>Попробуйте позже или обратитесь к администратору.</p>

            <a href="{{ route('translator.dashboard') }}" class="btn btn-primary">
                Проверить снова
            </a>
        </div>
    </div>
@endsection
