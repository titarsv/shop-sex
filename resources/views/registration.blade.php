@extends('public.layouts.main')

@section('meta')
    <title>Вход в личный кабинет</title>
    <meta name="description" content="{!! $settings->meta_description !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords !!}">
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('login') !!}
@endsection

@section('content')
    <section>
        <div class="container">
            <div class="row margin">
                <div class="col-md-3 col-sm-4 hidden-xs">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="{{env('APP_URL')}}/login"><p>Вход</p></a>
                                </div>
                            </div>
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="javascript:void(0);" class="active-aside-link"><p>Регистрация</p></a>
                                </div>
                            </div>
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="{{env('APP_URL')}}/forgotten"><p>Забыли пароль</p></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="visible-xs-block col-xs-12">
                    <div>
                        <select name="site-section-select" id="redirect_select" class="site-section-select">
                            <option value="{{env('APP_URL')}}/login">Вход</option>
                            <option selected="selected" value="{{env('APP_URL')}}/registration">Регистрация</option>
                            <option value="{{env('APP_URL')}}/forgotten">Забыли пароль</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-8 col-xs-12">
                    <div class="row">
                        <div class="col-md-12 margin">
                            <a href="{{env('APP_URL')}}/login/facebook" class="sing-up-btn-facebook">
                                <p>Регистрация с Facebook</p>
                            </a>
                            <div class="sign-advant">
                                <p>Регистрация дает возможность</p>
                                <ul>
                                    <li>Делать покупки быстро и с любого устройтсва</li>
                                    <li>Применить Бонусную программ</li>
                                    <li>Следить за акциями и новинками</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-1 col-sm-12 col-xs-12">
                    <p>или</p>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    @if(session('process')=='registration' && !empty($errors->all()))
                        <span class="error-message">
                            {!! $errors->first() !!}
                        </span>
                    @endif
                    <form action="" class="sign-up-form" method="post">
                        {!! csrf_field() !!}
                        <div class="sign-up-form-item">
                            <p>Почта *</p>
                            <input type="text"
                                   name="email"
                                   id="email"
                                   class="form_input @if($errors->has('email')) input_error @endif"
                                   value="{!! old('email') !!}" placeholder="E-mail">
                        </div>
                        <div class="sign-up-form-item">
                            <p>Имя *</p>
                            <input type="text"
                                   name="first_name"
                                   id="name"
                                   class="form_input @if($errors->has('first_name')) input_error @endif"
                                   value="{!! old('first_name') !!}" placeholder="Ваше имя">
                        </div>
                        <div class="sign-up-form-item">
                            <p>Фамилия</p>
                            <input type="text"
                                   name="last_name"
                                   id="surname"
                                   class="form_input"
                                   value="{!! old('last_name') !!}"placeholder="Ваша фамилия">
                        </div>
                        <div class="sign-up-form-item">
                            <p>Телефон *</p>
                            <input type="text"
                                   name="phone"
                                   id="phone"
                                   class="form_input @if($errors->has('phone')) input_error @endif"
                                   value="{!! old('phone') !!}" placeholder="Телефон">
                        </div>
                        <div class="sign-up-form-item">
                            <p>Пароль *</p>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form_input @if($errors->has('password')) input_error @endif" placeholder="Придумайте пароль">
                        </div>
                        <div class="sign-up-form-item">
                            <p>Повторите пароль *</p>
                            <input type="password"
                                   name="password_confirmation"
                                   id="passwordr" class="form_input @if($errors->has('password_confirmation')) input_error @endif" placeholder="Подтвердите пароль">
                        </div>
                        <div class="sign-up-form-item">
                            <input type="checkbox" name="subscribe" value="1" id="sign-up-get-info" class="checkbox">
                            <span class="checkbox-custom"></span>
                            <label for="sign-up-get-info">Я хочу получать информацию об акциях и новинках по почте (смс)</label>
                        </div>
                        <button type="submit" class="registr-btn">Регистрация</button>
                    </form>
                    <p class="margin">Я уже зарегистрирован <a href="{{env('APP_URL')}}/login">Вход</a></p>
                </div>
            </div>
        </div>
    </section>
@endsection