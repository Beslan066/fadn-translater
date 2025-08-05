@extends('layouts.main')

@section('content')
    <div class="row mb-6 gy-6">
        <div class="col-xl">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Создание пользователя</h5>
                </div>
                <div class="card-body">
                    <form action="{{route('users.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('post')
                        <div class="form-floating form-floating-outline mb-6 w-50" >
                            <input type="text" class="form-control" id="basic-default-fullname" placeholder="Иван Иванов Иванович" name="name">
                            <label for="basic-default-fullname">ФИО</label>
                        </div>

                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <div class="mb-6 w-50">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" id="basic-default-email" class="form-control" placeholder="ivanov@mail.ru" aria-label="john.doe" aria-describedby="basic-default-email2" name="email">
                                    <label for="basic-default-email">Email</label>
                                </div>
                            </div>
                        </div>

                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <div class="mb-4 w-50">
                            <input class="form-control" type="file" id="formFile" name="avatar">
                        </div>
                        @error('avatar')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <div class="input-group w-50 mb-4">
                            <label class="input-group-text" for="inputGroupSelect01">Роль</label>
                            <select class="form-select" id="inputGroupSelect01" name="role">
                                <option selected="selected">Выберите...</option>
                                @if(isset($roles))
                                    @foreach($roles as $id => $role)
                                        <option value="{{$id}}">{{$role}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        @error('role')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <div class="input-group w-50 mb-4">
                            <label class="input-group-text" for="inputGroupSelect01">Регион</label>
                            <select class="form-select" id="inputGroupSelect01" name="region_id">
                                <option selected="selected">Выберите...</option>
                                @if(isset($regions))
                                    @foreach($regions as $region)
                                        <option value="{{$region->id}}">{{$region->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        @error('region_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input class="form-control" type="password" placeholder="Введите пароль"  name="password">
                            <label for="html5-password-input">Пароль</label>
                        </div>

                        <div class="form-floating form-floating-outline mb-4 w-50">
                            <input class="form-control" type="password" placeholder="Повторите пароль" id="html5-password-input" name="confirm_password">
                            <label for="html5-password-input">Подтвердите пароль</label>
                        </div>

                        <div class="form-check mt-4 mb-4">
                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1" name="is_active">
                            <label class="form-check-label" for="defaultCheck1">Активировать</label>
                        </div>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Создать</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
