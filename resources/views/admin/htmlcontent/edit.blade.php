@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    HTML-страницы
@endsection
@section('content')

    <h1>Редактирование страницы</h1>

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
                         'label' => 'Название',
                         'key' => 'name',
                         'locale' => 'ru',
                         'required' => true,
                         'item' => $content,
                        ])
                        @include('admin.layouts.form.editor', [
                         'label' => 'Содержимое страницы',
                         'key' => 'content',
                         'locale' => 'ru',
                         'item' => $content,
                         'languages' => $languages
                        ])
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Родительская страница</label>
                                <div class="form-element col-sm-10">
                                    <select name="parent_id" class="form-control">
                                        <option value="0">Не выбрано</option>
                                        @foreach($pages as $p)
                                            <option value="{!! $p->id !!}"
                                                    @if ($p->id == old('parent_id'))
                                                    selected
                                                    @elseif ($p->id == $content->parent_id)
                                                    selected
                                                    @endif
                                            >{!! $p->name !!}</option>
                                        @endforeach
                                    </select>
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
                        'item' => $content,
                        'locale' => 'ru',
                        'required' => true
                        ])
                        @include('admin.layouts.form.text', [
                        'label' => 'Meta description',
                        'key' => 'meta_description',
                        'item' => $content,
                        'locale' => 'ru'
                        ])
                        @include('admin.layouts.form.text', [
                        'label' => 'Meta keywords',
                        'key' => 'meta_keywords',
                        'item' => $content,
                        'locale' => 'ru'
                        ])
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Alias</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" data-translit="output" class="form-control" name="url_alias" value="{!! old('url_alias') ? old('url_alias') : $content->url_alias !!}" />
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
                                    <input type="text" class="form-control" name="robots" value="{!! old('robots') ? old('robots') : $content->robots !!}" />
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
                                <label class="col-sm-2 text-right">Порядок сортировки</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="sort_order" value="{!! old('sort_order') ? old('sort_order') : $content->sort_order !!}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Статус</label>
                                <div class="form-element col-sm-10">
                                    <select name="status" class="form-control">
                                        @if(old('status') || $content->status)
                                            <option value="1" selected>Включено</option>
                                            <option value="0">Отключено</option>
                                        @elseif(!old('status') || !$content->status)
                                            <option value="1">Включено</option>
                                            <option value="0" selected>Отключено</option>
                                        @endif
                                    </select>
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

    {{--<script src="/js/libs/transliterate.js"></script>--}}

    {{--<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>--}}
    {{--<script>--}}
        {{--CKEDITOR.replace( 'text-area', {--}}
            {{--filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',--}}
            {{--filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',--}}
        {{--});--}}
        {{--CKEDITOR.config.allowedContent = true;--}}
    {{--</script>--}}
    @include('admin.layouts.mce', ['editors' => $editors])
@endsection
@section('before_footer')
    @include('admin.media.assets')
@endsection