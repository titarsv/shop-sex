<div class="container">
    <div class="row popup-centered">
        <div class="col-md-8 col-xs-12">
            <div class="row container-popup">
                @if(!is_null($cart))
                <div class="col-md-12">
                    <h5 class="popup-title">Товар добавлен в корзину</h5>
                </div>
                <div class="col-md-12 no-padding">
                    @foreach ($cart->get_products() as $code => $product)
                        @if(is_object($product['product']))
                            <div class="cart-product-item path-underline">
                                <div class="cart-img-wrp col-xs-2">
                                    <img src="{{ is_null($product['product']->image) ? '/uploads/no_image.jpg' : $product['product']->image->url('cart') }}" alt="{{ $product['product']->name }}">
                                </div>
                                <div class="cart-prod-description hidden-xs">
                                    <a href="{{env('APP_URL')}}/product/{{ $product['product']->url_alias }}">
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
                                    <p class="hidden-xs">Код товара:<span>{{ $product['product']->articul }}</span> </p>
                                </div>
                                <div class="cart-list cart-list-margins hidden-xs">
                                    <ul>
                                        <li>{{ $product['sale_percent'] }}%</li>
                                    </ul>
                                    <ul>
                                        <li>{{ isset($product['variations']['Размер']) ? $product['variations']['Размер'] : '' }}</li>
                                    </ul>
                                    <ul>
                                        <li class="prod-quantity">
                                            <span class="minus cart_minus">-</span>
                                            <input type="text" class="count_field" value="{{ $product['quantity'] }}" size="5" data-prod-id="{{ $code }}"/>
                                            <span class="plus cart_plus">+</span>
                                        </li>
                                    </ul>
                                    <div class="popup-price">
                                        <p><span data-one-price="{{ round($product['price'] * $product['quantity'], 2) }}">{{ number_format( round($product['price'] * $product['quantity'], 2), 0, ',', ' ' ) }}</span> грн</p>
                                    </div>
                                </div>

                                <div class="visible-xs-inline-block col-xs-8">
                                    <div class="cart-list-margins">
                                        <a href="{{env('APP_URL')}}/product/{{ $product['product']->url_alias }}">
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
                                            <p>Цена</p>
                                            <div class="popup-price">
                                                <p><span data-one-price="{{ round($product['price'] * $product['quantity'], 2) }}">{{ number_format( round($product['price'] * $product['quantity'], 2), 0, ',', ' ' ) }}</span> грн</p>
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
                        @endif
                    @endforeach
                </div>
                <div class="col-md-12">
                    <div class="popup-total-price popup-price">
                        <p>Сумма заказа <span>{{ $cart->total_price ? number_format( round($cart->total_price, 2), 0, ',', ' ' ) : '0' }}</span> грн</p>
                        <div class="order-popup__count-items hidden" data-qty="{{ $cart->total_quantity }} {{ Lang::choice('товар|товара|товаров', $cart->total_quantity, [], 'ru') }} на">{{ $cart->total_quantity }}</div>
                    </div>
                </div>
                <div class="col-sm-6 no-padding">
                    <a href="{{env('APP_URL')}}/cart" class="popup-btn process">
                        <p>Перейти в корзину</p>
                    </a>
                </div>
                <div class="col-sm-6 no-padding">
                    <a href="{{env('APP_URL')}}/catalog/tovary" class="popup-btn continue">
                        <p>Продолжить покупки</p>
                    </a>
                </div>
                @else
                    Здесь пусто...
                @endif
                <button title="Close (Esc)" type="button" class="mfp-close"></button>
            </div>
        </div>
    </div>
</div>