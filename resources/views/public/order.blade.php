@extends('public.layouts.main')
@section('meta')
    <title>{{ trans('app.checkout') }}</title>
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('checkout') !!}
@endsection

@section('content')
    <section id="order_checkout_content">
        <div class="container">
            <div class="row">
                <ul class="col-sm-12 col-xs-12 breadcrumbs">
                    <li>{{ trans('app.home') }} -</li>
                    <li>{{ trans('app.basket') }}</li>
                </ul>
                <div class="col-sm-12 col-xs-12">
                    <p class="contact__title">{{ trans('app.basket') }}</p>
                </div>
                <div class="col-sm-8 col-xs-12">
                    <p class="cart-link clear_cart">{{ trans('app.empty_trash') }}</p>
                    @foreach ($cart->get_products() as $code => $product)
                        @if(is_object($product['product']))
                            <div class="cart__item">
                                <div class="cart__item-delete" data-prod-id="{{ $code }}"><div></div></div>
                                <div class="cart__item-img">
                                    <img src="{{ is_null($product['product']->image) ? '/uploads/no_image.jpg' : $product['product']->image->url('cart') }}" alt="{{ $product['product']->name }}" alt="">
                                </div>
                                <div class="cart__item-name">
                                    <p>
                                        {{ $product['product']->name }}
                                        @if(!empty($product['variations']))
                                            (
                                            @foreach($product['variations'] as $name => $val)
                                                {{ $name }}: {{ $val }};
                                            @endforeach
                                            )
                                        @endif
                                    </p>
                                </div>
                                <div class="cart__item-quont">
                                    <input pattern="^[ 0-9]+$" type="number" min="1" name="qty" value="{{ $product['quantity'] }}" data-title="{{ trans('app.amount') }}" data-price="{{ $product['price'] }}" data-prod-id="{{ $code }}">
                                    <span>{{ trans('app.pc') }}</span>
                                </div>
                                <div class="cart__item-price">
                                    <p>{{ round($product['price'] * $product['quantity'], 2) }} {{ trans('app.hryvnias') }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog" class="cart-link back hidden-link">← {{ trans('app.Back_to_shopping') }}</a>
                </div>
                <div class="col-sm-4 col-xs-12" id="liqpay_checkout">
                    <p class="cart-form__text">{{ trans('app.checkout') }}</p>
                    <form action="{{env('APP_URL')}}/order/create" method="post" class="cart-form" id="order-checkout">
                        <p class="cart-form__text">*{{ trans('app.your_contact_phone_number') }}</p>
                        <input class="cart-form__input clear-styles" type="tel" name="phone" data-title="{{ trans('app.phone') }}" data-validate-required="{{ trans('app.obligatory_field') }}" data-validate-uaphone="Неправильный номер" placeholder="+380(___)-__-__-__" required>
                        <div class="cart-block_checkout">
                            <div class="error-message__text" style="font-size: 12px"></div>
                        </div>
                        <textarea name="comment" class="cart-form__textarea" placeholder="{{ trans('app.comment') }}"></textarea>
                        <div class="cart-form__radio">
                            <input type="radio" name="payment" id="cash" value="cash" checked>
                            <label for="cash">{{ trans('app.cod') }}</label>
                        </div>
                        <div class="cart-form__radio">
                            <input type="radio" name="payment" id="liqpay" value="card">
                            <label for="liqpay">{{ trans('app.online_payment') }} <img src="/images/liqpay.jpg" alt="liqpay"></label>
                        </div>
                        <button type="submit" class="cart-form__bnt clear-styles">{{ trans('app.checkoutt') }}</button>
                        <div class="cart-form__footnote">
                            <img src="/images/info.png" alt="info">
                            <span>{{ trans('app.the_seller_reserves_the_right_to_replace_your_order_with_a_similar_product_if_the_product_you_ordered_is_not_available_in_this_case,_the_cost_of_the_goods_does_not_change') }}</span>
                        </div>
                    </form>
                    <div class="cart-form__after">
                        <span>{{ trans('app.electronic_payment_possible') }} </span>
                        <img src="/images/visa.png" alt="visa mastercard">
                    </div>
                    <script src="//static.liqpay.ua/libjs/checkout.js" async></script>
                </div>
                <div class="visible-xs-block col-xs-12">
                    <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog" class="cart-link back">{{ trans('app.back_to_shopping') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
