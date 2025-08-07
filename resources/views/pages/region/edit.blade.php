@extends('layouts.main')

@section('content')
    <div class="row mb-6 gy-6">
        <div class="col-xl">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Изменение региона</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('regions.update', $region->id) }}" method="post">
                        @csrf
                        @method('patch')

                        <!-- Название региона -->
                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" placeholder="" name="name" value="{{ old('name', $region->name) }}">
                            <label for="name">Название</label>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Код региона -->
                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   id="code" placeholder="06 или 95 и т.п" name="code" value="{{ old('code', $region->code) }}">
                            <label for="code">Код региона</label>
                            @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Код языка -->
                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input type="text" class="form-control @error('language_code') is-invalid @enderror"
                                   id="language_code" placeholder="ru, en и т.п" name="language_code" value="{{ old('language_code', $region->language_code) }}">
                            <label for="language_code">Код языка</label>
                            @error('language_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Статус активности -->
                        <div class="mb-4 w-50">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_active"
                                       name="is_active" value="1" {{ old('is_active', $region->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Активный регион</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary waves-effect waves-light">Изменить</button>
                        <a href="{{ route('regions.index') }}" class="btn btn-secondary waves-effect">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
