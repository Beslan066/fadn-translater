@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-datatable text-nowrap">
            <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                <div class="row card-header mx-0 px-2">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <h5 class="card-title mb-0">Список регионов</h5>
                    </div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto">
                        <div class="dt-buttons btn-group flex-wrap">
                            <div class="btn-group">
                                <button
                                    class="btn buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                                    tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog"
                                    aria-expanded="false" data-bs-toggle="dropdown">
                                    <span><span class="d-flex align-items-center gap-2"><i
                                                class="icon-base ri ri-external-link-line icon-18px"></i> <span
                                                class="d-none d-sm-inline-block">Экспорт</span></span></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('regions.export') }}">Экспорт в CSV</a>
                                    </li>
                                </ul>
                            </div>
                            <a href="{{route('regions.create')}}" class="btn create-new btn-primary" tabindex="0"
                               aria-controls="DataTables_Table_0"
                               type="button">
                                <span><span class="d-flex align-items-center"><i
                                            class="icon-base ri ri-add-line icon-18px me-sm-1"></i><span
                                            class="d-none d-sm-inline-block">Добавить</span></span></span>
                            </a>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <form method="GET" action="{{ route('regions.index') }}">
                    <div class="row m-3 mx-2 my-0 justify-content-between">
                        <div
                            class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mb-2 mt-2">
                            <div class="dt-search d-flex align-items-center">
                                <input type="search"
                                       name="search"
                                       value="{{ request('search') }}"
                                       class="form-control form-control-sm"
                                       id="dt-search-0"
                                       placeholder="Поиск по названию или коду"
                                       aria-controls="DataTables_Table_0"
                                       style="border:1px solid #d1cfd4 !important; width: 250px;">
                                <button type="submit" class="btn btn-sm btn-primary ms-2">
                                    <i class="icon-base ri ri-search-line"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('regions.index') }}"
                                       class="btn btn-sm btn-outline-secondary ms-2">
                                        <i class="icon-base ri ri-close-line"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
                <div class="justify-content-between dt-layout-table">
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive">
                        <table class="datatables-basic table table-bordered table-responsive dataTable dtr-column"
                               id="DataTables_Table_0" aria-describedby="DataTables_Table_0_info" style="width: 100%;">
                            <thead>
                            <tr>
                                <th data-dt-column="0" class="control dt-orderable-none dtr-hidden" rowspan="1"
                                    colspan="1" aria-label="" style="display: none;"></th>
                                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-select dt-orderable-none"
                                    aria-label="">
                                    ID
                                </th>
                                <th data-dt-column="3" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Name: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Регион</span>
                                </th>
                                <th data-dt-column="4" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Email: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Пользователи</span>
                                </th>

                                <th data-dt-column="6" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Date: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Переводчики</span>
                                </th>
                                <th data-dt-column="7" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Корректоры</span>
                                </th>
                                <th data-dt-column="7" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Переведено</span>
                                </th>
                                <th data-dt-column="7" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">На проверке</span>
                                </th>
                                <th data-dt-column="8" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0">
                                    <span class="dt-column-title" role="button">Статус</span>
                                </th>
                                <th class="dt-orderable-none" data-dt-column="9" rowspan="1"
                                    colspan="1" aria-label="Actions">
                                    <span class="dt-column-title">Дата</span>
                                </th>
                                <th class="dt-orderable-none" data-dt-column="9" rowspan="1"
                                    colspan="1" aria-label="Actions">
                                    <span class="dt-column-title">Действия</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($regions as $region)
                                @php
                                    $stats = $region->getTranslationStats();
                                @endphp
                                <tr>
                                    <td class="dt-select">{{ $region->id }}</td>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center user-name">
                                            <div class="d-flex flex-column">
                                                <span class="emp_name text-truncate h6 mb-0">{{ $region->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $region->users->count() }}</td>
                                    <td>{{ $region->translators->count() }}</td>
                                    <td>{{ $region->proofreaders->count() }}</td>
                                    <td>{{ $stats['translated'] }}</td>
                                    <td>{{ $stats['proofread'] }}</td>
                                    <td>
                                        @if($region->is_active)
                                            <span class="badge rounded-pill bg-label-success">Активен</span>
                                        @else
                                            <span class="badge rounded-pill bg-label-danger">Неактивен</span>
                                        @endif
                                    </td>
                                    <td>{{ $region->created_at->format('d.m.Y') }}</td>
                                    <td class="d-flex align-items-center">
                                        <div class="d-inline-block">
                                            <a href="javascript:;"
                                               class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                               data-bs-toggle="dropdown">
                                                <i class="icon-base ri ri-more-2-line icon-22px"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                                <li>
                                                    <a href="" class="dropdown-item">
                                                        Скачать корпус региона
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('regions.edit', $region->id) }}"
                                                       class="dropdown-item">
                                                        Редактировать
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('regions.destroy', $region->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Вы уверены что хотите удалить этот регион?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            Удалить
                                                        </button>
                                                    </form>
                                                </li>

                                            </ul>
                                        </div>
                                        <a href="{{ route('regions.edit', $region->id) }}"
                                           class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit">
                                            <i class="icon-base ri ri-edit-box-line icon-22px"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mx-3 justify-content-between">
                    {{ $regions->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
