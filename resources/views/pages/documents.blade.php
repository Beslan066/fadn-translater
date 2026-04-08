@extends('layouts.main')

@section('content')
    <div class="row gy-6">
        <!-- Congratulations card -->
        <div class="col-md-12 col-lg-4">
            <h3>Юридическая информация</h3>

            <ul>
                <li><a href="/privacy-policy" style="text-decoration: underline;">Политика конфиденциальности</a></li>
                <li><a href="/soglasie-na-obrabotku-personalnykh-dannykh" style="text-decoration: underline;">Согласие на обработку персональных данных</a></li>
            </ul>
        </div>
    </div>
@endsection
