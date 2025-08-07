@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-datatable text-nowrap">
            <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                <div class="row card-header mx-0 px-2">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <h5 class="card-title mb-0">Пользователи</h5>
                    </div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto">
                        <div class="dt-buttons btn-group flex-wrap">
                            <div class="btn-group">
                                <button
                                    class="btn buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                                    tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog"
                                    aria-expanded="false">
                                    <span>
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="icon-base ri ri-external-link-line icon-18px"></i>
                                            <span class="d-none d-sm-inline-block">Выгрузить</span>
                                        </span>
                                    </span>
                                </button>
                            </div>
                            <a href="{{route('users.create')}}" class="btn create-new btn-primary" tabindex="0"
                               aria-controls="DataTables_Table_0"
                               type="button">
                                <span>
                                    <span class="d-flex align-items-center">
                                        <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                                        <span class="d-none d-sm-inline-block">Добавить</span>
                                    </span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                <hr class="my-0">

                <!-- Фильтры и поиск -->
                <div class="row m-3 mx-2 my-0 justify-content-between">
                    <form method="GET" action="{{ route('region-admin.users') }}" class="w-100">
                        <input type="hidden" name="region_id" value="{{ auth()->user()->region_id }}">
                        <div class="row mt-2">
                            <div class="col-md-2 mb-2">
                                <input type="search" name="search" class="form-control form-control-sm"
                                       placeholder="Поиск по имени или email"
                                       value="{{ request('search') }}"
                                       style="border:1px solid #d1cfd4 !important">
                            </div>
                            <div class="col-md-2 mb-2">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Все статусы</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                        Активные
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                        Неактивные
                                    </option>
                                    <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>
                                        Удаленные
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <select name="role" class="form-select form-select-sm" id="role-select">
                                    <option value="">Все роли</option>
                                    <option value="translator" {{ request('role') == 'translator' ? 'selected' : '' }}>
                                        Переводчик
                                    </option>
                                    <option
                                        value="proofreader" {{ request('role') == 'proofreader' ? 'selected' : '' }}>
                                        Корректор
                                    </option>
                                </select>
                            </div>

                            <!-- Фильтры, которые показываются только для переводчиков -->
                            <div class="col-md-2 mb-2 translator-filters"
                                 style="{{ request('role') != 'translator' ? 'display:none;' : '' }}">
                                <select name="translation_status" class="form-select form-select-sm">
                                    <option value="">Статус переводов</option>
                                    <option value="1" {{ request('translation_status') == '1' ? 'selected' : '' }}>На
                                        проверке
                                    </option>
                                    <option value="2" {{ request('translation_status') == '2' ? 'selected' : '' }}>
                                        Проверено
                                    </option>
                                    <option value="3" {{ request('translation_status') == '3' ? 'selected' : '' }}>
                                        Отклонено
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2 translator-filters"
                                 style="{{ request('role') != 'translator' ? 'display:none;' : '' }}">
                                <select name="translations_count" class="form-select form-select-sm">
                                    <option value="">Количество переводов</option>
                                    <option
                                        value="on_review" {{ request('translations_count') == 'on_review' ? 'selected' : '' }}>
                                        На проверке
                                    </option>
                                    <option
                                        value="approved" {{ request('translations_count') == 'approved' ? 'selected' : '' }}>
                                        Проверено
                                    </option>
                                    <option
                                        value="rejected" {{ request('translations_count') == 'rejected' ? 'selected' : '' }}>
                                        Отклонено
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary">Применить</button>
                                    <a href="{{ route('region-admin.users') }}" class="btn btn-sm btn-outline-secondary">Сбросить</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="justify-content-between dt-layout-table">
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive">
                        <table class="datatables-basic table table-bordered table-responsive dataTable dtr-column"
                               id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" style="width: 100%;">
                            <colgroup>
                                <col data-dt-column="1" style="width: 64.2031px;">
                                <col data-dt-column="3" style="width: 327.562px;">
                                <col data-dt-column="4" style="width: 313.781px;">
                                <col data-dt-column="5" style="width: 133.203px;">
                                <col data-dt-column="6" style="width: 131.781px;">
                                <col data-dt-column="7" style="width: 157.859px;">
                                <col data-dt-column="8" style="width: 119.609px;">
                            </colgroup>
                            <thead>
                            <tr>
                                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-select dt-orderable-none"
                                    aria-label="">
                                    <span class="dt-column-title"></span>
                                    <span class="dt-column-order"></span>
                                    ID
                                </th>
                                <th data-dt-column="3" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Name: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Имя</span>
                                    <span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="4" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Email: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Email</span>
                                    <span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="4" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Email: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Регион</span>
                                    <span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="6" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc dt-type-numeric"
                                    aria-label="Salary: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">На проверке</span>
                                    <span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="6" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc dt-type-numeric"
                                    aria-label="Salary: Activate to sort" tabindex="0">
                                    <span class="dt-column-title" role="button">Переведено</span>
                                    <span class="dt-column-order"></span>
                                </th>
                                <th data-dt-column="7" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Статус</span>
                                    <span class="dt-column-order"></span>
                                </th>
                                <th class="d-flex align-items-center dt-orderable-none" data-dt-column="8" rowspan="1"
                                    colspan="1" aria-label="Actions">
                                    <span class="dt-column-title">Действие</span>
                                    <span class="dt-column-order"></span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="dt-select">{{$user->id}}</td>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center user-name">
                                            <div class="avatar-wrapper">
                                                @if(isset($user->avatar))
                                                    <div class="avatar me-2">
                                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                                             class="rounded-circle" style="object-fit: cover">
                                                    </div>
                                                @else
                                                    <div class="avatar me-2">
                                                        <img src="{{ $user->avatar_url }}" alt="Avatar"
                                                             class="rounded-circle" style="object-fit: cover">
                                                    </div>
                                                @endif

                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="emp_name text-truncate h6 mb-0">{{$user->name}}</span>
                                                <small
                                                    class="emp_post text-truncate">{{$user->getRoleNameAttribute()}}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{$user->email}}</td>
                                    <td>{{ $user->region?->name ?? '-' }}</td>
                                    <td class="dt-type-numeric">{{$user->translatedTranslations->count()}}</td>
                                    <td class="dt-type-numeric">{{$user->proofreadTranslations->count()}}</td>
                                    <td>
                                        @if($user->deleted_at)
                                            <span class="badge rounded-pill bg-label-danger">
                                                Удален
                                            </span>
                                        @elseif($user->is_active)
                                            <span class="badge rounded-pill bg-label-success">
                                                Активен
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-label-warning">
                                                Отключен
                                            </span>
                                        @endif
                                    </td>
                                    <td class="d-flex align-items-center">
                                        <div class="d-inline-block">
                                            <a href="javascript:;"
                                               class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                               data-bs-toggle="dropdown">
                                                <i class="icon-base ri ri-more-2-line icon-22px"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                                <li>
                                                    <a class="dropdown-item">Создан: {{$user->created_at->format('d.m.Y H:i')}}</a>
                                                </li>
                                                @if($user->deleted_at)
                                                    <li>
                                                        <form action="{{ route('users.restore', $user->id) }}"
                                                              method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="dropdown-item">Восстановить
                                                            </button>
                                                        </form>
                                                    </li>
                                                @else
                                                    <li>
                                                        <form action="{{ route('users.archive', $user->id) }}"
                                                              method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item">Архивировать
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <li>
                                                    <form action="{{ route('users.force-delete', $user->id) }}"
                                                          method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            onclick="return confirm('Вы действительно хотите безвозвратно удалить этого пользователя?');"
                                                            class="dropdown-item text-danger delete-record">
                                                            Удалить окончательно
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="{{route('users.edit', $user->id)}}"
                                           class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit">
                                            <i class="icon-base ri ri-edit-box-line icon-22px"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
                <div class="row mx-2">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_info mt-2 mb-2" id="DataTables_Table_0_info" role="status"
                             aria-live="polite">
                            Показано с {{ $users->firstItem() }} по {{ $users->lastItem() }} из {{ $users->total() }}
                            записей
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                            {{ $users->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('role-select').addEventListener('change', function() {
            const translatorFilters = document.querySelectorAll('.translator-filters');
            if (this.value === 'translator') {
                translatorFilters.forEach(el => el.style.display = 'block');
            } else {
                translatorFilters.forEach(el => {
                    el.style.display = 'none';
                    // Сбрасываем значения фильтров переводов при смене роли
                    const selects = el.querySelectorAll('select');
                    selects.forEach(select => select.value = '');
                });
            }
        });

        // При загрузке страницы тоже проверяем значение
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role-select');
            if (roleSelect) {
                const translatorFilters = document.querySelectorAll('.translator-filters');
                if (roleSelect.value !== 'translator') {
                    translatorFilters.forEach(el => el.style.display = 'none');
                }
            }
        });
    </script>
@endpush
