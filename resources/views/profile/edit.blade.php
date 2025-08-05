@extends('layouts.main')

@section('content')
    <div class="row mb-6 gy-6">
        <div class="col-xl">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Изменить профиль</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        @method('patch')

                        <!-- Аватар и предпросмотр -->
                        <div class="mb-4">
                            <div class="">
                                <div class="me-3" style="margin-bottom: 10px">
                                    <img id="avatar-preview" src="{{ auth()->user()->avatar ? 'storage/'.auth()->user()->avatar : asset('images/default-avatar.png') }}"
                                         class="rounded-circle" width="180" height="180" alt="Avatar" style="object-fit: cover">
                                </div>
                                <div class="w-50">
                                    <input class="form-control" type="file" id="avatar" name="avatar"
                                           onchange="previewAvatar(this)" autocomplete="off">
                                    @error('avatar')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input type="text" class="form-control" id="basic-default-fullname"
                                   placeholder="Иван Иванов Иванович" name="name"
                                   value="{{ old('name', auth()->user()->name) }}" autocomplete="off">
                            <label for="basic-default-fullname">ФИО</label>
                            @error('name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 w-50">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" id="basic-default-email" class="form-control"
                                           placeholder="ivanov@mail.ru" name="email"
                                           value="{{ old('email', auth()->user()->email) }}" autocomplete="off">
                                    <label for="basic-default-email">Email</label>
                                </div>
                            </div>
                            @error('email')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Смена пароля -->
                        <div class="mt-5">
                            <h5 class="mb-3">Смена пароля</h5>

                            <div class="form-floating form-floating-outline mb-4 w-50">
                                <input class="form-control" type="password" name="current_password"
                                       placeholder="Текущий пароль" autocomplete="new-password">
                                <label>Текущий пароль</label>
                                @error('current_password')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating form-floating-outline mb-4 w-50">
                                <input class="form-control" type="password" name="password"
                                       placeholder="Новый пароль" autocomplete="new-password">
                                <label>Новый пароль</label>
                                @error('password')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating form-floating-outline mb-4 w-50">
                                <input class="form-control" type="password" name="password_confirmation"
                                       placeholder="Подтвердите пароль" autocomplete="new-password">
                                <label>Подтвердите пароль</label>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Сохранить изменения</button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-5 border-top pt-4">
                        <h5 class="mb-3">Деактивация аккаунта</h5>
                        <p class="text-muted mb-4">После деактивации ваш аккаунт будет скрыт, но вы сможете восстановить его при следующем входе.</p>

                        <form id="deleteUserForm" method="post" action="{{ route('profile.destroy') }}">
                            @csrf
                            @method('delete')

                            <div class="form-floating form-floating-outline mb-4 w-50">
                                <input class="form-control" type="password" id="deletePassword" name="password"
                                       placeholder="Пароль" autocomplete="new-password" required>
                                <label for="deletePassword">Пароль для подтверждения</label>
                                @error('password', 'userDeletion')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                Деактивировать аккаунт
                            </button>
                        </form>

                        <!-- Модальное окно подтверждения -->
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Подтверждение деактивации</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Вы уверены, что хотите деактивировать свой аккаунт?</p>
                                        <p class="text-muted">Все ваши данные будут сохранены, и вы сможете восстановить аккаунт при следующем входе.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                        <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteUserForm').submit()">
                                            Деактивировать
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Функция для предпросмотра аватара
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Очистка автозаполнения паролей
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('input[type="password"]').forEach(function(input) {
                    if (!input.value) {
                        input.value = '';
                    }
                });
            }, 100);
        });
    </script>
@endpush
