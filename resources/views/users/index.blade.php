@extends('public.layouts.main')

@section('meta')
    <title>Личные данные</title>
    <meta name="description" content="{!! $settings->meta_description !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords !!}">
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('user') !!}
@endsection

@section('content')

    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-4 hidden-xs aside-filter-menu-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="{{env('APP_URL')}}/user/history"><p>История покупок</p></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="{{env('APP_URL')}}/user/wishlist"><p>Список желаний</p></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="javascript:void(0);" class="active-aside-link"><p>Личный кабинет</p></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9 col-sm-8 col-xs-12 profile-grid-container">
                    <div class="row">
                        <div class="visible-xs-block col-xs-12">
                            <div>
                                <select name="site-section-select" id="redirect_select" class="chosen-select site-section-select">
                                    <option value="{{env('APP_URL')}}/user/history">История покупок</option>
                                    <option value="{{env('APP_URL')}}/user/wishlist">Список желаний</option>
                                    <option selected="selected" value="">Личный кабинет</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 margin">
                            <h5 class="title">Мои данные
                                <a href="" class="edit-profile">
                                    <img src="/images/homepage-icons/edit icon.svg" alt="">
                                </a>
                            </h5>
                            <div class="profile-data-wrp">
                                <div class="profile-data-item">
                                    <h5 class="data-name">Имя Фамилия</h5>
                                    <span class="user-name">{{ $user->first_name }} {{ $user->last_name }}</span>
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">{{ trans('app.phone') }}</h5>
                                    <span>{{ $user->user_data->phone }}</span>
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">{{ trans('app.mail') }}</h5>
                                    <span>{{ $user->email }}</span>
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">Дата Рождения</h5>
                                    <input type="text" value="{{ $user->user_data->user_birth }}" class="birthday-input" disabled placeholder="__/__/____">
                                    <p>Мы дарим подарки к Вашему празднику</p>
                                </div>
                            </div>

                            <form  class="profile-edit-data-wrp unactive">
                                <div class="profile-data-item">
                                    <h5 class="data-name">Имя Фамилия</h5>
                                    <input type="text" name="fio" value="{{ $user->first_name }} {{ $user->last_name }}" class="profile-edit-data-input">
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">{{ trans('app.phone') }}</h5>
                                    <input type="text" name="phone" value="{{ $user->user_data->phone }}" id="phone" class="profile-edit-data-input">
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">{{ trans('app.mail') }}</h5>
                                    <input type="text" name="email" value="{{ $user->email }}" class="profile-edit-data-input">
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">Дата Рождения</h5>
                                    <input type="text" name="user-birth" class="birthday-input" placeholder="__/__/____">
                                    <p>Мы дарим подарки к Вашему празднику</p>
                                </div>
                            </form>

                            <form>
                                <div class="profile-data-item">
                                    <h5 class="data-name">{{ trans('app.password') }}</h5>
                                    <a href="" class="user-password">
                                        <p>Сменить пароль</p>
                                    </a>
                                    <div class="password-edit unactive">
                                        <input type="password" name="pass" class="profile-edit-data-input" placeholder="Введите пароль">
                                        <input type="password" name="repass" class="profile-edit-data-input" placeholder="Повторите пароль">
                                        <input type="button" value="Изменить" class="password-btn">
                                    </div>
                                </div>
                            </form>

                            <h5 class="title">Управление моими подписками</h5>
                            <div class="profile-subscr-wrp profile-margin">
                                <div class="profile-subscr-item">
                                    <input type="radio" name="subscr-type" value="1" id="subscr-email" class="radio"{{ $user->user_data->subscribe == 1 ? ' checked' : '' }}>
                                    <span class="radio-custom"></span>
                                    <label for="subscr-email">по email</label>
                                </div>
                                <div class="profile-subscr-item">
                                    <input type="radio" name="subscr-type" value="2" id="subscr-sms" class="radio"{{ $user->user_data->subscribe == 2 ? ' checked' : '' }}>
                                    <span class="radio-custom"></span>
                                    <label for="subscr-sms">по sms</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 margin">
                            <h5 class="title">Дисконтная программа</h5>
                            <div class="profile-discount-wrp profile-margin">
                                @if(key($user->sales) > $user->ordersTotal())
                                    <p>У Вас нет скидки</p>
                                    <span>Общая сумма Ваших покупок меньше {{ key($user->sales) }} {{ trans('app.hryvnias') }}.<br/>
                                    При покупке на общую сумму свыше {{ key($user->sales) }} грн сумма скидки станет {{ $user->sales[key($user->sales)] }}%<br/>
                                    Узнать больше о <a href="/page/bonusnyya-programma" class="default-link-hover">Бонусной программе</a></span>
                                @else
                                    <p>Ваша скидка составляет {{ $user->sale() }}%</p>
                                    <span>Общая сумма Ваших покупок {{ $user->ordersTotal() }} {{ trans('app.hryvnias') }}.<br/>
                                    @if(!empty($user->nextSale()))
                                        @php
                                            $next_sale = $user->nextSale();
                                        @endphp
                                        При покупке на общую сумму свыше {{ $next_sale[0] }} грн сумма скидки станет {{ $next_sale[1] }}%<br/>
                                    @endif
                                    Узнать больше о <a href="{{env('APP_URL')}}/page/bonusnyya-programma" class="default-link-hover">Бонусной программе</a></span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 margin">
                            <h5 class="title">Адрес доставки</h5>
                            @php
                                $address = $user->user_data->address();
                            @endphp
                            <form class="profile-address-wrp">
                                <div class="profile-data-item">
                                    <h5 class="data-name">Город</h5>
                                    <input type="text" name="city" value="{{ isset($address->city) ? $address->city : '' }}" class="profile-data-input">
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">Индекс</h5>
                                    <input type="text" name="post_code" value="{{ isset($address->post_code) ? $address->post_code : '' }}" class="profile-data-input">
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">Улица</h5>
                                    <input type="text" name="street" value="{{ isset($address->street) ? $address->street : '' }}" class="profile-data-input">
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">Дом</h5>
                                    <input type="text" name="house" value="{{ isset($address->house) ? $address->house : '' }}" class="profile-data-input">
                                </div>
                                <div class="profile-data-item">
                                    <h5 class="data-name">Квартира</h5>
                                    <input type="text" name="flat" value="{{ isset($address->flat) ? $address->flat : '' }}" class="profile-data-input">
                                </div>
                                <div class="profile-data-item">
                                    <button type="button" class="profile-address-btn">Изменить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection