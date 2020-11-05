@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    SEO
@endsection
@section('content')

    <h1>Добавление SEO записи</h1>

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
                                <label class="col-sm-2 text-right control-label">Url</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="url" value="{!! old('url') !!}" />
                                    @if($errors->has('url'))
                                        <p class="warning" role="alert">{!! $errors->first('url',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @include('admin.layouts.form.string', [
                         'label' => 'Название',
                         'key' => 'name',
                         'locale' => 'ru',
                         'required' => true
                        ])
                        @include('admin.layouts.form.editor', [
                         'label' => 'Описание товара',
                         'key' => 'description',
                         'locale' => 'ru'
                        ])
                        @include('admin.layouts.form.string', [
                        'label' => 'Title',
                        'key' => 'meta_title',
                        'locale' => 'ru',
                        'required' => true
                        ])
                        @include('admin.layouts.form.text', [
                        'label' => 'Meta description',
                        'key' => 'meta_description',
                        'locale' => 'ru'
                        ])
                        @include('admin.layouts.form.text', [
                        'label' => 'Meta keywords',
                        'key' => 'meta_keywords',
                        'locale' => 'ru'
                        ])
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Canonical</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="canonical" value="{!! old('canonical') !!}" />
                                    @if($errors->has('canonical'))
                                        <p class="warning" role="alert">{!! $errors->first('canonical',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Robots</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="robots" value="{!! old('robots') !!}" />
                                    @if($errors->has('robots'))
                                        <p class="warning" role="alert">{!! $errors->first('robots',':message') !!}</p>
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
                                <button type="submit" class="btn">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{--<script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>--}}
    {{--<script>--}}
        {{--var options = {--}}
            {{--filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',--}}
            {{--filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',--}}
            {{--filebrowserBrowseUrl: '/laravel-filemanager?type=Files',--}}
            {{--filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'--}}
        {{--};--}}
    {{--</script>--}}
    {{--<script>--}}
        {{--CKEDITOR.replace('text-area', options);--}}
    {{--</script>--}}
    @include('admin.layouts.mce', ['editors' => $editors])
@endsection
@section('before_footer')
    {{--@include('admin.layouts.imagesloader')--}}
    @include('admin.media.assets')
@endsection
@include('admin.layouts.footer')

