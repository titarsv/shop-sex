@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Каталог товаров
@endsection
@section('content')

    <h1>Добавление товара</h1>

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
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Изображение товара</label>
                                <div class="form-element col-sm-3">
                                    @include('admin.layouts.form.image', [
                                     'key' => 'image_id'
                                    ])
                                </div>
                                <div class="form-element col-sm-7">
                                    <label class="gallery-label">Галлерея</label>
                                    @include('admin.layouts.form.gallery', [
                                     'key' => 'gallery',
                                     'gallery' => null
                                    ])
                                </div>
                            </div>
                        </div>
                        @include('admin.layouts.form.editor', [
                         'label' => 'Описание товара',
                         'key' => 'description',
                         'locale' => 'ru'
                        ])
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Плашка</label>
                                <div class="form-element col-sm-10">
                                    <select name="label" class="form-control">
                                        @foreach($labels as $label => $label_name)
                                            <option value="{!! $label !!}"
                                                    @if ($label == old('label'))
                                                    selected
                                                    @endif
                                            >{!! $label_name !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Старая цена</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="old_price" value="{!! old('old_price') ? old('old_price') : '' !!}" />
                                    @if($errors->has('old_price'))
                                        <p class="warning" role="alert">{!! $errors->first('old_price',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Цена</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="price" value="{!! old('price') !!}" />
                                    @if($errors->has('price'))
                                        <p class="warning" role="alert">{!! $errors->first('price',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Артикул</label>
                                <div class="form-element col-sm-10">
                                    <input type="text" class="form-control" name="articul" value="{!! old('articul') !!}" />
                                    @if($errors->has('articul'))
                                        <p class="warning" role="alert">{!! $errors->first('articul',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Наличие товара</label>
                                <div class="form-element col-sm-10">
                                    <select name="stock" class="form-control">
                                        @if(old('stock') !== null && !old('stock'))
                                            <option value="1">В наличии</option>
                                            <option value="0" selected>Нет в наличии</option>
                                        @else
                                            <option value="1" selected>В наличии</option>
                                            <option value="0">Нет в наличии</option>
                                        @endif
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
                        <h4>Связи</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Категория товара</label>
                                <div class="form-element col-sm-10">
                                    <select name="product_category_id[]" class="form-control chosen-select" multiple="multiple">
                                        <option value="0">Не выбрано</option>
                                        @foreach($categories as $category)
                                            <option value="{!! $category->id !!}"
                                                    @if (in_array($category->id, (array)old('product_category_id')))
                                                    selected
                                                    @endif
                                            >{!! $category->meta_title !!}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('product_category_id'))
                                        <p class="warning" role="alert">{!! $errors->first('product_category_id',':message') !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<label class="col-sm-2 text-right">Связанная категория</label>--}}
                                {{--<div class="form-element col-sm-10">--}}
                                    {{--<select name="product_related_category_id" class="form-control">--}}
                                        {{--<option value="0">Не выбрано</option>--}}
                                        {{--@foreach($categories as $category)--}}
                                            {{--<option value="{!! $category->id !!}"--}}
                                                    {{--@if ($category->id == old('product_related_category_id'))--}}
                                                    {{--selected--}}
                                                    {{--@endif--}}
                                            {{-->{!! $category->name !!}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Атрибуты товара</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="table table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr class="success">
                                                <td align="center">Выберите атрибут</td>
                                                <td align="center">Выберите значение атрибута</td>
                                                {{--<td align="center">Добавочная стоимость</td>--}}
                                                <td align="center">Действия</td>
                                            </tr>
                                        </thead>
                                        <tbody id="product-attributes">
                                            @if(old('product_attributes') !== null)
                                                @if(session('attributes_error'))
                                                    <tr>
                                                        <td colspan="2">
                                                            <p class="warning" role="alert">{!! session('attributes_error') !!}</p>
                                                        </td>
                                                    </tr>
                                                @endif
                                                @foreach(old('product_attributes') as $key => $attr)
                                                    <tr>
                                                        <td>
                                                            <select class="form-control" onchange="getAttributeValues($(this).val(), '{!! $key !!}')">
                                                                @foreach($attributes as $attribute)
                                                                    <option value="{!! $attribute->id !!}"
                                                                        @if ($attribute->id == $attr['id'])
                                                                            selected
                                                                        @endif
                                                                    >{!! $attribute->name !!}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td align="center" id="attribute-{!! $key !!}-values">
                                                            <input type="hidden" name="product_attributes[{!! $key !!}][id]" value="{!! $attr['id'] !!}"/>
                                                            <select class="form-control" name="product_attributes[{!! $key !!}][value]">';
                                                                @foreach($attributes as $attribute)
                                                                    @if($attribute->id == $attr['id'])
                                                                        @foreach($attribute->values as $value)
                                                                            <option value="{!! $value->id !!}"
                                                                                @if ($value->id == $attr['value'])
                                                                                    selected
                                                                                @endif
                                                                            >{!! $value->name !!}</option>
                                                                        @endforeach
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td align="center">
                                                            <button class="btn btn-danger" onclick="$(this).parent().parent().remove();">Удалить</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <input type="hidden" value="{!! $key !!}" id="attributes-iterator" />
                                            @else
                                                <input type="hidden" value="0" id="attributes-iterator" />
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td align="center">
                                                    <button type="button" id="add-attribute" onclick="getAttributes();" class="btn">Добавить</button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Вариации товара</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="form-element col-sm-12">
                                    <button style="float: right;" type="button" id="add-variation" onclick="addVariation($(this));" class="btn">Добавить вариацию</button>
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
        {{--CKEDITOR.replaceAll('editor', options);--}}
    {{--</script>--}}
    @include('admin.layouts.mce', ['editors' => $editors])
@endsection
@section('before_footer')
    {{--@include('admin.layouts.imagesloader')--}}
    @include('admin.media.assets')
@endsection
