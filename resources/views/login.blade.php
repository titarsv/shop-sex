@extends('public.layouts.main')

@section('meta')
    <title>{{ trans('app.login_to_your_personal_account') }}</title>
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
                    <li>{{ trans('app.input') }}</li>
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
                        <p>{{ trans('app.enter_your_e-mail') }}</p>
                    </div>
                    <div class="registration-form__input-wrp">
                        <input class="clear-styles" type="text" name="password" id="password" placeholder="{{ trans('app.password') }}">
                        <p>{{ trans('app.enter_your_password') }}</p>
                    </div>
                    <button type="submit" class="registration-form__btn">{{ trans('app.to_come_in') }}</button>
                </form>
            </div>
        </div>
    </section>
@endsection