@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Управление предложениями региона</h5>
                <form method="GET" action="{{ route('region-admin.sentences') }}" class="form-inline">
                    <div class="dt-search d-flex align-items-center">
                        <input type="search" name="search" value="" class="form-control form-control-sm" id="dt-search-0" placeholder="Поиск по названию или коду" aria-controls="DataTables_Table_0" style="border:1px solid #d1cfd4 !important; width: 250px;">
                        <button type="submit" class="btn btn-sm btn-primary ms-2 waves-effect waves-light">
                            <i class="icon-base ri ri-search-line"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('region-admin.bulk-complete') }}" id="bulk-form">
            @csrf
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-success btn-sm">
                            Пометить выбранные как завершенные
                        </button>
                    </div>
                    <div>
                        {{ $sentences->links() }}
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="50"><input type="checkbox" id="select-all"></th>
                                <th>Предложение</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sentences as $sentence)
                                <tr>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="sentence_ids[]"
                                            value="{{ $sentence->id }}"
                                            {{ $sentence->translationForRegion && $sentence->translationForRegion->status === \App\Models\Translation::STATUS_PROOFREAD ? 'disabled' : '' }}
                                        >
                                    </td>
                                    <td>{{ $sentence->sentence }}</td>
                                    <td>
                                        @if($sentence->translationForRegion)
                                            @if($sentence->translationForRegion->status === \App\Models\Translation::STATUS_PROOFREAD)
                                                <span class="badge bg-success">Завершено</span>
                                            @else
                                                <span class="badge bg-warning">В работе</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Не начато</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sentence->translationForRegion && $sentence->translationForRegion->status === \App\Models\Translation::STATUS_PROOFREAD)                                            <form method="POST" action="{{ route('region-admin.mark-completed') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="sentence_id" value="{{ $sentence->id }}">
                                                <input type="hidden" name="status" value="available">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Вернуть в работу
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('region-admin.mark-completed') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="sentence_id" value="{{ $sentence->id }}">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    Переведено
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    {{ $sentences->links() }}
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('select-all').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('input[name="sentence_ids[]"]:not(:disabled)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });
    </script>
@endsection
