@extends('public.layouts.main')

@section('meta')
    <title>Восстановление пароля</title>
    <meta name="description" content="{!! $settings->meta_description !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords !!}">
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('forgotten') !!}
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
                                    <a href="javascript:void(0);" class="active-aside-link"><p>Вход</p></a>
                                </div>
                            </div>
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="{{env('APP_URL')}}/registration"><p>Регистрация</p></a>
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
                            <option value="{{env('APP_URL')}}/registration">Регистрация</option>
                            <option selected="selected" value="{{env('APP_URL')}}/forgotten">Забыли пароль</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-8 col-xs-12">
                    <div class="row">
                        <div class="col-md-12 margin">
                            <a href="{{env('APP_URL')}}/login/facebook" class="sing-up-btn-facebook">
                                <p>Авторизация с Facebook</p>
                            </a>
                            <div class="sign-advant">
                                <p>Авторизация дает возможность</p>
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
                    @if(!empty($errors->all()))
                        <div class="error-message">
                            <div class="error-message__text">
                                {!! $errors->first() !!}
                            </div>
                        </div>
                    @endif

                    <form class="sign-up-form sign-in-form" method="post">
                        {!! csrf_field() !!}
                        <div class="sign-up-form-item">
                            <p>Почта</p>
                            <input type="text" value="{!! old('email') !!}" name="email" id="email" class="@if($errors->has('email')) input-error @endif">
                        </div>
                        <button type="submit" class="registr-btn">Продолжить</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection