@extends('layouts.main')

@section('content')
    <div class="row gy-6">
        <!-- Four Cards -->
        <div class="row gy-6">
            <div class="" style="display: flex; align-items: center; justify-content: space-between; width: 100%">
                <!-- Всего переводов -->
                <div class="col" style="height: 191px; max-width: 250px;">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-primary rounded-circle shadow-xs">
                                    <i class="icon-base ri ri-file-list-2-line icon-24px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-1">Успешных переводов</h6>
                            <div class="d-flex flex-wrap mb-1 align-items-center">
                                <h4 class="mb-0 me-2">{{ $completedTranslations }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- На проверке -->
                <div class="col" style="height: 191px; max-width: 250px;">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-info rounded-circle shadow-xs">
                                    <i class="icon-base ri ri-task-line icon-24px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-1">На проверке</h6>
                            <div class="d-flex flex-wrap mb-1 align-items-center">
                                <h4 class="mb-0 me-2">{{ $translatedTranslations }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Отклонено -->
                <div class="col" style="height: 191px; max-width: 250px;">
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
                                <h4 class="mb-0 me-2">{{ $rejectedTranslations }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col" style="height: 191px; max-width: 250px;">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-success rounded-circle shadow-xs">
                                    <i class="icon-base ri ri-user-fill icon-24px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-1">Переводчиков</h6>
                            <div class="d-flex flex-wrap mb-1 align-items-center">
                                <h4 class="mb-0 me-2">{{ $translatorsCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col" style="height: 191px; max-width: 250px;">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="avatar">
                                <div class="avatar-initial bg-warning rounded-circle shadow-xs">
                                    <i class="icon-base ri ri-user-fill icon-24px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-1">Корректоров</h6>
                            <div class="d-flex flex-wrap mb-1 align-items-center">
                                <h4 class="mb-0 me-2">{{ $proofreadersCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ four cards -->

        <div class="row gy-6">
            <!-- Топ переводчиков -->
            <div class="col-xl-6 col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Топ переводчиков</h5>
                        <small class="text-muted">По завершенным переводам</small>
                    </div>
                    <div class="card-body">
                        @forelse($topTranslators as $translator)
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="avatar me-4">
                                        <div class="avatar-initial bg-label-success rounded-circle">
                                            @if(isset($translator->avatar))
                                                <img src="{{ Storage::disk('public')->url($translator->avatar) }}" alt="">
                                            @else
                                                <img src="{{ asset('assets/img/user.png') }}" alt="">
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <h6 class="mb-0">{{ $translator->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-1">{{ $translator->completed_translations ?? 0 }}</h6>
                                    <small class="text-body-secondary">Переведено</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="ri-user-line fs-1 text-muted"></i>
                                <p class="mt-2 text-muted mb-0">Нет завершенных переводов</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Топ корректоров -->
            <div class="col-xl-6 col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Топ корректоров</h5>
                        <small class="text-muted">По проверенным переводам</small>
                    </div>
                    <div class="card-body">
                        @forelse($topProofreaders as $proofreader)
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="avatar me-4">
                                        <div class="avatar-initial bg-label-info rounded-circle">
                                            @if(isset($proofreader->avatar))
                                                <img src="{{ Storage::disk('public')->url($proofreader->avatar) }}" alt="">
                                            @else
                                                <img src="{{ asset('assets/img/user.png') }}" alt="">
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <h6 class="mb-0">{{ $proofreader->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-1">{{ $proofreader->proofreads_count ?? 0 }}</h6>
                                    <small class="text-body-secondary">Проверено</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="ri-user-line fs-1 text-muted"></i>
                                <p class="mt-2 text-muted mb-0">Нет проверенных переводов</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Неподтвержденные пользователи -->
            <div class="col-12">
                <div class="card overflow-hidden">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Неподтвержденные пользователи</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th class="text-truncate">Имя</th>
                                <th class="text-truncate">Email</th>
                                <th class="text-truncate">Зарегистрирован</th>
                                <th class="text-truncate">Регион</th>
                                <th class="text-truncate">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-4">
                                                @if(isset($user->avatar))
                                                    <img src="{{ Storage::disk('public')->url($user->avatar) }}" alt="">
                                                @else
                                                    <img src="{{ asset('assets/img/user.png') }}" alt="">
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-truncate">{{ $user->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">{{ $user->email }}</td>
                                    <td><span class="badge bg-label-success rounded-pill">{{ $user->created_at->format('d.m.Y') }}</span></td>
                                    <td class="text-truncate">
                                        <span class="badge bg-label-info">{{ $user->region->name ?? 'Не указан' }}</span>
                                    </td>
                                    <td class="d-flex align-items-center">
                                        <div class="d-inline-block">
                                            <a href="javascript:;"
                                               class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                               data-bs-toggle="dropdown">
                                                <i class="icon-base ri ri-more-2-line icon-22px"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                                <li><a class="dropdown-item">Создан: {{ $user->created_at->format('d.m.Y H:i') }}</a></li>
                                                @if($user->deleted_at)
                                                    <li>
                                                        <form action="{{ route('users.restore', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="dropdown-item">Восстановить</button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('users.archive', $user->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">Архивировать</button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <li>
                                                    <form action="{{ route('users.force-delete', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button onclick="return confirm('Вы действительно хотите безвозвратно удалить этого пользователя?');"
                                                                class="dropdown-item text-danger delete-record">
                                                            Удалить окончательно
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{ route('users.edit', $user->id) }}"
                                           class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit">
                                            <i class="icon-base ri ri-edit-box-line icon-22px"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
