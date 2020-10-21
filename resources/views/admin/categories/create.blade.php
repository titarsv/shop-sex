@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Категории
@endsection
@section('content')

    <h1>Добавление категории</h1>

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
                        <h4>Общая информация</h4>
                    </div>
                    <div class="panel-body">
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
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Родительская категория</label>
                                <div class="form-element col-sm-10">
                                    <select name="parent_id" class="form-control">
                                        <option value="0">Не выбрано</option>
                                        @foreach($categories as $category)
                                            <option value="{!! $category->id !!}"
                                                    @if ($category->id == old('parent_id'))
                                                    selected
                                                    @endif
                                            >{!! $category->name !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Выберите изображение </label>
                                <div class="form-element col-sm-3">
                                    @include('admin.layouts.form.image', [
                                     'key' => 'image_id'
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Связанный атрибут (для фильтрации в главном меню)</label>
                                <div class="form-element col-sm-10">
                                    <select name="related_attribute_ids[]" class="form-control chosen-select">
                                        <option value="null">Не выбрано</option>
                                        @foreach($attributes as $attribute)
                                            <option value="{!! $attribute->id !!}"
                                                @if (is_array(old('related_attribute_ids')) && in_array($attribute->id, old('related_attribute_ids')))
                                                selected
                                                @endif
                                            >{!! $attribute->name !!}</option>
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
                                <label class="col-sm-2 text-right control-label">Alias</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" data-translit="output" class="form-control" name="url_alias" value="{!! old('url_alias') !!}" />
                                    @if($errors->has('url_alias'))
                                        <p class="warning" role="alert">{!! $errors->first('url_alias',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
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
                    <div class="panel-heading">
                        <h4>Настройки</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Порядок сортировки</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="sort_order" value="{!! old('sort_order') ? old('sort_order') : 0 !!}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Статус</label>
                                    <div class="form-element col-sm-10">
                                    <select name="status" class="form-control">
                                        @if(old('status') !== null && !old('status'))
                                            <option value="1">Включено</option>
                                            <option value="0" selected>Отключено</option>
                                        @else
                                            <option value="1" selected>Включено</option>
                                            <option value="0">Отключено</option>
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

