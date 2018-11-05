@extends('public.layouts.main')

@section('breadcrumbs')
    {!! Breadcrumbs::render('history') !!}
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
                                    <a href="javascript:void(0);" class="active-aside-link"><p>История покупок</p></a>
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
                                    <a href="{{env('APP_URL')}}/user"><p>Личный кабинет</p></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-8 col-xs-12 profile-grid-container">
                    <div class="row">
                        <div class="col-md-12 hidden-xs margin">
                            <h1 class="title">История покупок</h1>
                        </div>
                        <div class="visible-xs-block col-xs-12">
                            <div>
                                <select name="site-section-select" id="redirect_select" class="chosen-select site-section-select">
                                    <option selected="selected" value="">История покупок</option>
                                    <option value="{{env('APP_URL')}}/user/wishlist">Список желаний</option>
                                    <option value="{{env('APP_URL')}}/user">Личный кабинет</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12 history-page">
                            <div class="history-list-title path-underline hidden-xs">
                                <p>Скидка</p>
                                <p>Размер</p>
                                <p>Количество</p>
                                <p>Сумма</p>
                            </div>

                            @foreach($orders as $order)
                                @foreach($order->getProducts() as $product)
                                    @if(!is_null($product['product']))
                                        <div class="cart-product-item path-underline">
                                            <div class="cart-img-wrp col-xs-2">
                                                <img src="{{ $product['product']->image->url('product_list') }}" alt="{{ $product['product']->name }}">
                                            </div>
                                            <div class="cart-prod-description hidden-xs">
                                                <a href="{{env('APP_URL')}}/product/{{ $product['product']->url_alias }}"><h5 class="default-link-hover">{{ $product['product']->name }}</h5></a>
                                                <p class="hidden-xs">Код товара:<span>{{ $product['product']->articul }}</span> </p>
                                            </div>
                                            <div class="cart-list cart-list-margins hidden-xs">
                                                <ul>
                                                    <li>{{ $product['sale_percent'] or 0 }}%</li>
                                                </ul>
                                                <ul>
                                                    <li>{{ isset($product['variations']['Размер']) ? $product['variations']['Размер'] : '' }}</li>
                                                </ul>
                                                <ul>
                                                    <li class="prod-quantity">{{ $product['quantity'] }}</li>
                                                </ul>
                                                <div class="popup-price">
                                                    <p><span>{{ $product['price'] }}</span> грн</p>
                                                </div>
                                            </div>

                                            <div class="visible-xs-inline-block col-xs-8">
                                                <div class="cart-list-margins">
                                                    <a href="{{env('APP_URL')}}/product/{{ $product['product']->url_alias }}"><h5 class="mobile-prod-cart-title default-link-hover">{{ $product['product']->name }}</h5></a>
                                                </div>
                                                <ul class="mobile-prod-cart">
                                                    <li>
                                                        <p>Цена</p>
                                                        <div class="popup-price">
                                                            <p><span>{{ $product['price'] }}</span> грн</p>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <p>Размер</p><span>{{ isset($product['variations']['Размер']) ? $product['variations']['Размер'] : '' }}</span>
                                                    </li>
                                                    <li>
                                                        <p>Цвет</p><span>{{ isset($product['variations']['Цвет']) ? $product['variations']['Цвет'] : '' }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @else
                                        <div class="cart-product-item path-underline">
                                            <p>Товар более недоступен...</p>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-12 hidden-sm hidden-xs no-padding">
                        <ul class="cart-links">
                            @include('public.layouts.links')
                        </ul>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12 col-xs-12 cart-receipt-wrp">
                    <div class="row">
                        <div class="col-md-12 no-padding">
                            <div class="cart-receipt">
                                <div class="history-info-box path-underline">
                                    <h5>
                                        <p>Скидка на сайте</p>
                                        <p>{{ $user->sale() }} <span>%</span></p>
                                    </h5>
                                    <span>Общая сумма Ваших покупок {{ $user->ordersTotal() }} грн.<br/>
                                        @if(!empty($user->nextSale()))
                                            @php
                                                $next_sale = $user->nextSale();
                                            @endphp
                                            При покупке на общую сумму свыше {{ $next_sale[0] }} грн сумма скидки станет {{ $next_sale[1] }}%<br/>
                                        @endif
                                        Узнать больше о <a href="{{env('APP_URL')}}/page/bonusnyya-programma" class="default-link-hover">Бонусной программе</a></span>
                                </div>
                                <div class="cart-receipt-item cart-receipt-price history-info-box">
                                    <h5>Общая сумма покупок</h5>
                                    <p><span>{{ $user->ordersTotal() }}</span> грн</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 no-padding">
                            <div class="cart-receipt-btn">
                                @if(empty($orders))
                                    <a href="{{env('APP_URL')}}/" class="process" style="color: #F5F5F5; font-family: 'HelveticaNeue'; font-size: 18px; font-weight: bold; line-height: 25px; text-align: center; border: none; outline: none;">
                                        <p>Продолжить покупки</p>
                                    </a>
                                @else
                                    @php
                                        $href = '/';
                                        $products = $orders->first()->getProducts();
                                        if(!empty($products)){
                                            $categories = $products[0]['product']->categories;
                                            if(!empty($categories)){
                                                $href = '/catalog/'.$categories->first()->get_root_category()->url_alias;
                                            }
                                        }
                                    @endphp
                                    <a href="{{env('APP_URL')}}{{ $href }}" class="process" style="color: #F5F5F5; font-family: 'HelveticaNeue'; font-size: 18px; font-weight: bold; line-height: 25px; text-align: center; border: none; outline: none;">
                                        <p>Продолжить покупки</p>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="visible-sm-inline-block visible-xs-inline-block col-sm-12 col-xs-12">
                    <ul class="cart-links">
                        @include('public.layouts.links')
                    </ul>
                </div>

            </div>
        </div>
    </main>
@endsection