@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-datatable text-nowrap">
            <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                <div class="row card-header mx-0 px-2">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <h5 class="card-title mb-0">Проверенные вами переводы</h5></div>
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
                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light show"
                                    data-bs-toggle="dropdown" aria-expanded="true"></i>Фильтр
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="row m-3 mx-2 my-0 justify-content-between">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto mb-2 mt-2">
                        <div class="dt-search"><input type="search"
                                                      class="form-control form-control-sm"
                                                      id="dt-search-0"
                                                      placeholder="Введите для поиска"
                                                      aria-controls="DataTables_Table_0"
                                                      style="border:1px solid #d1cfd4 !important">
                        </div>
                    </div>
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
                                    tabindex="0"><span class="dt-column-title" role="button">Перевод</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="4" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Email: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Оригинал</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="5" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Date: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Переведено</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="5" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Date: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">На проверке</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="7" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Статус</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="7" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Сообщение</span><span
                                        class="dt-column-order"></span></th>
                                <th class="d-flex align-items-center dt-orderable-none" data-dt-column="8" rowspan="1"
                                    colspan="1" aria-label="Actions"><span class="dt-column-title">Действие</span><span
                                        class="dt-column-order"></span></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($translations as $translation)
                                <tr>
                                    <td class="dt-select">{{$translation->id}}</td>
                                    <td style="white-space: normal;">
                                        <div class="d-flex justify-content-start align-items-center user-name">
                                            <div class="d-flex flex-column">
                                                <span class="emp_name text-truncate h6 mb-0">{{$translation->translated_text}}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="white-space: normal;">{{$translation->sentence->sentence}}</td>
                                    <td>{{$translation->created_at}}</td>
                                    <td class="dt-type-numeric">{{$translation->translator->name}}</td>
                                    <td>
                                      <span class="badge rounded-pill  bg-label-success">
                                        @if($translation->status === 2)
                                              Подтвержден
                                          @elseif($translation->status === 3)
                                                Отклонен
                                          @endif
                                      </span>
                                    </td>
                                    <td style="white-space: normal;">
                                        <p>{{$translation->reject_reason}}</p>
                                    </td>
                                    <td class="d-flex align-items-center">
                                        <div class="d-inline-block"><a href="javascript:;"
                                                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                                                                       data-bs-toggle="dropdown"><i
                                                    class="icon-base ri ri-more-2-line icon-22px"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-end m-0">
                                                <li><a href="javascript:;" class="dropdown-item">Details</a></li>
                                                <li><a href="javascript:;" class="dropdown-item">Archive</a></li>
                                                <div class="dropdown-divider"></div>
                                                <li><a href="javascript:;"
                                                       class="dropdown-item text-danger delete-record">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="javascript:;"
                                           class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i
                                                class="icon-base ri ri-edit-box-line icon-22px"></i></a></td>
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
