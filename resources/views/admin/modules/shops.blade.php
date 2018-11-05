@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Модули
@endsection
@section('content')

    <h1>{!! $module->name !!}</h1>

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
    <div class="form">
        <form method="post">
            {!! csrf_field() !!}
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Карточки</h4>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="panel-body">
                        <div class="table slideshow-images">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="success">
                                        <th align="center" class="col-md-6">Изображение</th>
                                        <th align="center" class="col-md-5">Адрес</th>
                                        <th align="center" class="col-md-1">Действия</th>
                                    </tr>
                                </thead>
                                <tbody id="modules-table">
                                    @forelse($slideshow as $key => $slide)
                                        <tr>
                                            <td class="col-md-6">
                                                <input type="hidden" id="module-image-{!! $key !!}" name="slide[{!! $key !!}][image_id]" value="{!! $slide->image_id !!}" />
                                                <div id="module-image-output-{!! $key !!}" class="module-image">
                                                    <img src="{!! $slide->image->url() !!}" />
                                                    <button type="button" class="btn btn-del" data-delete="{!! $key !!}" data-toggle="tooltip" data-placement="bottom" title="Удалить изображение">X</button>
                                                    <button type="button" data-open="module-image" data-key="{!! $key !!}" class="btn">Выбрать изображение</button>
                                                </div>
                                            </td>
                                            <td class="col-md-5">
                                                <div>
                                                    <input type="text" name="slide[{!! $key !!}][slide_title]" class="form-control" value="{!! $slide->slide_title !!}" />
                                                    <span style="color: red">
                                                        @if($errors->has('slide.' . $key . '.slide_title'))
                                                            {{ $errors->first('slide.' . $key . '.slide_title',':message')  }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="col-md-1" align="center">
                                                <button class="btn btn-danger" onclick="$(this).parent().parent().remove();">Удалить</button>
                                                @if($key == count($slideshow) - 1)
                                                    <input type="hidden" value="{!! $key !!}" id="slideshow-iterator" />
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty">
                                            <td colspan="3" align="center">Нет добавленных слайдов!</td>
                                        </tr>
                                        <input type="hidden" value="0" id="slideshow-iterator" />
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td align="center"><button type="button" id="button-add-shop" class="btn">Добавить слайд</button></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
@section('before_footer')
    @include('admin.layouts.imagesloader')
@endsection
