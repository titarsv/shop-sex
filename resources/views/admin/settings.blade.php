@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Настройки
@endsection
@section('content')

    <h1>Настройки магазина</h1>

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
                        <h4>Мета-теги</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right control-label">Мета-тег Title</label>
                                <div class="form-element col-sm-10">
                                    @if(old('meta_title') !== null)
                                        <input type="text" class="form-control" name="meta_title" value="{!! old('meta_title') !!}" />
                                        @if($errors->has('meta_title'))
                                            <p class="warning" role="alert">{!! $errors->first('meta_title',':message') !!}</p>
                                        @endif
                                    @else
                                        <input type="text" class="form-control" name="meta_title" value="{!! $settings->meta_title !!}" />
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Мета-тег Description</label>
                                <div class="form-element col-sm-10">
                                    @if(old('meta_description') !== null)
                                        <textarea name="meta_description" class="form-control" rows="6">{!! old('meta_description') !!}</textarea>
                                        @if($errors->has('meta_description'))
                                            <p class="warning" role="alert">{!! $errors->first('meta_description',':message') !!}</p>
                                        @endif
                                    @else
                                        <textarea name="meta_description" class="form-control" rows="6">{!! $settings->meta_description !!}</textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Мета-тег Keywords</label>
                                <div class="form-element col-sm-10">
                                    @if(old('meta_keywords') !== null)
                                        <textarea name="meta_keywords" class="form-control" rows="6">{!! old('meta_keywords') !!}</textarea>
                                        @if($errors->has('meta_description'))
                                            <p class="warning" role="alert">{!! $errors->first('meta_description',':message') !!}</p>
                                        @endif
                                    @else
                                        <textarea name="meta_keywords" class="form-control" rows="6">{!! $settings->meta_keywords !!}</textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Текст на главной странице</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Содержание</label>
                                <div class="form-element col-sm-10">
                                    <textarea id="text-area" name="about" class="form-control" rows="6">{!! old('about') ? old('about') : $settings->about  !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Пользовательское соглашение</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Содержание</label>
                                <div class="form-element col-sm-10">
                                    <textarea id="text-area-terms" name="terms" class="form-control" rows="6">{!! old('terms') ? old('terms') : $settings->terms  !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Телефоны</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Мобильный</label>
                                <div class="form-element col-sm-10">
                                    @if(old('main_phone_1') !== null)
                                        <input type="text" class="form-control" name="main_phone_1" value="{!! old('main_phone_1') !!}" />
                                        @if($errors->has('main_phone_1'))
                                            <p class="warning" role="alert">{!! $errors->first('main_phone_1',':message') !!}</p>
                                        @endif
                                    @else
                                        <input type="text" class="form-control" name="main_phone_1" value="{!! $settings->main_phone_1 !!}" />
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Рабочий</label>
                                <div class="form-element col-sm-10">
                                    @if(old('main_phone_2') !== null)
                                        <input type="text" class="form-control" name="main_phone_2" value="{!! old('main_phone_2') !!}" />
                                        @if($errors->has('main_phone_2'))
                                            <p class="warning" role="alert">{!! $errors->first('main_phone_2',':message') !!}</p>
                                        @endif
                                    @else
                                        <input type="text" class="form-control" name="main_phone_2" value="{!! $settings->main_phone_2 !!}" />
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group phones">
                            <div class="row">
                                <label class="col-sm-2 text-right">Дополнительные</label>
                                <div class="form-element col-sm-10">
                                    @if(old('other_phones'))
                                        {{--@foreach(old('other_phones') as $key => $phone)--}}
                                            {{--<div class="input-group">--}}
                                                {{--<input type="text" name="other_phones[]" class="form-control" value="{!! $phone !!}" />--}}
                                                {{--<span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Удалить" onclick="$(this).parent().remove();">--}}
                                                    {{--<i class="glyphicon glyphicon-trash"></i>--}}
                                                {{--</span>--}}
                                            {{--</div>--}}
                                            {{--@if($errors->has('other_phones.' . $key))--}}
                                                {{--<p class="warning" role="alert">{!! $errors->first('other_phones.' . $key,':message') !!}</p>--}}
                                            {{--@endif--}}
                                        {{--@endforeach@foreach(old('other_phones') as $key => $phone)--}}
                                            {{--<div class="input-group">--}}
                                                {{--<input type="text" name="other_phones[]" class="form-control" value="{!! $phone !!}" />--}}
                                                {{--<span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Удалить" onclick="$(this).parent().remove();">--}}
                                                    {{--<i class="glyphicon glyphicon-trash"></i>--}}
                                                {{--</span>--}}
                                            {{--</div>--}}
                                            {{--@if($errors->has('other_phones.' . $key))--}}
                                                {{--<p class="warning" role="alert">{!! $errors->first('other_phones.' . $key,':message') !!}</p>--}}
                                            {{--@endif--}}
                                        {{--@endforeach--}}
                                    @elseif($settings->other_phones !== null)
                                        {{--@foreach(json_decode($settings->other_phones) as $phone)--}}
                                            {{--<div class="input-group">--}}
                                                {{--<input type="text" name="other_phones[]" class="form-control" value="{!! $phone !!}" />--}}
                                                {{--<span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Удалить" onclick="$(this).parent().remove();">--}}
                                                    {{--<i class="glyphicon glyphicon-trash"></i>--}}
                                                {{--</span>--}}
                                            {{--</div>--}}
                                        {{--@endforeach--}}
                                    @endif
                                    <button type="button" class="btn" id="button-add-telephone">Добавить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Почта, на которую будут приходить заказы и заявки</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group emails">
                            <div class="row">
                                <label class="col-sm-2 text-right">E-mail</label>
                                <div class="form-element col-sm-10">
                                    @if(old('notify_emails'))
                                        @foreach(old('notify_emails') as $key => $email)
                                            <div class="input-group">
                                                <input type="text" name="notify_emails[]" class="form-control" value="{!! $email !!}" />
                                                <span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Удалить" onclick="$(this).parent().remove();">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </span>
                                            </div>
                                            @if($errors->has('notify_emails.' . $key))
                                                <p class="warning" role="alert">{!! $errors->first('notify_emails.' . $key,':message') !!}</p>
                                            @endif
                                        @endforeach
                                    @elseif($settings->notify_emails !== null && is_array($settings->notify_emails))
                                        @foreach($settings->notify_emails as $email)
                                            <div class="input-group">
                                                <input type="text" name="notify_emails[]" class="form-control" value="{!! $email !!}" />
                                                <span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Удалить" onclick="$(this).parent().remove();">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </span>
                                            </div>
                                        @endforeach
                                    @endif
                                    <button type="button" class="btn" id="button-add-email">Добавить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Изображения<button type="button" class="btn" id="update_images_sizes" style="float: right;">Обновить размеры</button></h4>
                    </div>
                    <div class="panel-body">
                        @foreach($image_sizes as $size)
                            <div class="form-group">
                                <div class="row">
                                    <label class="col-sm-5 text-right">{{ $size['description'] }}</label>
                                    <div class="form-element col-sm-7">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="text-right">Ширина: {{ $size['width'] }}px</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="text-right">Высота: {{ $size['height'] }}px</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<label class="col-sm-2 text-right">Размер изображения товара в категории</label>--}}
                                {{--<div class="form-element col-sm-10">--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-sm-6">--}}
                                            {{--<label class="col-sm-2 control-label text-right">Ширина</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--@if(old('product_list_image_width') !== null)--}}
                                                    {{--<input type="text" class="form-control" name="product_list_image_width" value="{!! old('product_list_image_width') !!}" />--}}
                                                    {{--@if($errors->has('product_list_image_width'))--}}
                                                        {{--<p class="warning" role="alert">{!! $errors->first('product_list_image_width',':message') !!}</p>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{--<input type="text" class="form-control" name="product_list_image_width" value="{!! isset($settings->product_list_image_width)?$settings->product_list_image_width:'' !!}" />--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="col-sm-6">--}}
                                            {{--<label class="col-sm-2 control-label text-right">Высота</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--@if(old('product_list_image_height') !== null)--}}
                                                    {{--<input type="text" class="form-control" name="product_list_image_height" value="{!! old('product_list_image_height') !!}" />--}}
                                                    {{--@if($errors->has('product_list_image_height'))--}}
                                                        {{--<p class="warning" role="alert">{!! $errors->first('product_list_image_height',':message') !!}</p>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{--<input type="text" class="form-control" name="product_list_image_height" value="{!! isset($settings->product_list_image_height)?$settings->product_list_image_height:'' !!}" />--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<label class="col-sm-2 text-right">Размер главного изображения в карточке товара</label>--}}
                                {{--<div class="form-element col-sm-10">--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-sm-6">--}}
                                            {{--<label class="col-sm-2 control-label text-right">Ширина</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--@if(old('product_image_width') !== null)--}}
                                                    {{--<input type="text" class="form-control" name="product_image_width" value="{!! old('product_image_width') !!}" />--}}
                                                    {{--@if($errors->has('product_image_width'))--}}
                                                        {{--<p class="warning" role="alert">{!! $errors->first('product_image_width',':message') !!}</p>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{--<input type="text" class="form-control" name="product_image_width" value="{!! isset($settings->product_image_width)?$settings->product_image_width:'' !!}" />--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="col-sm-6">--}}
                                            {{--<label class="col-sm-2 control-label text-right">Высота</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--@if(old('product_image_height') !== null)--}}
                                                    {{--<input type="text" class="form-control" name="product_image_height" value="{!! old('product_image_height') !!}" />--}}
                                                    {{--@if($errors->has('product_image_height'))--}}
                                                        {{--<p class="warning" role="alert">{!! $errors->first('product_image_height',':message') !!}</p>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{--<input type="text" class="form-control" name="product_image_height" value="{!! isset($settings->product_image_height)?$settings->product_image_height:'' !!}" />--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<label class="col-sm-2 text-right">Размер изображения блога</label>--}}
                                {{--<div class="form-element col-sm-10">--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-sm-6">--}}
                                            {{--<label class="col-sm-2 control-label text-right">Ширина</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--@if(old('blog_image_width') !== null)--}}
                                                    {{--<input type="text" class="form-control" name="blog_image_width" value="{!! old('blog_image_width') !!}" />--}}
                                                    {{--@if($errors->has('blog_image_width'))--}}
                                                        {{--<p class="warning" role="alert">{!! $errors->first('blog_image_width',':message') !!}</p>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{--<input type="text" class="form-control" name="blog_image_width" value="{!! isset($settings->blog_image_width)?$settings->blog_image_width:'' !!}" />--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="col-sm-6">--}}
                                            {{--<label class="col-sm-2 control-label text-right">Высота</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--@if(old('blog_image_height') !== null)--}}
                                                    {{--<input type="text" class="form-control" name="blog_image_height" value="{!! old('blog_image_height') !!}" />--}}
                                                    {{--@if($errors->has('blog_image_height'))--}}
                                                        {{--<p class="warning" role="alert">{!! $errors->first('blog_image_height',':message') !!}</p>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{--<input type="text" class="form-control" name="blog_image_height" value="{!! isset($settings->blog_image_height)?$settings->blog_image_height:'' !!}" />--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<label class="col-sm-2 text-right">Размер изображения слайда</label>--}}
                                {{--<div class="form-element col-sm-10">--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-sm-6">--}}
                                            {{--<label class="col-sm-2 control-label text-right">Ширина</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--@if(old('slide_image_width') !== null)--}}
                                                    {{--<input type="text" class="form-control" name="blog_image_height" value="{!! old('slide_image_width') !!}" />--}}
                                                    {{--@if($errors->has('slide_image_width'))--}}
                                                        {{--<p class="warning" role="alert">{!! $errors->first('slide_image_width',':message') !!}</p>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{--<input type="text" class="form-control" name="slider_image_width" value="{!! isset($settings->slide_image_width)?$settings->slide_image_width:''  !!}" />--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        {{--<div class="col-sm-6">--}}
                                            {{--<label class="col-sm-2 control-label text-right">Высота</label>--}}
                                            {{--<div class="col-sm-10">--}}
                                                {{--@if(old('slider_image_height') !== null)--}}
                                                    {{--<input type="text" class="form-control" name="blog_image_height" value="{!! old('slider_image_height') !!}" />--}}
                                                    {{--@if($errors->has('slider_image_height'))--}}
                                                        {{--<p class="warning" role="alert">{!! $errors->first('slider_image_height',':message') !!}</p>--}}
                                                    {{--@endif--}}
                                                {{--@else--}}
                                                    {{--<input type="text" class="form-control" name="slider_image_height" value="{!! isset($settings->slide_image_height)?$settings->slide_image_height:'' !!}" />--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">--}}
                        {{--<h4>Курсы валют</h4>--}}
                    {{--</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<label class="col-sm-2 text-right">Курс гривны</label>--}}
                                {{--<div class="form-element col-sm-10">--}}
                                    {{--<input type="text" class="form-control" name="rate" value="{!! $settings->rate !!}" />--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
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


    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'text-area', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        });
        CKEDITOR.replace('text-area-terms', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        });
    </script>
@endsection
