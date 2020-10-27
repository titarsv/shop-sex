@extends('public.layouts.main')

@section('meta')
    <title>Вход в личный кабинет</title>
    <meta name="description" content="{!! $settings->meta_description_ru !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords_ru !!}">
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('login') !!}
@endsection

@section('content')
    <section>
        <div class="container">
            <div class="row">
                <ul class="registration-tab">
                    <li>Вход</li>
                </ul>
                <form method="post" class="registration-form clear-styles">
                    {!! csrf_field() !!}
                    @if(session('process')=='registration' && !empty($errors->all()))
                        <span class="error-message">
                            {!! $errors->first() !!}
                        </span>
                    @endif
                    <div class="registration-form__input-wrp">
                        <input class="clear-styles" type="email" name="email" id="email" placeholder="E-mail">
                        <p>Введите Ваш E-mail.</p>
                    </div>
                    <div class="registration-form__input-wrp">
                        <input class="clear-styles" type="text" name="password" id="password" placeholder="Пароль">
                        <p>Введите Ваш пароль.</p>
                    </div>
                    <button type="submit" class="registration-form__btn">Войти</button>
                </form>
            </div>
        </div>
    </section>
@endsection