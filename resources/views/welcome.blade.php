@extends('layouts.main')

@section('content')
    <div class="row gy-6">
        <!-- Congratulations card -->
        <div class="col-md-12 col-lg-4">
            <div class="card">
                <div class="card-body text-nowrap">
                    <h5 class="card-title mb-0 flex-wrap text-nowrap">Здравствуйте, {{auth()->user()->name}}</h5>
                    <form method="POST" action="{{route('sentences.upload')}}" enctype="multipart/form-data">
                        @csrf
                        <input class="form-control mb-6" type="file" id="formFile" name="file">
                        <button type="submit" class="btn btn-success">
                            Загрузить корпус
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!--/ Congratulations card -->

        <!-- Transactions -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Сводка</h5>
                    </div>
                </div>
                <div class="card-body pt-lg-10">
                    <div class="row g-6">
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-primary rounded shadow-xs">
                                        <i class="icon-base ri ri-list-view icon-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">Корпус</p>
                                    <h5 class="mb-0">{{$sentenceCount}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-success rounded shadow-xs">
                                        <i class="icon-base ri ri-group-line icon-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">Пользователи</p>
                                    <h5 class="mb-0">{{$usersCount}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-warning rounded shadow-xs">
                                        <i class="icon-base ri ri-text-block icon-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">Переводы</p>
                                    <h5 class="mb-0">{{$translatesCount}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow-xs">
                                        <i class="icon-base ri ri-map-2-fill icon-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">Регионы</p>
                                    <h5 class="mb-0">{{$regionsCount}}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Transactions -->

        <!-- Total Earnings -->
        <div class="col-xl-4 col-md-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between" style="padding-bottom: 6px !important;">
                    <h5 class="card-title m-0">Регионы по переводам</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @if($topRegions)
                            @foreach($topRegions as $region)
                                <li class="d-flex mb-6">
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <h6 class="mb-0">{{$region->name}}</h6>
                                            <p class="mb-0">Переводчиков: {{$region->translators->count()}}</p>
                                        </div>
                                        <div>
                                            <h6 class="mb-2">{{$region->translated_count}}</h6>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <!--/ Total Earnings -->

        <!-- Four Cards -->
        <div class="col-xl-4 col-md-6">
            <div class="row gy-6">
                <div class="col-sm-6" style="height: 191px">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-success rounded-circle shadow-xs">
                                    <i class="icon-base ri ri-verified-badge-fill icon-24px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-1">Всего переведено</h6>
                            <div class="d-flex flex-wrap mb-1 align-items-center">
                                <h4 class="mb-0 me-2">{{$completedTranslations}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6" style="height: 191px">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-warning rounded-circle shadow-xs">
                                    <i class="icon-base ri ri-time-fill icon-24px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-1">В процессе</h6>
                            <div class="d-flex flex-wrap mb-1 align-items-center">
                                <h4 class="mb-0 me-2">{{$inProgressTranslations}}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6" style="height: 191px">
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
                                <h4 class="mb-0 me-2">{{$rejectedTranslations}}</h4>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--/ four cards -->

        <!-- Sales by Countries -->
        <div class="col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Топ переводчиков</h5>
                </div>
                <div class="card-body">
                    @if($topTranslators)
                        @foreach($topTranslators as $user)
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="avatar me-4">
                                        @if(isset($user->avatar))
                                            <div class="avatar-initial bg-label-success rounded-circle">
                                                <img src="{{asset('storage/' . $user->avatar)}}" style="border-radius: 50%; object-fit: cover;">
                                            </div>
                                        @else
                                            <div class="avatar-initial bg-label-success rounded-circle">
                                                <img src="{{asset('assets/img/user.png')}}" alt="">
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <h6 class="mb-0">{{$user->name}}</h6>
                                        </div>
                                        <p class="mb-0">{{$user->region->name}}</p>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-1">{{$user->translated_count}}</h6>
                                    <small class="text-body-secondary">Переведено</small>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <!--/ Sales by Countries -->

        <!-- Data Tables -->
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Руководители регионов</h5>
                </div>
                <div class="table-responsive">

                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th class="text-truncate">Имя</th>
                            <th class="text-truncate">Email</th>
                            <th class="text-truncate">Регион</th>
                            <th class="text-truncate">Зарегистрирован</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($supervisors as $supervisor)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-4">
                                                @if($supervisor->avatar)
                                                    <img src="{{asset('storage/public/' . $supervisor->avatar)}}" alt="Avatar" class="rounded-circle" />
                                                @else
                                                    <img src="{{asset('user.png')}}" alt="Avatar" class="rounded-circle" />
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-truncate">{{$supervisor->name}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">{{$supervisor->email}}</td>
                                    <td class="text-truncate">
                                        <div class="d-flex align-items-center">
                                            <span>{{$supervisor->region->name}}</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-label-success rounded-pill">{{$supervisor->created_at}}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    {{$supervisors->links('pagination::bootstrap-5')}}
                </div>
            </div>
        </div>
        <!--/ Data Tables -->
    </div>
@endsection
