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
                                    <div class="row">
                                        <div class="col-xs-4">
                                            @if(old('meta_title_ru') !== null)
                                                <input type="text" class="form-control" name="meta_title_ru" value="{!! old('meta_title_ru') !!}" />
                                                @if($errors->has('meta_title_ru'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_title_ru',':message') !!}</p>
                                                @endif
                                            @else
                                                <input type="text" class="form-control" name="meta_title_ru" value="{!! $settings->meta_title_ru !!}" />
                                            @endif
                                        </div>
                                        <div class="col-xs-4">
                                            @if(old('meta_title_ua') !== null)
                                                <input type="text" class="form-control" name="meta_title_ua" value="{!! old('meta_title_ua') !!}" />
                                                @if($errors->has('meta_title_ua'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_title_ua',':message') !!}</p>
                                                @endif
                                            @else
                                                <input type="text" class="form-control" name="meta_title_ua" value="{!! $settings->meta_title_ua !!}" />
                                            @endif
                                        </div>
                                        <div class="col-xs-4">
                                            @if(old('meta_title_en') !== null)
                                                <input type="text" class="form-control" name="meta_title_en" value="{!! old('meta_title_en') !!}" />
                                                @if($errors->has('meta_title_en'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_title_en',':message') !!}</p>
                                                @endif
                                            @else
                                                <input type="text" class="form-control" name="meta_title_en" value="{!! $settings->meta_title_en !!}" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Мета-тег Description</label>
                                <div class="form-element col-sm-10">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            @if(old('meta_description_ru') !== null)
                                                <textarea name="meta_description_ru" class="form-control" rows="6">{!! old('meta_description_ru') !!}</textarea>
                                                @if($errors->has('meta_description_ru'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_description_ru',':message') !!}</p>
                                                @endif
                                            @else
                                                <textarea name="meta_description_ru" class="form-control" rows="6">{!! $settings->meta_description_ru !!}</textarea>
                                            @endif
                                        </div>
                                        <div class="col-xs-4">
                                            @if(old('meta_description_ua') !== null)
                                                <textarea name="meta_description_ua" class="form-control" rows="6">{!! old('meta_description_ua') !!}</textarea>
                                                @if($errors->has('meta_description_ua'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_description',':message') !!}</p>
                                                @endif
                                            @else
                                                <textarea name="meta_description_ua" class="form-control" rows="6">{!! $settings->meta_description_ua !!}</textarea>
                                            @endif
                                        </div>
                                        <div class="col-xs-4">
                                            @if(old('meta_description_en') !== null)
                                                <textarea name="meta_description_en" class="form-control" rows="6">{!! old('meta_description_en') !!}</textarea>
                                                @if($errors->has('meta_description_en'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_description_en',':message') !!}</p>
                                                @endif
                                            @else
                                                <textarea name="meta_description_en" class="form-control" rows="6">{!! $settings->meta_description_en !!}</textarea>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 text-right">Мета-тег Keywords</label>
                                <div class="form-element col-sm-10">
                                    <div class="row">
                                        <div class="col-xs-4">
                                            @if(old('meta_keywords_ru') !== null)
                                                <textarea name="meta_keywords_ru" class="form-control" rows="6">{!! old('meta_keywords_ru') !!}</textarea>
                                                @if($errors->has('meta_description_ru'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_description_ru',':message') !!}</p>
                                                @endif
                                            @else
                                                <textarea name="meta_keywords_ru" class="form-control" rows="6">{!! $settings->meta_keywords_ru !!}</textarea>
                                            @endif
                                        </div>
                                        <div class="col-xs-4">
                                            @if(old('meta_keywords_ua') !== null)
                                                <textarea name="meta_keywords_ua" class="form-control" rows="6">{!! old('meta_keywords_ua') !!}</textarea>
                                                @if($errors->has('meta_description_ua'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_description_ua',':message') !!}</p>
                                                @endif
                                            @else
                                                <textarea name="meta_keywords_ua" class="form-control" rows="6">{!! $settings->meta_keywords_ua !!}</textarea>
                                            @endif
                                        </div>
                                        <div class="col-xs-4">
                                            @if(old('meta_keywords_en') !== null)
                                                <textarea name="meta_keywords_en" class="form-control" rows="6">{!! old('meta_keywords_en') !!}</textarea>
                                                @if($errors->has('meta_description_en'))
                                                    <p class="warning" role="alert">{!! $errors->first('meta_description_en',':message') !!}</p>
                                                @endif
                                            @else
                                                <textarea name="meta_keywords_en" class="form-control" rows="6">{!! $settings->meta_keywords_en !!}</textarea>
                                            @endif
                                        </div>
                                    </div>
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
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <textarea id="text-area-ru" name="about_ru" class="form-control" rows="6">{!! old('about_ru') ? old('about_ru') : $settings->about_ru  !!}</textarea>
                                        </div>
                                        <div class="col-xs-4">
                                            <textarea id="text-area-ua" name="about_ua" class="form-control" rows="6">{!! old('about_ua') ? old('about_ua') : $settings->about_ua  !!}</textarea>
                                        </div>
                                        <div class="col-xs-4">
                                            <textarea id="text-area-en" name="about_en" class="form-control" rows="6">{!! old('about_en') ? old('about_en') : $settings->about_en  !!}</textarea>
                                        </div>
                                    </div>
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
                                    <div class="row">
                                        <div class="col-xs-4">
                                            <textarea id="text-area-ru-terms" name="terms_ru" class="form-control" rows="6">{!! old('terms_ru') ? old('terms_ru') : $settings->terms_ru  !!}</textarea>
                                        </div>
                                        <div class="col-xs-4">
                                            <textarea id="text-area-ua-terms" name="terms_ua" class="form-control" rows="6">{!! old('terms_ua') ? old('terms_ua') : $settings->terms_ua  !!}</textarea>
                                        </div>
                                        <div class="col-xs-4">
                                            <textarea id="text-area-en-terms" name="terms_en" class="form-control" rows="6">{!! old('terms_en') ? old('terms_en') : $settings->terms_en  !!}</textarea>
                                        </div>
                                    </div>
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
                        {{--<div class="form-group phones">--}}
                            {{--<div class="row">--}}
                                {{--<label class="col-sm-2 text-right">Дополнительные</label>--}}
                                {{--<div class="form-element col-sm-10">--}}
                                    {{--@if(old('other_phones'))--}}
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
                                    {{--@elseif($settings->other_phones !== null)--}}
                                        {{--@foreach(json_decode($settings->other_phones) as $phone)--}}
                                            {{--<div class="input-group">--}}
                                                {{--<input type="text" name="other_phones[]" class="form-control" value="{!! $phone !!}" />--}}
                                                {{--<span class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Удалить" onclick="$(this).parent().remove();">--}}
                                                    {{--<i class="glyphicon glyphicon-trash"></i>--}}
                                                {{--</span>--}}
                                            {{--</div>--}}
                                        {{--@endforeach--}}
                                    {{--@endif--}}
                                    {{--<button type="button" class="btn" id="button-add-telephone">Добавить</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
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


    <script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace( 'text-area-ru', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        });
        CKEDITOR.replace('text-area-ru-terms', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        });
        CKEDITOR.replace( 'text-area-ua', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        });
        CKEDITOR.replace('text-area-ua-terms', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        });
        CKEDITOR.replace( 'text-area-en', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        });
        CKEDITOR.replace('text-area-en-terms', {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{csrf_token()}}',
        });
    </script>
@endsection
