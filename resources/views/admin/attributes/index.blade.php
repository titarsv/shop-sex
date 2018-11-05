@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Список атрибутов
@endsection
@section('content')

    <div class="content-title">
        <div class="row">
            <div class="col-sm-10">
                <h1>Список атрибутов</h1>
            </div>
            <div class="col-sm-2 text-right">
                <a href="/admin/attributes/create" class="btn">Добавить</a>
            </div>
        </div>
    </div>

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

    <div class="panel-group">
        <div class="panel panel-default">
            <div class="table table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr class="success">
                        <td>Название</td>
                        <td>Значения</td>
                        <td align="center">Действия</td>
                    </tr>
                    </thead>
                    <tbody>

                    @forelse($attributes as $attribute)
                        <tr>
                            <td>{{ $attribute->name }}</td>
                            <td>
                                <ul class="nav">
                                    @forelse($attribute->values as $value)
                                        <li>{!! $value->name !!}</li>
                                    @empty
                                        <li>Нет добавленных значений!</li>
                                    @endforelse
                                </ul>
                            </td>
                            <td class="actions" align="center">
                                <a class="btn btn-primary" href="/admin/attributes/edit/{!! $attribute->id !!}" data-toggle="tooltip" data-placement="top" title="Редактировать">
                                    <i class="glyphicon glyphicon-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete('attributes', '{!! $attribute->id !!}', '{{ $attribute->name }}')" data-toggle="tooltip" data-placement="top" title="Удалить">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="colspan">Нет добавленных атрибутов!</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($attributes->links())
                <div class="panel-footer text-right">
                    {{ $attributes->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="attributes-delete-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Подтверждение удаления</h4>
                </div>
                <div class="modal-body">
                    <p>Удалить атрибут <span id="attributes-name"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <a type="button" class="btn btn-primary" id="confirm">Удалить</a>
                </div>
            </div>
        </div>
    </div>

@endsection
