@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    SEO редиректы
@endsection
@section('content')

    <h1>SEO редиректы</h1>

    @if (session('message-success'))
        <div class="alert alert-success">
            {{ session('message-success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session('message-error'))
        <div class="alert alert-danger">
            {{ session('message-error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="redirects" method="post" id="settings-form">
        {!! csrf_field() !!}
        <div class="settings row">
            <div class="col-sm-4">

            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <label for="search" class="input-group-addon">Поиск:</label>
                    <input type="text" id="search" name="search" placeholder="Введите текст..." class="form-control input-sm" value="{{ $current_search or '' }}" />
                </div>
            </div>
            <div class="col-sm-4">

            </div>
        </div>
    </form>

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading text-right">
                <a href="/admin/seo/redirects/create" class="btn btn-primary">Добавить новый</a>
            </div>
            <div class="table table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr class="success">
                        <td>Старый URL</td>
                        <td>Новый URL</td>
                        <td align="center">Действия</td>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($redirects as $redirect)
                        <tr>
                            <td>{{ $redirect->old_url }}</td>
                            <td>{{ $redirect->new_url }}</td>
                            <td class="actions" align="center">
                                <a class="btn btn-primary" href="/admin/seo/redirects/edit/{!! $redirect->id !!}">
                                    <i class="glyphicon glyphicon-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmRedirectDelete('{!! $redirect->id !!}', '{!! $redirect->old_url !!}')">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" align="center">Нет СЕО записей!</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="panel-footer text-right">
                {{ $redirects->links() }}
            </div>
        </div>
    </div>

    <div id="seo-delete-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Подтверждение удаления</h4>
                </div>
                <div class="modal-body">
                    <p>Удалить запись <span id="category-name"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <a type="button" class="btn btn-primary" id="confirm">Удалить</a>
                </div>
            </div>
        </div>
    </div>

@endsection
