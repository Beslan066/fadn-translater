@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-header">Список предложений корпуса</h5>
            <div class="form-floating form-floating" style="margin-right: 10px">
                <input class="form-control" type="search" placeholder="Поиск ..." id="html5-search-input" style="border:1px solid #d1cfd4 !important">
                <label for="html5-search-input">Найти</label>
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
                                {{$sentence->sentence}}
                            </td>
                            <td>
                                <form action="">
                                    <button type="button" class="btn btn-danger waves-effect waves-light" onclick="return confirm('Вы действительно хотите безвозвратно удалить эти данные?');">Удалить</button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{$sentences->links('pagination::bootstrap-5')}}
        </div>

    </div>
@endsection
