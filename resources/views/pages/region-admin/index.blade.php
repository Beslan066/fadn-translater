@extends('layouts.main')

@section('content')
    <div class="row gy-6">
        <!-- Transactions -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Сводка</h5>
                    </div>
                    <p class="small mb-0">Здесь находится основная информация портала</p>
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
                                    @if(isset($sentenceCount))
                                        <h5 class="mb-0">{{$sentenceCount}}</h5>
                                    @endif
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
                                    @if(isset($usersCount))
                                        <h5 class="mb-0">{{$usersCount}}</h5>
                                    @endif
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
                                    @if(isset($translatesCount))
                                        <h5 class="mb-0">{{$translatesCount}}</h5>
                                    @endif
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
                                    @if(isset($regionsCount))
                                        <h5 class="mb-0">{{$regionsCount}}</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Transactions -->

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
                                @if(isset($completedTranslations))
                                    <h4 class="mb-0 me-2">{{$completedTranslations}}</h4>
                                @endif
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
                                @if(isset($inProgressTranslations))
                                    <h4 class="mb-0 me-2">{{$inProgressTranslations}}</h4>
                                @endif
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
                                @if(isset($rejectedTranslations))
                                    <h4 class="mb-0 me-2">{{$rejectedTranslations}}</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--/ four cards -->

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
                        @if(isset($supervisors))
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
                        @endif
                        </tbody>
                    </table>
                </div>
                @if(isset($supervisors))
                    <div class="mt-2">
                        {{$supervisors->links('pagination::bootstrap-5')}}
                    </div>
                @endif
            </div>
        </div>
        <!--/ Data Tables -->
    </div>
@endsection
