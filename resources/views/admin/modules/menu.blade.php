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
                @foreach($parts as $part => $name)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>{{ $name }}</h4>
                        </div>
                        <div class="panel-body">
                            @if(isset($settings->{$part}))
                                @foreach($settings->{$part} as $i => $item)
                                    <div class="row" style="margin-bottom: 15px;">
                                        <div class="col-sm-3 form-element">
                                            <input class="form-control" type="text" name="menu[{{ $part }}][{{ $i }}][name]" placeholder="Название*" value="{{ $item->name }}">
                                        </div>
                                        <div class="col-sm-5 form-element">
                                            <input class="form-control" type="text" name="menu[{{ $part }}][{{ $i }}][href]" placeholder="Ссылка*" value="{{ $item->href }}">
                                        </div>
                                        <div class="col-sm-3 form-element">
                                            <input class="form-control" type="text" name="menu[{{ $part }}][{{ $i }}][class]" placeholder="Клас" value="{{ $item->class }}">
                                        </div>
                                        <div class="col-sm-1 form-element">
                                            <div class="menu-image">
                                                @if(empty($item->image))
                                                    <i class="add-image" data-open="menu">+</i>
                                                @else
                                                    <i class="remove-image">-</i>
                                                @endif
                                                <input name="menu[{{ $part }}][{{ $i }}][image]" value="{{ $item->image or '' }}" type="hidden">
                                                <img src="{{ !empty($item->image) ?  $item->image : '/uploads/no_image.jpg' }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <div class="row">
                                <div class="col-xs-12">
                                    <button type="button" class="btn button-add-menu-item" data-id="{{ $part }}">Добавить пункт меню</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
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
    <style>
        .menu-image{
            position: relative;
            cursor: pointer;
        }
        .menu-image img{
            height: 34px;
            width: 100%;
            object-fit: contain;
        }
        .menu-image .remove-image, .menu-image .add-image{
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            text-align: center;
            line-height: 34px;
        }
    </style>
    <script>
        jQuery(document).ready(function($){
            $('.button-add-menu-item').click(function () {
                var id = $(this).data('id');
                var i = $(this).parents('.panel-body').find('.row').length - 1;
                $(this).parents('.row').before('<div class="row" style="margin-bottom: 15px;">' +
                    '                            <div class="col-sm-3 form-element">' +
                    '                                <input class="form-control" type="text" name="menu['+id+']['+i+'][name]" placeholder="Название*" value="">' +
                    '                            </div>' +
                    '                            <div class="col-sm-5 form-element">' +
                    '                                <input class="form-control" type="text" name="menu['+id+']['+i+'][href]" placeholder="Ссылка*" value="">' +
                    '                            </div>' +
                    '                            <div class="col-sm-3 form-element">' +
                    '                                <input class="form-control" type="text" name="menu['+id+']['+i+'][class]" placeholder="Клас" value="">' +
                    '                            </div>' +
                    '                            <div class="col-sm-1 form-element">' +
                    '                                 <div class="menu-image">' +
                    '                                   <i class="add-image" data-open="menu">+</i>' +
                    '                                   <input name="menu['+id+']['+i+'][image]" value="" type="hidden">' +
                    '                                   <img src="/uploads/no_image.jpg">' +
                    '                                 </div>' +
                    '                            </div>'+
                    '                        </div>');
            });
        });
    </script>
@endsection

@section('before_footer')
    @include('admin.layouts.imagesloader')
@endsection