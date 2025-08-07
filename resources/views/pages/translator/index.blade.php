@extends('layouts.main')

@section('content')
    <div class="row gy-6">
        <div class="col-sm-3" style="height: 191px">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="avatar">
                        <div class="avatar-initial bg-success rounded-circle shadow-xs">
                            <i class="icon-base ri ri-verified-badge-fill icon-24px"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-1">Проверено</h6>
                    <div class="d-flex flex-wrap mb-1 align-items-center">
                        <h4 class="mb-0 me-2">{{ $translations->where('status', \App\Models\Translation::STATUS_PROOFREAD)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3" style="height: 191px">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="avatar">
                        <div class="avatar-initial bg-warning rounded-circle shadow-xs">
                            <i class="icon-base ri ri-time-fill icon-24px"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="mb-1">Ожидают проверки</h6>
                    <div class="d-flex flex-wrap mb-1 align-items-center">
                        <h4 class="mb-0 me-2">{{ $translations->where('status', \App\Models\Translation::STATUS_TRANSLATED)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3" style="height: 191px">
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
                        <h4 class="mb-0 me-2">{{ $translations->where('status', \App\Models\Translation::STATUS_REJECTED)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
