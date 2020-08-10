@extends('public.layouts.main')
@section('meta')
    <title>Оформление заказа</title>
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('checkout') !!}
@endsection

@section('content')
    <section id="order_checkout_content">
        <div class="container">
            <div class="row">
                <ul class="col-sm-12 col-xs-12 breadcrumbs">
                    <li>Главная -</li>
                    <li>Корзина</li>
                </ul>
                <div class="col-sm-12 col-xs-12">
                    <p class="contact__title">Корзина</p>
                </div>
                <div class="col-sm-8 col-xs-12">
                    <p class="cart-link clear_cart">Очистить корзину</p>
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
                                    <input pattern="^[ 0-9]+$" type="number" min="1" name="qty" value="{{ $product['quantity'] }}" data-title="Колличество" data-price="{{ $product['price'] }}" data-prod-id="{{ $code }}">
                                    <span>шт</span>
                                </div>
                                <div class="cart__item-price">
                                    <p>{{ round($product['price'] * $product['quantity'], 2) }} грн</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <a href="{{env('APP_URL')}}/catalog" class="cart-link back hidden-link">← Вернуться к покупкам</a>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <p class="cart-form__text">Оформление заказа</p>
                    <form action="{{env('APP_URL')}}/order/create" method="post" class="cart-form" id="order-checkout">
                        <p class="cart-form__text">Ваш контактный телефон</p>
                        <input class="cart-form__input clear-styles" type="tel" name="phone" data-title="Телефон" data-validate-required="Обязательное поле" data-validate-uaphone="Неправильный номер" placeholder="+380(___)-__-__-__">
                        <textarea name="comment" class="cart-form__textarea" placeholder="Комментарий"></textarea>
                        <div class="cart-form__radio">
                            <input type="radio" name="r1" id="r1" checked>
                            <label for="r1">Наложенный платеж</label>
                        </div>
                        <div class="cart-form__radio">
                            <input type="radio" name="r1" id="r2">
                            <label for="r2">Оплата онлайн <img src="/images/liqpay.jpg" alt="liqpay"></label>
                        </div>
                        <button type="submit" class="cart-form__bnt clear-styles">Оформить заказ</button>
                        <div class="cart-form__footnote">
                            <img src="/images/info.png" alt="info">
                            <span>Продавец оставляет за собой право замены Вашего заказа аналогичным товаром, если заказанного Вами товара не окажется в наличии! Стоимость товара при этом не меняется!</span>
                        </div>
                    </form>
                </div>
                <div class="visible-xs-block col-xs-12">
                    <a href="{{env('APP_URL')}}/catalog" class="cart-link back">вернуться к покупкам</a>
                </div>
            </div>
        </div>
    </section>

    {{--<section id="order_checkout_content">--}}
        {{--<div class="container">--}}
            {{--<div class="row cart-main-content">--}}
                {{--<div class="col-md-10 col-sm-12">--}}
                    {{--<div class="cart-list-title checkout-list-title hidden-xs path-underline">--}}
                        {{--<p>Скидка</p>--}}
                        {{--<p>Размер</p>--}}
                        {{--<p>Количество</p>--}}
                        {{--<p>Сумма</p>--}}
                    {{--</div>--}}
                    {{--@foreach ($cart->get_products() as $code => $product)--}}
                        {{--@if(is_object($product['product']))--}}
                            {{--<div class="cart-product-item path-underline">--}}
                                {{--<div class="cart-img-wrp col-xs-2">--}}
                                    {{--<img src="{{ is_null($product['product']->image) ? '/uploads/no_image.jpg' : $product['product']->image->url('cart') }}" alt="{{ $product['product']->name }}">--}}
                                {{--</div>--}}
                                {{--<div class="cart-prod-description hidden-xs">--}}
                                    {{--<a href="{{env('APP_URL')}}/product/{{ $product['product']->url_alias }}">--}}
                                        {{--<h5 class="default-link-hover">--}}
                                            {{--{{ $product['product']->name }}--}}
                                            {{--@if(!empty($product['variations']))--}}
                                                {{--(--}}
                                                {{--@foreach($product['variations'] as $name => $val)--}}
                                                    {{--{{ $name }}: {{ $val }};--}}
                                                {{--@endforeach--}}
                                                {{--)--}}
                                            {{--@endif--}}
                                        {{--</h5>--}}
                                    {{--</a>--}}
                                    {{--<p class="hidden-xs">Код товара:<span>{{ $product['product']->articul }}</span> </p>--}}
                                {{--</div>--}}
                                {{--<div class="cart-list cart-list-margins hidden-xs">--}}
                                    {{--<ul>--}}
                                        {{--<li>{{ $product['sale_percent'] }}%</li>--}}
                                    {{--</ul>--}}
                                    {{--<ul>--}}
                                        {{--<li>{{ isset($product['variations']['Размер']) ? $product['variations']['Размер'] : '' }}</li>--}}
                                    {{--</ul>--}}
                                    {{--<ul>--}}
                                        {{--<li class="prod-quantity">--}}
                                            {{--<span class="minus cart_minus">-</span>--}}
                                            {{--<input type="text" class="count_field" value="{{ $product['quantity'] }}" size="5" data-prod-id="{{ $code }}"/>--}}
                                            {{--<span class="plus cart_plus">+</span>--}}
                                        {{--</li>--}}
                                    {{--</ul>--}}
                                    {{--<div class="popup-price">--}}
                                        {{--<p><span data-one-price="{{ round($product['price'] * $product['quantity'], 2) }}">{{ number_format( round($product['price'] * $product['quantity'], 2), 0, ',', ' ' ) }}</span> грн</p>--}}
                                    {{--</div>--}}
                                {{--</div>--}}

                                {{--<div class="visible-xs-inline-block col-xs-8">--}}
                                    {{--<div class="cart-list-margins">--}}
                                        {{--<a href="{{env('APP_URL')}}/product/{{ $product['product']->url_alias }}">--}}
                                            {{--<h5 class="mobile-prod-cart-title default-link-hover">--}}
                                                {{--{{ $product['product']->name }}--}}
                                                {{--@if(!empty($product['variations']))--}}
                                                    {{--(--}}
                                                    {{--@foreach($product['variations'] as $name => $val)--}}
                                                        {{--{{ $name }}: {{ $val }};--}}
                                                    {{--@endforeach--}}
                                                    {{--)--}}
                                                {{--@endif--}}
                                            {{--</h5>--}}
                                        {{--</a>--}}
                                    {{--</div>--}}
                                    {{--<ul class="mobile-prod-cart">--}}
                                        {{--<li>--}}
                                            {{--<p>Цена</p>--}}
                                            {{--<div class="popup-price">--}}
                                                {{--<p><span data-one-price="{{ round($product['price'] * $product['quantity'], 2) }}">{{ number_format( round($product['price'] * $product['quantity'], 2), 0, ',', ' ' ) }}</span> грн</p>--}}
                                            {{--</div>--}}
                                        {{--</li>--}}
                                        {{--<li>--}}
                                            {{--<p>Размер</p><span>{{ isset($product['variations']['Размер']) ? $product['variations']['Размер'] : '' }}</span>--}}
                                        {{--</li>--}}
                                        {{--<li>--}}
                                            {{--<p>Цвет</p><span>{{ isset($product['variations']['Цвет']) ? $product['variations']['Цвет'] : '' }}</span>--}}
                                        {{--</li>--}}
                                    {{--</ul>--}}
                                {{--</div>--}}
                                {{--<div class="close-prod-item col-xs-2">--}}
                                    {{--<a href="#" class="mc_item_delete" data-prod-id="{{ $code }}">--}}
                                        {{--<img src="/images/homepage-icons/delete (cart) icon.svg" alt="remove">--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--@endif--}}
                    {{--@endforeach--}}
                    {{--<form action="{{env('APP_URL')}}/order/create" method="post" class="order-form" id="order-checkout">--}}
                        {{--{{ csrf_field() }}--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-sm-12 col-xs-12">--}}
                                {{--<h5 class="checkout-title">Оформление заказа</h5>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-7 col-xs-12">--}}
                                {{--<div class="form-conditions">--}}
                                    {{--<h5>Доставка</h5>--}}
                                    {{--<span id="current-delivery">Доставка на склад новой почты</span>--}}
                                    {{--<a href="" class="form-conditions-btn popup-btn" data-mfp-src="#delivery-popup"><p>Изменить</p></a>--}}
                                {{--</div>--}}
                                {{--<div class="form-conditions">--}}
                                    {{--<h5>Оплата</h5>--}}
                                    {{--<span id="current-pay">На расчетный счет Приват Банка</span>--}}
                                    {{--<a href="" class="form-conditions-btn popup-btn" data-mfp-src="#pay-popup"><p>Изменить</p></a>--}}
                                {{--</div>--}}
                                {{--<div class="form-comment">--}}
                                    {{--<h5>Комментарий</h5>--}}
                                    {{--<textarea name="" id="" cols="30" rows="10" class="leave-comment"></textarea>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="col-sm-5 col-xs-12 no-padding cart-receipt-wrp">--}}
                                {{--<div class="cart-receipt">--}}
                                    {{--<div class="cart-receipt-item-wrp path-underline">--}}
                                        {{--<div class="cart-receipt-item">--}}
                                            {{--<h5>Общая сумма заказа</h5>--}}
                                            {{--<p><span>{{ $cart->total_price ? number_format( round($cart->total_price, 2), 0, ',', ' ' ) : '0' }}</span> грн</p>--}}
                                        {{--</div>--}}
                                        {{--@if(!empty($cart->total_sale))--}}
                                            {{--<div class="cart-receipt-item">--}}
                                                {{--<h5>Скидка</h5>--}}
                                                {{--<p><span>{{ $cart->total_sale ? number_format( round($cart->total_sale, 2), 0, ',', ' ' ) : '0' }}</span> грн</p>--}}
                                            {{--</div>--}}
                                        {{--@endif--}}
                                        {{--<div class="cart-receipt-item">--}}
                                            {{--<h5>Сумма доставки</h5>--}}
                                            {{--<p><span>0</span> грн</p>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="cart-receipt-item cart-receipt-price">--}}
                                        {{--<h5>Итого</h5>--}}
                                        {{--<p><span>{{ $cart->total_price ? number_format( round($cart->total_price, 2), 0, ',', ' ' ) : '0' }}</span> грн</p>--}}
                                    {{--</div>--}}
                                    {{--<div class="agreement-container">--}}
                                        {{--<input type="checkbox" name="agreement" value="" id="safe-agreement" class="checkbox">--}}
                                        {{--<span class="checkbox-custom"></span>--}}
                                        {{--<label for="safe-agreement">Соглашаюсь с условиями безопасности</label>--}}
                                    {{--</div>--}}
                                    {{--<div class="agreement-container">--}}
                                        {{--<input type="checkbox" name="agreement" value="" id="public-agreement" class="checkbox">--}}
                                        {{--<span class="checkbox-custom"></span>--}}
                                        {{--<label for="public-agreement">Соглашаюсь с условиями Договора публичной оферты и возврата</label>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<div class="cart-receipt-btn">--}}
                                    {{--@if($user_logged)--}}
                                        {{--<button type="submit" class="checkout-btn" id="checkout-btn">Оформить заказ</button>--}}
                                    {{--@else--}}
                                        {{--<a href="{{env('APP_URL')}}/login" class="checkout-btn">Оформить заказ</a>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--POP-UP DELIVERY--}}
                        {{--<div class="mfp-hide">--}}
                            {{--<div id="delivery-popup">--}}
                                {{--<div class="container">--}}
                                    {{--<div class="row popup-centered">--}}
                                        {{--<div class="col-md-8 col-xs-12">--}}
                                            {{--<div class="row container-popup delivery-popup">--}}
                                                {{--<div class="col-md-12">--}}
                                                    {{--<h5 class="popup-title">Выберите способ доставки</h5>--}}
                                                {{--</div>--}}
                                                {{--<div class="col-md-12">--}}
                                                    {{--<div class="delivery-popup-item">--}}
                                                        {{--<input type="radio" name="delivery" value="newpost" id="delivery2" class="radio" checked>--}}
                                                        {{--<span class="radio-custom"></span>--}}
                                                        {{--<label for="delivery2">Доставка на склад новой почты</label>--}}
                                                        {{--<p class="delivery-popup-text"> - указана усредненная стоимость доставки, сумма может меняться в зависимости от веса посылки. Доставка осуществляется за счет покупателя.</p>--}}
                                                        {{--<p class="delivery-popup-price">(за доставку +50,00 грн.)</p>--}}
                                                    {{--</div>--}}

                                                    {{--<div class="delivery-popup-item">--}}
                                                        {{--<input type="radio" name="delivery" value="courier" id="delivery3" class="radio">--}}
                                                        {{--<span class="radio-custom"></span>--}}
                                                        {{--<label for="delivery3">Доставка курьером по Харькову</label>--}}
                                                        {{--<p class="delivery-popup-text"> - доставка посылки на указанный Вами, адрес. Данный вид доставки осуществляется только в г. Харькове.</p>--}}
                                                        {{--<p class="delivery-popup-price">(за доставку +40,00 грн.)</p>--}}
                                                    {{--</div>--}}

                                                    {{--<div class="delivery-popup-item">--}}
                                                        {{--<input type="radio" name="delivery" value="samovivoz_hol_gor" id="delivery4" class="radio">--}}
                                                        {{--<span class="radio-custom"></span>--}}
                                                        {{--<label for="delivery4">Самовывоз Холодная Гора</label>--}}
                                                        {{--<p class="delivery-popup-text"> Данная услуга действует только в г. Харькове. По адресу улица Полтавский шлях, 147 ст. метро Холодная гора (Терминал) магазин "TYFLI.COM" (с 9:00 до 21:00)</p>--}}
                                                    {{--</div>--}}
                                                    {{--<div class="delivery-popup-item">--}}
                                                        {{--<input type="radio" name="delivery" value="samovivoz_nauki" id="delivery5" class="radio">--}}
                                                        {{--<span class="radio-custom"></span>--}}
                                                        {{--<label for="delivery5">Самовывоз проспект Науки</label>--}}
                                                        {{--<p class="delivery-popup-text"> Данная услуга действует только в г. Харькове. По адресу проспект Науки, 41/43 ст. метро 23 августа магазин "TYFLI.COM" (с 10:00 до 20:00)</p>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<div class="col-md-12">--}}
                                                    {{--<button type="button" class="delivery-popup-btn save">Сохранить</button>--}}
                                                    {{--<button type="button" class="delivery-popup-btn cancel">Отменить</button>--}}
                                                {{--</div>--}}
                                                {{--<button title="Close (Esc)" type="button" class="mfp-close"><!-- × --></button>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--POP-UP PAY--}}
                        {{--<div class="mfp-hide">--}}
                            {{--<div id="pay-popup">--}}
                                {{--<div class="container">--}}
                                    {{--<div class="row popup-centered">--}}
                                        {{--<div class="col-md-8 col-xs-12">--}}
                                            {{--<div class="row container-popup delivery-popup">--}}
                                                {{--<div class="col-md-12">--}}
                                                    {{--<h5 class="popup-title">Выберите способ оплаты</h5>--}}
                                                {{--</div>--}}
                                                {{--<div class="col-md-12">--}}
                                                    {{--<div class="delivery-popup-item">--}}
                                                        {{--<input type="radio" name="payment" value="privat" id="pay1" class="radio" checked>--}}
                                                        {{--<span class="radio-custom"></span>--}}
                                                        {{--<label for="pay1">На расчетный счет Приват Банка</label>--}}
                                                        {{--<p class="delivery-popup-text"> Платеж осуществляется на расчетный счет Приват Банка.</p>--}}
                                                    {{--</div>--}}

                                                    {{--<div class="delivery-popup-item">--}}
                                                        {{--<input type="radio" name="payment" value="nal_delivery" id="pay2" class="radio">--}}
                                                        {{--<span class="radio-custom"></span>--}}
                                                        {{--<label for="pay2">Наличными курьеру</label>--}}
                                                        {{--<p class="delivery-popup-text"> Данная услуга действует только в г. Харькове.</p>--}}
                                                    {{--</div>--}}

                                                    {{--<div class="delivery-popup-item">--}}
                                                        {{--<input type="radio" name="payment" value="nal_samovivoz" id="pay3" class="radio">--}}
                                                        {{--<span class="radio-custom"></span>--}}
                                                        {{--<label for="pay3">Оплата при самовывозе</label>--}}
                                                        {{--<p class="delivery-popup-text"> Данная услуга действует только в г. Харькове.</p>--}}
                                                    {{--</div>--}}

                                                    {{--<div class="delivery-popup-item">--}}
                                                        {{--<input type="radio" name="payment" value="nalogenniy" id="pay4" class="radio">--}}
                                                        {{--<span class="radio-custom"></span>--}}
                                                        {{--<label for="pay4">Оплата наложенным платежом</label>--}}
                                                        {{--<p class="delivery-popup-text"> — денежная сумма, которую почта взыскивает по поручению отправителя с адресата при вручении почтового отправления.</p>--}}
                                                        {{--<p class="delivery-popup-price">(наценка +30,51 грн.)</p>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<div class="col-md-12">--}}
                                                    {{--<button type="button" class="delivery-popup-btn save">Сохранить</button>--}}
                                                    {{--<button type="button" class="delivery-popup-btn cancel">Отменить</button>--}}
                                                {{--</div>--}}
                                                {{--<button title="Close (Esc)" type="button" class="mfp-close"><!-- × --></button>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
@endsection