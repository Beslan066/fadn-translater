@extends('layouts.main')

@section('content')
    <div class="row mb-6 gy-6">
        <div class="col-xl">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Редактирование пользователя</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <!-- Блок с текущим аватаром -->
                        @if($user->avatar)
                            <div class="mb-4 w-50">
                                <label class="form-label">Текущий аватар</label>
                                <div>
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Аватар пользователя"  width="200px">
                                </div>
                            </div>
                        @endif

                        <!-- Поле для загрузки нового аватара -->
                        <div class="mb-4 w-50">
                            <label for="formFile" class="form-label">Новый аватар</label>
                            <input class="form-control" type="file" id="formFile" name="avatar">
                            <div class="form-text">Допустимые форматы: JPEG, PNG, JPG, SVG. Максимальный размер: 2MB</div>
                        </div>
                        @error('avatar')
                        <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror

                        <!-- ФИО -->
                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input type="text" class="form-control" id="name" placeholder="Иван Иванов Иванович"
                                   name="name" value="{{ old('name', $user->name) }}">
                            <label for="name">ФИО</label>
                        </div>
                        @error('name')
                        <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror

                        <!-- Email -->
                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input type="email" id="email" class="form-control" placeholder="ivanov@mail.ru"
                                   name="email" value="{{ old('email', $user->email) }}">
                            <label for="email">Email</label>
                        </div>
                        @error('email')
                        <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror

                        <!-- Роль -->
                        <div class="input-group w-50 mb-4">
                            <label class="input-group-text" for="role">Роль</label>
                            <select class="form-select" id="role" name="role">
                                @foreach($roles as $id => $role)
                                    <option value="{{ $id }}" {{ old('role', $user->role) == $id ? 'selected' : '' }}>
                                        {{ $role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('role')
                        <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror

                        <!-- Регион -->
                        <div class="input-group w-50 mb-4">
                            <label class="input-group-text" for="region_id">Регион</label>
                            <select class="form-select" id="region_id" name="region_id">
                                <option value="">Выберите регион...</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" {{ old('region_id', $user->region_id) == $region->id ? 'selected' : '' }}>
                                        {{ $region->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('region_id')
                        <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror

                        <!-- Пароль -->
                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input class="form-control" type="password" id="password" placeholder="Введите пароль" name="password">
                            <label for="password">Новый пароль</label>
                            <div class="form-text">Оставьте пустым, если не хотите менять</div>
                        </div>
                        @error('password')
                        <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror

                        <!-- Подтверждение пароля -->
                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input class="form-control" type="password" id="password_confirmation"
                                   placeholder="Повторите пароль" name="password_confirmation">
                            <label for="password_confirmation">Подтвердите пароль</label>
                        </div>

                        <!-- Активность -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="is_active"
                                   name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Активен</label>
                        </div>

                        <button type="submit" class="btn btn-primary me-2">Сохранить</button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Отмена</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
