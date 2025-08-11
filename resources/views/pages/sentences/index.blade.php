@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-header">Список предложений корпуса</h5>
            <div class="form-floating form-floating" style="margin-right: 10px">
                <form method="GET" action="{{ route('sentences.index') }}" class="d-flex">
                    <input
                        class="form-control"
                        type="search"
                        name="search"
                        placeholder="Поиск ..."
                        id="html5-search-input"
                        style="border:1px solid #d1cfd4 !important"
                        value="{{ request('search') }}"
                    >
                    <button type="submit" class="btn btn-primary ms-2">Поиск</button>
                    @if(request('search'))
                        <a href="{{ route('sentences.index') }}" class="btn btn-outline-secondary ms-2">Сбросить</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                <tr>
                    <th>id</th>
                    <th>Предложение</th>
                    <th>Действие</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                @foreach($sentences as $sentence)
                    <tr class="responsive-tr">
                        <td class="table-item-max-width">
                            <span>{{$sentence->id}}</span>
                        </td>
                        <td class="table-item-max-width" style="word-wrap: break-word; max-width: 300px; white-space: normal;">
                            @if(request('search'))
                                {!! preg_replace('/('.preg_quote(request('search'), '/').')/i', '<mark>$1</mark>', $sentence->sentence) !!}
                            @else
                                {{$sentence->sentence}}
                            @endif
                        </td>
                        <td>
                            <form action="{{route('sentences.destroy', $sentence->id)}}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger waves-effect waves-light" onclick="return confirm('Вы действительно хотите безвозвратно удалить эти данные?');">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{$sentences->appends(['search' => request('search')])->links('pagination::bootstrap-5')}}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('html5-search-input').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    </script>
@endpush

