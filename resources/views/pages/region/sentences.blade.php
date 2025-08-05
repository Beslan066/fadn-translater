@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-datatable text-nowrap">
            <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                <div class="row card-header mx-0 px-2">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <h5 class="card-title mb-0">DataTable with Buttons</h5></div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto">
                        <div class="dt-buttons btn-group flex-wrap">
                            <div class="btn-group">
                                <button
                                    class="btn buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                                    tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog"
                                    aria-expanded="false"><span><span class="d-flex align-items-center gap-2"><i
                                                class="icon-base ri ri-external-link-line icon-18px"></i> <span
                                                class="d-none d-sm-inline-block">Выгрузить</span></span></span></button>
                            </div>
                            <button class="btn create-new btn-primary" tabindex="0" aria-controls="DataTables_Table_0"
                                    type="button"><span><span class="d-flex align-items-center"><i
                                            class="icon-base ri ri-add-line icon-18px me-sm-1"></i><span
                                            class="d-none d-sm-inline-block">Добавить</span></span></span>
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="row m-3 mx-2 my-0 justify-content-between">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <div class="dt-length"><label for="dt-length-0">Show<select name="DataTables_Table_0_length"
                                                                                    aria-controls="DataTables_Table_0"
                                                                                    class="form-select form-select-sm"
                                                                                    id="dt-length-0">
                                    <option value="7">7</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>entries</label></div>
                    </div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto">
                        <div class="dt-search"><label for="dt-search-0">Search:</label><input type="search"
                                                                                              class="form-control form-control-sm"
                                                                                              id="dt-search-0"
                                                                                              placeholder="Type search here"
                                                                                              aria-controls="DataTables_Table_0">
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
                                        class="dt-column-order"></span><input class="form-check-input" type="checkbox"
                                                                              aria-label="Select all rows"></th>
                                <th data-dt-column="3" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Name: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Name</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="4" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Email: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Email</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="5" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Date: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Date</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="6" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc dt-type-numeric"
                                    aria-label="Salary: Activate to sort" tabindex="0"><span class="dt-column-title"
                                                                                             role="button">Salary</span><span
                                        class="dt-column-order"></span></th>
                                <th data-dt-column="7" rowspan="1" colspan="1"
                                    class="dt-orderable-asc dt-orderable-desc" aria-label="Status: Activate to sort"
                                    tabindex="0"><span class="dt-column-title" role="button">Status</span><span
                                        class="dt-column-order"></span></th>
                                <th class="d-flex align-items-center dt-orderable-none" data-dt-column="8" rowspan="1"
                                    colspan="1" aria-label="Actions"><span class="dt-column-title">Actions</span><span
                                        class="dt-column-order"></span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="control dtr-hidden" tabindex="0" style="display: none;"></td>
                                <td class="dt-select"><input aria-label="Select row" class="form-check-input"
                                                             type="checkbox"></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">GG</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="emp_name text-truncate h6 mb-0">Glyn Giacoppo</span>
                                            <small class="emp_post text-truncate">Software Test Engineer</small>
                                        </div>
                                    </div>
                                </td>
                                <td>ggiacoppo2r@apache.org</td>
                                <td>04/15/2021</td>
                                <td class="dt-type-numeric">$24973.48</td>
                                <td>
              <span class="badge rounded-pill  bg-label-success">
                Professional
              </span>
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
                                            <li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;"
                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i
                                            class="icon-base ri ri-edit-box-line icon-22px"></i></a></td>
                            </tr>
                            <tr>
                                <td class="control dtr-hidden" tabindex="0" style="display: none;"></td>
                                <td class="dt-select"><input aria-label="Select row" class="form-check-input"
                                                             type="checkbox"></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                <img src="../../assets/img/avatars/10.png" alt="Avatar"
                                                     class="rounded-circle">
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="emp_name text-truncate h6 mb-0">Evangelina Carnock</span>
                                            <small class="emp_post text-truncate">Cost Accountant</small>
                                        </div>
                                    </div>
                                </td>
                                <td>ecarnock2q@washington.edu</td>
                                <td>01/26/2021</td>
                                <td class="dt-type-numeric">$23704.82</td>
                                <td>
              <span class="badge rounded-pill  bg-label-warning">
                Resigned
              </span>
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
                                            <li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;"
                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i
                                            class="icon-base ri ri-edit-box-line icon-22px"></i></a></td>
                            </tr>
                            <tr>
                                <td class="control dtr-hidden" tabindex="0" style="display: none;"></td>
                                <td class="dt-select"><input aria-label="Select row" class="form-check-input"
                                                             type="checkbox"></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                <img src="../../assets/img/avatars/7.png" alt="Avatar"
                                                     class="rounded-circle">
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="emp_name text-truncate h6 mb-0">Olivette Gudgin</span>
                                            <small class="emp_post text-truncate">Paralegal</small>
                                        </div>
                                    </div>
                                </td>
                                <td>ogudgin2p@gizmodo.com</td>
                                <td>04/09/2021</td>
                                <td class="dt-type-numeric">$15211.60</td>
                                <td>
              <span class="badge rounded-pill  bg-label-success">
                Professional
              </span>
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
                                            <li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;"
                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i
                                            class="icon-base ri ri-edit-box-line icon-22px"></i></a></td>
                            </tr>
                            <tr>
                                <td class="control dtr-hidden" tabindex="0" style="display: none;"></td>
                                <td class="dt-select"><input aria-label="Select row" class="form-check-input"
                                                             type="checkbox"></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial rounded-circle bg-label-dark">RP</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="emp_name text-truncate h6 mb-0">Reina Peckett</span>
                                            <small class="emp_post text-truncate">Quality Control Specialist</small>
                                        </div>
                                    </div>
                                </td>
                                <td>rpeckett2o@timesonline.co.uk</td>
                                <td>05/20/2021</td>
                                <td class="dt-type-numeric">$16619.40</td>
                                <td>
              <span class="badge rounded-pill  bg-label-warning">
                Resigned
              </span>
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
                                            <li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;"
                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i
                                            class="icon-base ri ri-edit-box-line icon-22px"></i></a></td>
                            </tr>
                            <tr>
                                <td class="control dtr-hidden" tabindex="0" style="display: none;"></td>
                                <td class="dt-select"><input aria-label="Select row" class="form-check-input"
                                                             type="checkbox"></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial rounded-circle bg-label-dark">AB</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="emp_name text-truncate h6 mb-0">Alaric Beslier</span>
                                            <small class="emp_post text-truncate">Tax Accountant</small>
                                        </div>
                                    </div>
                                </td>
                                <td>abeslier2n@zimbio.com</td>
                                <td>04/16/2021</td>
                                <td class="dt-type-numeric">$19366.53</td>
                                <td>
              <span class="badge rounded-pill  bg-label-warning">
                Resigned
              </span>
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
                                            <li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;"
                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i
                                            class="icon-base ri ri-edit-box-line icon-22px"></i></a></td>
                            </tr>
                            <tr>
                                <td class="control dtr-hidden" tabindex="0" style="display: none;"></td>
                                <td class="dt-select"><input aria-label="Select row" class="form-check-input"
                                                             type="checkbox"></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                <img src="../../assets/img/avatars/2.png" alt="Avatar"
                                                     class="rounded-circle">
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="emp_name text-truncate h6 mb-0">Edwina Ebsworth</span>
                                            <small class="emp_post text-truncate">Human Resources Assistant</small>
                                        </div>
                                    </div>
                                </td>
                                <td>eebsworth2m@sbwire.com</td>
                                <td>09/27/2021</td>
                                <td class="dt-type-numeric">$19586.23</td>
                                <td>
              <span class="badge rounded-pill  bg-label-primary">
                Current
              </span>
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
                                            <li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;"
                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i
                                            class="icon-base ri ri-edit-box-line icon-22px"></i></a></td>
                            </tr>
                            <tr>
                                <td class="control dtr-hidden" tabindex="0" style="display: none;"></td>
                                <td class="dt-select"><input aria-label="Select row" class="form-check-input"
                                                             type="checkbox"></td>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar me-2">
                                                <span class="avatar-initial rounded-circle bg-label-danger">RH</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="emp_name text-truncate h6 mb-0">Ronica Hasted</span>
                                            <small class="emp_post text-truncate">Software Consultant</small>
                                        </div>
                                    </div>
                                </td>
                                <td>rhasted2l@hexun.com</td>
                                <td>07/04/2021</td>
                                <td class="dt-type-numeric">$24866.66</td>
                                <td>
              <span class="badge rounded-pill  bg-label-warning">
                Resigned
              </span>
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
                                            <li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="javascript:;"
                                       class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i
                                            class="icon-base ri ri-edit-box-line icon-22px"></i></a></td>
                            </tr>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                </div>
                <div class="row mx-3 justify-content-between">
                    <div
                        class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <div class="dt-info" aria-live="polite" id="DataTables_Table_0_info" role="status">Showing 1 to
                            7 of 100 entries
                        </div>
                    </div>
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto ms-auto">
                        <div class="dt-paging">
                            <nav aria-label="pagination">
                                <ul class="pagination">
                                    <li class="dt-paging-button page-item disabled">
                                        <button class="page-link first" role="link" type="button"
                                                aria-controls="DataTables_Table_0" aria-disabled="true"
                                                aria-label="First" data-dt-idx="first" tabindex="-1"><i
                                                class="icon-base ri ri-skip-back-mini-line scaleX-n1-rtl icon-22px"></i>
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item disabled">
                                        <button class="page-link previous" role="link" type="button"
                                                aria-controls="DataTables_Table_0" aria-disabled="true"
                                                aria-label="Previous" data-dt-idx="previous" tabindex="-1"><i
                                                class="icon-base ri ri-arrow-left-s-line scaleX-n1-rtl icon-22px"></i>
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item active">
                                        <button class="page-link" role="link" type="button"
                                                aria-controls="DataTables_Table_0" aria-current="page" data-dt-idx="0">1
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item">
                                        <button class="page-link" role="link" type="button"
                                                aria-controls="DataTables_Table_0" data-dt-idx="1">2
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item">
                                        <button class="page-link" role="link" type="button"
                                                aria-controls="DataTables_Table_0" data-dt-idx="2">3
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item">
                                        <button class="page-link" role="link" type="button"
                                                aria-controls="DataTables_Table_0" data-dt-idx="3">4
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item">
                                        <button class="page-link" role="link" type="button"
                                                aria-controls="DataTables_Table_0" data-dt-idx="4">5
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item disabled">
                                        <button class="page-link ellipsis" role="link" type="button"
                                                aria-controls="DataTables_Table_0" aria-disabled="true"
                                                data-dt-idx="ellipsis" tabindex="-1">…
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item">
                                        <button class="page-link" role="link" type="button"
                                                aria-controls="DataTables_Table_0" data-dt-idx="14">15
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item">
                                        <button class="page-link next" role="link" type="button"
                                                aria-controls="DataTables_Table_0" aria-label="Next" data-dt-idx="next">
                                            <i class="icon-base ri ri-arrow-right-s-line scaleX-n1-rtl icon-22px"></i>
                                        </button>
                                    </li>
                                    <li class="dt-paging-button page-item">
                                        <button class="page-link last" role="link" type="button"
                                                aria-controls="DataTables_Table_0" aria-label="Last" data-dt-idx="last">
                                            <i class="icon-base ri ri-skip-forward-mini-line scaleX-n1-rtl icon-22px"></i>
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
