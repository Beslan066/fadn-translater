@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-datatable text-nowrap">
            <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                <div class="row card-header mx-0 px-2">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <h5 class="card-title mb-0">Ваши переводы</h5></div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto">
                        <div class="dt-buttons btn-group flex-wrap">
                            <div class="btn-group">
                                <button
                                    class="btn buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                                    tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog"
                                    aria-expanded="false"><span><span class="d-flex align-items-center gap-2"><i
                                                class="icon-base ri ri-external-link-line icon-18px"></i> <span
                                                class="d-none d-sm-inline-block">Экспорт</span></span></span></button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="row m-3 mx-2 my-0 justify-content-between">
                    <form method="GET" action="{{ route('translator.translations') }}" class="w-100">
                        <div class="row mt-2">
                            <div class="col-md-3 mb-2">
                                <input type="search" name="search" class="form-control form-control-sm"
                                       placeholder="Поиск по тексту" value="{{ request('search') }}"
                                       style="border:1px solid #d1cfd4 !important">
                            </div>
                            <div class="col-md-3 mb-2">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Все статусы</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                        На проверке
                                    </option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>
                                        Подтвержден
                                    </option>
                                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>
                                        Отклонен
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary waves-effect waves-light">Применить</button>
                                    <a href="{{ route('translator.translations') }}" class="btn btn-sm btn-outline-secondary waves-effect">Сбросить</a>
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
                                <th data-dt-column="0" class="control dt-orderable-none dtr-hidden" rowspan="1"
                                    colspan="1" aria-label="" style="display: none;"><span
                                        class="dt-column-title"></span><span class="dt-column-order"></span></th>
                                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-select dt-orderable-none"
                                    aria-label=""><span class="dt-column-title"></span><span
                                        class="dt-column-order"></span>id
                                </th>
                                <th data-dt-column="3" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Name: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Оригинал</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="3" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Name: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Перевод</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="4" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Email: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Переведено</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="5" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Date: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Автор</span><span
                                        class="dt-column-order"></span></th>

                                <th data-dt-column="7" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Статус</span><span
                                        class="dt-column-order"></span></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($translations as $translation)
                                <tr>
                                    <td class="dt-select">{{$translation->id}}</td>
                                    <td style="white-space: normal;">
                                        <span class="emp_name text-truncate h6 mb-0">{{$translation->translated_text}}</span>
                                    </td>
                                    <td style="white-space: normal;">
                                        {{$translation->sentence->sentence }}
                                    </td>
                                        <td>{{$translation->created_at}}</td>
                                    <td class="dt-type-numeric">{{$translation->translator->name}}</td>
                                    <td>
                                      <span class="badge rounded-pill  bg-label-success">
                                        @if($translation->status === 2)
                                              Подтвержден
                                          @elseif($translation->status === 3)
                                              Отклонен
                                          @elseif($translation->status === 1)
                                                На проверке
                                          @endif
                                      </span>
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
                <div class="row mx-3 justify-content-between">
                    {{$translations->links('pagination::bootstrap-5')}}
                </div>
            </div>
        </div>
    </div>
@endsection
