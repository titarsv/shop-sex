@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Запись новости
@endsection
@section('content')

    <h1>Редактирование новости {{ $article->title }}</h1>

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
                        <h4>Общая информация</h4>
                    </div>
                    <div class="panel-body">
                        @include('admin.layouts.form.string', [
                         'label' => 'Заголовок',
                         'key' => 'title',
                         'locale' => 'ru',
                         'required' => true,
                         'item' => $article
                        ])
                        @include('admin.layouts.form.editor', [
                         'label' => 'Текст новости',
                         'key' => 'text',
                         'locale' => 'ru',
                         'item' => $article
                        ])
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Изображение</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Выберите изображение </label>
                                <div class="form-element col-sm-3">
                                    @include('admin.layouts.form.image', [
                                     'key' => 'image_id',
                                     'image' => $article->image
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>SEO</h4>
                    </div>
                    <div class="panel-body">
                        @include('admin.layouts.form.string', [
                        'label' => 'Title',
                        'key' => 'meta_title',
                        'item' => $article,
                        'locale' => 'ru',
                        'required' => true
                        ])
                        @include('admin.layouts.form.text', [
                        'label' => 'Meta description',
                        'key' => 'meta_description',
                        'item' => $article,
                        'locale' => 'ru'
                        ])
                        @include('admin.layouts.form.text', [
                        'label' => 'Meta keywords',
                        'key' => 'meta_keywords',
                        'item' => $article,
                        'locale' => 'ru'
                        ])
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Alias</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" data-translit="output" class="form-control" name="url_alias" value="{!! old('url_alias') ? old('url_alias') : $article->url_alias!!}" />
                                    @if($errors->has('url_alias'))
                                        <p class="warning" role="alert">{!! $errors->first('url_alias',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Robots</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="robots" value="{!! old('robots') ? old('robots') : $article->robots !!}" />
                                    @if($errors->has('robots'))
                                        <p class="warning" role="alert">{!! $errors->first('robots',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Настройки</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Опубликовать</label>
                                <div class="form-element col-sm-10">
                                    <select name="published" class="form-control">
                                        @if(old('published') || $article->published)
                                            <option value="1" selected>Да</option>
                                            <option value="0">Нет</option>
                                        @elseif(!old('published') || !$article->published)
                                            <option value="1">Да</option>
                                            <option value="0" selected>Нет</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
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

    {{--<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>--}}
    {{--<script>--}}
        {{--CKEDITOR.replace( 'text-area', {--}}
            {{--filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',--}}
            {{--filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',--}}
            {{--filebrowserBrowseUrl: '/laravel-filemanager?type=Files',--}}
            {{--filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{csrf_token()}}'--}}
        {{--});--}}
        {{--CKEDITOR.config.allowedContent = true;--}}
    {{--</script>--}}

    {{--<script src="/js/libs/transliterate.js"></script>--}}
    @include('admin.layouts.mce', ['editors' => $editors])
@endsection
@section('before_footer')
    {{--@include('admin.layouts.imagesloader')--}}
    @include('admin.media.assets')
@endsection
