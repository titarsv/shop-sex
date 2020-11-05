@extends('public.layouts.main', ['root_category' => $root_category])
@section('meta')
    <title>{{ trans('app.buy') }} {{ $product->name }} | {{ trans('app.sex_shop_in_kharkov_online_store') }} | Shop-sex.com.ua</title>
    <meta name="description" content="{{ $product->name }} {{ trans('app.at_the_best_prices_in_kharkov_ukraine_anonymity_guarantee') }}">
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

    <section style="margin-bottom: 40px;">
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
                        <p class="product-article">{{ trans('app.vendor_code') }} {{ $product->articul }}</p>
                        <p class="product-name">{{ $product->name }}</p>
                        @if($product->stock)
                            <p class="product-available">{{ trans('app.are_available') }}</p>
                        @else
                            <p class="product-unavailable">{{ trans('app.not_available') }}</p>
                        @endif
                        <p class="product-decription">{!! $product->description !!}</p>
                        <table style="font-size: 14px; margin-top: 15px; border-collapse: collapse;">
                            @foreach($product_attributes as $attr => $values)
                                <tr>
                                    <td style="padding: 5px; border: 1px solid black;">{{ $attr }}:</td>
                                    <td style="padding: 5px; border: 1px solid black;">{{ implode(', ', $values) }}</td>
                                </tr>
                            @endforeach
                        </table>
                        <p class="product-price">{{ number_format($product->price, 2, '.', ' ') }} {{ trans('app.hryvnias') }}</p>
                        <button class="product-buy-btn btn_buy popup-btn"  data-mfp-src="#cart-popup" data-prod-id="{{ $product->id }}">{{ trans('app.in_garbage') }}</button>
                        <button class="product-click-btn popup-btn"  data-mfp-src="#click-buy-popup">{{ trans('app.buy_in_one_click') }}</button>
                        <ul class="product-features">
                            <li>
                                <a class="flash" href="viber://chat?number=+380509712569" style="color: #000; text-decoration: none;">
                                    <img src="/images/icons/prod-viber.png" alt="{{ trans('app.viber_consultation') }}">
                                    <p>{{ trans('app.viber_consultation') }}</p>
                                </a>
                            </li>
                            <li>
                                <img src="/images/icons/prod-wallet.png" alt="{{ trans('app.payment_in_any_way') }}">
                                <p>{{ trans('app.payment_in_any_way') }}
                                    <span>{{ trans('app.and_even') }} <img src="/images/liqpay.jpg" alt="liqpay">
                                        <div class="product-features__tooltip">
                                            {{ trans('app.the_site_is_connected_to_electronic_payments_now_you_can_buy_goods_with_payment_online') }}
                                        </div>
                                    </span>
                                </p>
                            </li>
                            <li>
                                <img src="/images/icons/prod-incog.png" alt="{{ trans('app.confidential_delivery') }}">
                                <p>{{ trans('app.confidential_delivery') }}</p>
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
                            <p class="question-popup__container-title">{{ trans('app.added_to_your_cart') }} </p>
                            <p class="product-name">{{ $product->name }}</p>
                            @if(!empty($product->image))
                            <img class="question-popup__container-img" src="{{ $product->image->url('product') }}" alt="{{ $product->name }}">
                            @endif
                            <div class="question-popup__container-btns">
                                <button title="Close (Esc)" type="button" class="cart-popup__continue-btn mfp-close">{{ trans('app.continue_shopping') }}</button>
                                <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/checkout" class="cart-popup__cart-btn">{{ trans('app.go_to_cart') }}</a>
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
                            <p class="click-buy-popup__container-title">{{ trans('app.item_to_order') }}</p>
                            <form action="/sendmail" class="click-buy-popup__form ajax_form"
                                  data-error-title="{{ trans('app.send_error') }}"
                                  data-error-message="{{ trans('app.try_to_send_a_question_after_a_while') }}"
                                  data-success-title="{{ trans('app.thanks_for_the_question') }}"
                                  data-success-message="{{ trans('app.our_manager_will_contact_you_soon') }}">
                                <input type="hidden" name="form" value="{{ trans('app.buy_in_1_click') }}" data-title="{{ trans('app.the_form') }}">
                                <input type="hidden" name="product_name" value="{{ $product->name }}" data-title="{{ trans('app.product_name') }}">
                                <input type="hidden" name="product_id" value="{{ $product->id }}" data-title="{{ trans('app.item_id') }}">
                                <input type="hidden" name="product_articul" value="{{ $product->articul }}" data-title="{{ trans('app.item_number') }}">
                                <div class="click-buy-popup__form-product">
                                    @if(!empty($product->image))
                                    <img src="{{ $product->image->url('product') }}" alt="{{ $product->name }}">
                                    @endif
                                    <div class="prod-name">
                                        <p>{{ $product->name }}</p>
                                        <span>{{ trans('app.vendor_code') }} {{ $product->articul }}</span>
                                    </div>
                                    <div class="prod-quont">
                                        <input pattern="^[ 0-9]+$" type="number" min="1" name="qty" value="1" data-title="{{ trans('app.amount') }}" data-price="{{ $product->price }}">
                                        <span>{{ trans('app.pc') }}</span>
                                        <p class="prod-price visible-xs-block">{{ $product->price }} {{ trans('app.hryvnias') }}</p>
                                    </div>
                                    <p class="prod-price hidden-xs">{{ $product->price }} {{ trans('app.hryvnias') }}</p>
                                </div>
                                <p class="total">{{ trans('app.total') }}: <span class="result-price">{{ $product->price }} {{ trans('app.hryvnias') }}</span> </p>
                                <p>{{ trans('app.enter_your_phone_number_and_our_manager_will_help_you_arrange') }}</p>
                                <div class="click-buy-popup__form-btn-wrp">
                                    <input type="tel" name="phone" placeholder="+38 (___) ___ __ __" data-title="{{ trans('app.phone') }}" data-validate-required="{{ trans('app.obligatory_field') }}" data-validate-uaphone="Неправильный номер">
                                    <button type="submit">{{ trans('app.checkoutt') }}</button>
                                </div>
                            </form>
                            <button title="Close (Esc)" type="button" class="mfp-close">×</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($related))
    <section>
        <p class="section-title">{{ trans('app.similar_Products') }}</p>
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
