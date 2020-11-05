@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    SEO
@endsection
@section('content')

    <h1>Добавление SEO редиректа</h1>

    @if(session('message-error'))
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
                        <h4>SEO</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Старый Url</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="old_url" value="{!! old('old_url') !!}" />
                                    @if($errors->has('old_url'))
                                        <p class="warning" role="alert">{!! $errors->first('old_url',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Новый Url</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="new_url" value="{!! old('new_url') !!}" />
                                    @if($errors->has('new_url'))
                                        <p class="warning" role="alert">{!! $errors->first('new_url',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-primary">Сохранить</button>
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
@include('admin.layouts.footer')

