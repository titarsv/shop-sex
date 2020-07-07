@extends('public.layouts.main', ['root_category' => $root_category])
@section('meta')
    <title>Купить {{ $product->name }} | Секс-шоп в Харькове, интернет-магазин | Shop-sex.com.ua</title>
    <meta name="description" content="{{ $product->name }} по лучшим ценам в Харькове, Украине ✓Гарантия анонимности">
    <meta name="keywords" content="{!! $product->meta_keywords !!}">
    @if(!empty($product->robots))
        <meta name="robots" content="{!! $product->robots !!}">
    @endif
@endsection
@section('page_vars')
    @include('public.layouts.microdata.product', ['product' => $product])
    @include('public.layouts.microdata.open_graph', [
     'title' => $product->name,
     'description' => $product->description,
     'image' => !empty($product->imag) ? $product->image->url() : '/images/no_image.jpg',
     'type' => 'product'
     ])
@endsection

@section('content')

    <section>
        <div class="container">
            <div class="row">
                {!! Breadcrumbs::render('product', $product, $product->categories) !!}
                <div class="col-md-4 col-sm-5 col-xs-12">
                    <div class="slick-slider product-slider" data-slick='{"slidesToShow": 1, "arrows":false, "dots": false, "asNavFor": ".product-slider-nav"}'>
                        @forelse($gallery as $image)
                            @if(is_object($image))
                                <div>
                                    <div class="product-slider__item">
                                        {{--<img src="{{ $image->url('product') }}" alt="{{ $product->name }}">--}}
                                        {!! $image->webp_image('product', ['alt' => $product->name], 'slider') !!}
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div>
                                <div class="product-slider__item">
                                    @if(empty($product->image))
                                        {{--<img src="/uploads/no_image.jpg" alt="{{ $product->name }}">--}}
                                        <picture>
                                            <source data-lazy="/uploads/no_image.webp" srcset="/images/pixel.webp" type="image/webp">
                                            <source data-lazy="/uploads/no_image.jpg" srcset="/images/pixel.jpg" type="image/jpeg">
                                            <img src="/images/pixel.jpg" alt="{{ $product->name }}">
                                        </picture>
                                    @else
                                        {{--<img src="{{ $product->image->url('product') }}" alt="{{ $product->name }}">--}}
                                        {!! $product->image->webp_image('product', ['alt' => $product->name], 'slider') !!}
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @if(count($gallery) > 1)
                    <div class="slick-slider product-slider-nav" data-slick='{"slidesToShow": 4, "focusOnSelect": true, "arrows":false, "dots": false, "asNavFor": ".product-slider"}'>
                        @forelse($gallery as $image)
                            @if(is_object($image))
                                <div>
                                    <div class="product-slider__item nav">
                                        {{--<img src="{{ $image->url('product') }}" alt="{{ $product->name }}">--}}
                                        {!! $image->webp_image('product', ['alt' => $product->name], 'slider') !!}
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div>
                                <div class="product-slider__item nav">
                                    @if(empty($product->image))
                                        {{--<img src="/uploads/no_image.jpg" alt="{{ $product->name }}">--}}
                                        <picture>
                                            <source data-lazy="/uploads/no_image.webp" srcset="/images/pixel.webp" type="image/webp">
                                            <source data-lazy="/uploads/no_image.jpg" srcset="/images/pixel.jpg" type="image/jpeg">
                                            <img src="/images/pixel.jpg" alt="{{ $product->name }}">
                                        </picture>
                                    @else
                                        {{--<img src="{{ $product->image->url('product') }}" alt="{{ $product->name }}">--}}
                                        {!! $product->image->webp_image('product', ['alt' => $product->name], 'slider') !!}
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @endif
                </div>
                <div class="col-md-8 col-sm-7 col-xs-12">
                    <div class="product-description-wrp">
                        <p class="product-article">Артикул: {{ $product->articul }}</p>
                        <p class="product-name">{{ $product->name }}</p>
                        @if($product->stock)
                            <p class="product-available">Есть в наличии</p>
                        @else
                            <p class="product-unavailable">Нет в наличии</p>
                        @endif
                        <p class="product-decription">{!! $product->description !!}</p>
                        <p class="product-price">{{ number_format($product->price, 2, '.', ' ') }} грн</p>
                        <button class="product-buy-btn btn_buy popup-btn"  data-mfp-src="#cart-popup" data-prod-id="{{ $product->id }}">В корзину</button>
                        <button class="product-click-btn popup-btn"  data-mfp-src="#click-buy-popup">Купить в один клик</button>
                        <ul class="product-features">
                            <li>
                                <a class="flash" href="viber://chat?number=+380509712569" style="color: #000; text-decoration: none;">
                                    <img src="/images/icons/prod-viber.png" alt="Консультация по Viber">
                                    <p>Консультация по Viber</p>
                                </a>
                            </li>
                            <li>
                                <img src="/images/icons/prod-wallet.png" alt="Оплата любым способом">
                                <p>Оплата любым способом</p>
                            </li>
                            <li>
                                <img src="/images/icons/prod-incog.png" alt="Конфиденциальная доставка">
                                <p>Конфиденциальная доставка</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

     <div class="mfp-hide">
        <div id="cart-popup" class="view-popup">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 col-sm-12 col-sm-offset-0 col-xs-12">
                        <div class="question-popup__container">
                            <p class="question-popup__container-title">К Вам в корзину добавлен: </p>
                            <p class="product-name">{{ $product->name }}</p>
                            @if(!empty($product->image))
                            <img class="question-popup__container-img" src="{{ $product->image->url('product') }}" alt="{{ $product->name }}">
                            @endif
                            <div class="question-popup__container-btns">
                                <button title="Close (Esc)" type="button" class="cart-popup__continue-btn mfp-close">Продолжить покупки</button>
                                <a href="/checkout" class="cart-popup__cart-btn">Перейти в корзину</a>
                            </div>
                            <button title="Close (Esc)" type="button" class="mfp-close">×</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mfp-hide">
        <div id="click-buy-popup" class="view-popup">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="click-buy-popup__container">
                            <p class="click-buy-popup__container-title">Товар для заказа</p>
                            <form action="/sendmail" class="click-buy-popup__form ajax_form"
                                  data-error-title="Ошибка отправки!"
                                  data-error-message="Попробуйте отправить вопрос через некоторое время."
                                  data-success-title="Спасибо за вопрос!"
                                  data-success-message="Наш менеджер свяжется с вами в ближайшее время.">
                                <input type="hidden" name="form" value="Купить в 1 клик" data-title="Форма">
                                <input type="hidden" name="product_name" value="{{ $product->name }}" data-title="Название товара">
                                <input type="hidden" name="product_id" value="{{ $product->id }}" data-title="ID товара">
                                <input type="hidden" name="product_articul" value="{{ $product->articul }}" data-title="Артикул товара">
                                <div class="click-buy-popup__form-product">
                                    @if(!empty($product->image))
                                    <img src="{{ $product->image->url('product') }}" alt="{{ $product->name }}">
                                    @endif
                                    <div class="prod-name">
                                        <p>{{ $product->name }}</p>
                                        <span>Артикул: {{ $product->articul }}</span>
                                    </div>
                                    <div class="prod-quont">
                                        <input pattern="^[ 0-9]+$" type="number" min="1" name="qty" value="1" data-title="Колличество" data-price="{{ $product->price }}">
                                        <span>шт</span>
                                        <p class="prod-price visible-xs-block">{{ $product->price }} грн</p>
                                    </div>
                                    <p class="prod-price hidden-xs">{{ $product->price }} грн</p>
                                </div>
                                <p class="total">Итого: <span class="result-price">{{ $product->price }} грн</span> </p>
                                <p>Введите Ваш номер телефона и наш менеджер поможет Вам оформить</p>
                                <div class="click-buy-popup__form-btn-wrp">
                                    <input type="tel" name="phone" placeholder="+38 (___) ___ __ __" data-title="Телефон" data-validate-required="Обязательное поле" data-validate-uaphone="Неправильный номер">
                                    <button type="submit">Оформить заказ</button>
                                </div>
                            </form>
                            <button title="Close (Esc)" type="button" class="mfp-close">×</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="product__tabs">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#1" data-toggle="tab">Описание</a></li>
            {{--<li><a href="#2" data-toggle="tab">Отзывы</a></li>--}}
        </ul>
        <div class="container">
            <div class="row">
                <div class="col-sm-9 col-xs-12 tab-content clearfix">
                    <div class="tab-pane active" id="1">
                        <div class="product__tabs-descr">
                            {!! $product->description !!}
                            <table style="font-size: 16px;">
                                @foreach($product_attributes as $attr => $values)
                                    <tr>
                                        <td style="padding-right: 15px">{{ $attr }}:</td>
                                        <td>{{ implode(', ', $values) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="2">
                        @if(!empty($user))
                            <form action="" class="response-form">
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="name" value="{{ $user->first_name  }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">
                                <input type="hidden" name="grade" value="5">
                                <textarea name="review" class="response-form__textarea clear-styles" placeholder="Оставить отзыв"></textarea>
                                <button type="submit" class="response-form__btn clear-styles">Отправить</button>
                            </form>
                        @endif
                        {{--<div class="user-response__item">--}}
                            {{--<div  class="user-response__item-name-wrp">--}}
                                {{--<p class="user-response__item-name">Анастасия</p>--}}
                                {{--<p class="user-response__item-date">01.09.2018</p>--}}
                            {{--</div>--}}
                            {{--<p class="user-response__item-text">Рейтинг, безусловно, основан на опыте повседневного применения. Бизнес-план, отбрасывая подробности, позиционирует продукт. Отсюда естественно следует, что внутрифирменная реклама консолидирует рекламный макет.</p>--}}
                        {{--</div>--}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(!empty($related))
    <section>
        <p class="section-title">Похожие товары</p>
        <div class="container">
            <div class="row">
                <div class="slick-prod-wrap">
                    <div class="slick-slider slick-prod" data-slick='{"slidesToShow": 6, "dots": false, "arrows": false, "responsive":[{"breakpoint":991,"settings":{"slidesToShow": 4, "centerMode": true}}, {"breakpoint":768,"settings":{"slidesToShow": 3, "centerMode": true}}]}'>
                        @foreach($related as $key => $prod)
                            <div class="col-md-2 col-sm-4 col-xs-4">
                                <div class="product-item top">
                                    @include('public.layouts.product', ['product' => $prod])
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
@endsection
