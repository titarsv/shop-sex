@extends('public.layouts.main')
@section('meta')
    <title>{{ trans('app.checkout') }}</title>
@endsection
@section('content')

    @section('breadcrumbs')
        {!! Breadcrumbs::render('cart') !!}
    @endsection

    <section id="order_cart_content">
        <div class="container">
            <div class="row cart-main-content">
                <div class="visible-xs-block col-xs-12">
                    <h1 class="title">{{ trans('app.basket') }}</h1>
                </div>
                <div class="col-md-8 col-sm-12 col-xs-12">
                    <div class="cart-list-title path-underline hidden-xs">
                        <p>{{ trans('app.discount') }}</p>
                        <p>{{ trans('app.the_size') }}</p>
                        <p>{{ trans('app.number') }}</p>
                        <p>{{ trans('app.sum') }}</p>
                    </div>

                    @foreach ($cart->get_products() as $code => $product)
                        @if(is_object($product['product']))
                            <div class="cart-product-item path-underline">
                                <div class="cart-img-wrp col-xs-2">
                                    <img src="{{ is_null($product['product']->image) ? '/uploads/no_image.jpg' : $product['product']->image->url('cart') }}" alt="{{ $product['product']->name }}">
                                </div>
                                <div class="cart-prod-description hidden-xs">
                                    <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/product/{{ $product['product']->url_alias }}">
                                        <h5 class="default-link-hover">
                                            {{ $product['product']->name }}
                                            @if(!empty($product['variations']))
                                                (
                                                @foreach($product['variations'] as $name => $val)
                                                    {{ $name }}: {{ $val }};
                                                @endforeach
                                                )
                                            @endif
                                        </h5>
                                    </a>
                                    <p class="hidden-xs">{{ trans('app.product_code') }}<span>{{ $product['product']->articul }}</span> </p>
                                </div>
                                <div class="cart-list cart-list-margins hidden-xs">
                                    <ul>
                                        <li>{{ $product['sale_percent'] }}%</li>
                                    </ul>
                                    <ul>
                                        <li>{{ isset($product['variations'][trans('app.the_size')]) ? $product['variations'][trans('app.the_size')] : '' }}</li>
                                    </ul>
                                    <ul>
                                        <li class="prod-quantity">
                                            <span class="minus cart_minus">-</span>
                                            <input type="text" class="count_field" value="{{ $product['quantity'] }}" size="5" data-prod-id="{{ $code }}"/>
                                            <span class="plus cart_plus">+</span>
                                        </li>
                                    </ul>
                                    <div class="popup-price">
                                        <p><span data-one-price="{{ round($product['price'] * $product['quantity'], 2) }}">{{ number_format( round($product['price'] * $product['quantity'], 2), 0, ',', ' ' ) }}</span> {{ trans('app.hryvnias') }}</p>
                                    </div>
                                </div>

                                <div class="visible-xs-inline-block col-xs-8">
                                    <div class="cart-list-margins">
                                        <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/product/{{ $product['product']->url_alias }}">
                                            <h5 class="mobile-prod-cart-title default-link-hover">
                                                {{ $product['product']->name }}
                                                @if(!empty($product['variations']))
                                                    (
                                                    @foreach($product['variations'] as $name => $val)
                                                        {{ $name }}: {{ $val }};
                                                    @endforeach
                                                    )
                                                @endif
                                            </h5>
                                        </a>
                                    </div>
                                    <ul class="mobile-prod-cart">
                                        <li>
                                            <p>{{ trans('app.price') }}</p>
                                            <div class="popup-price">
                                                <p><span data-one-price="{{ round($product['price'] * $product['quantity'], 2) }}">{{ number_format( round($product['price'] * $product['quantity'], 2), 0, ',', ' ' ) }}</span> {{ trans('app.hryvnias') }}</p>
                                            </div>
                                        </li>
                                        <li>
                                            <p>{{ trans('app.the_size') }}</p><span>{{ isset($product['variations'][trans('app.the_size')]) ? $product['variations'][trans('app.the_size')] : '' }}</span>
                                        </li>
                                        <li>
                                            <p>{{ trans('app.colour') }}</p><span>{{ isset($product['variations'][trans('app.colour')]) ? $product['variations'][trans('app.colour')] : '' }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="close-prod-item">
                                    <a href="#" class="mc_item_delete" data-prod-id="{{ $code }}">
                                        <img src="/images/homepage-icons/delete (cart) icon.svg" alt="remove">
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <ul class="cart-links hidden-xs">
                        {{--@include('public.layouts.links')--}}
                    </ul>
                </div>

                <div class="col-md-3 col-md-offset-1 col-sm-12 col-xs-12 cart-receipt-wrp">
                    <div class="row">
                        <div class="col-md-12 no-padding">
                            <div class="cart-receipt">
                                <div class="cart-receipt-item-wrp path-underline">
                                    @foreach ($cart->get_products() as $code => $product)
                                        @if(is_object($product['product']))
                                            <div class="cart-receipt-item">
                                                <h5>
                                                    {{ $product['product']->name }}
                                                    @if(!empty($product['variations']))
                                                        (
                                                        @foreach($product['variations'] as $name => $val)
                                                            {{ $name }}: {{ $val }};
                                                        @endforeach
                                                        )
                                                    @endif
                                                </h5>
                                                <p><span>{{ number_format( round($product['price'] * $product['quantity'], 2), 0, ',', ' ' ) }}</span> {{ trans('app.hryvnias') }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(!empty($cart->total_sale))
                                        <div class="cart-receipt-item">
                                            <h5>{{ trans('app.discount') }}</h5>
                                            <p><span>{{ $cart->total_sale ? number_format( round($cart->total_sale, 2), 0, ',', ' ' ) : '0' }}</span> {{ trans('app.hryvnias') }}</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="cart-receipt-item cart-receipt-price">
                                    <h5>{{ trans('app.total') }}</h5>
                                    <p><span>{{ $cart->total_price ? number_format( round($cart->total_price, 2), 0, ',', ' ' ) : '0' }}</span> {{ trans('app.hryvnias') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 no-padding">
                            <div class="cart-receipt-btn">
                                <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/checkout">
                                    <p class="checkout-btn process">{{ trans('app.go_to_checkout') }}</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="visible-xs-block col-xs-12">
                    <ul class="cart-links">
                        @include('public.layouts.links')
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection