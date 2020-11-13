@extends('public.layouts.main')
@section('meta')
    <title>{!! $settings->{'meta_title_'.app()->getLocale()} !!}</title>
    <meta name="description" content="{!! $settings->{'meta_description_'.app()->getLocale()} !!}">
    <meta name="keywords" content="{!! $settings->{'meta_keywords_'.app()->getLocale()} !!}">
@endsection
@section('page_vars')
    <style>
        .section-1 .banner picture{
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
        }
        .section-1 .banner picture img{
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    @include('public.layouts.microdata.local_business')
    @include('public.layouts.microdata.open_graph', [
     'title' => $settings->{'meta_title_'.app()->getLocale()},
     'description' => $settings->{'meta_description_'.app()->getLocale()},
     'image' => '/images/logo.png',
     'type' => trans('app.home')
     ])
@endsection

@section('content')

    @if($slideshow->count())
        <section class="section-1">
            <div class="slick-slider header-slider" data-slick='{"slidesToShow": 1, "dots": true}'>
                @foreach($slideshow as $slide)
                    @if($slide->status)
                        <div>
                            <div class="banner">
                                {!! $slide->image->webp_image('full', ['alt' => $slide->data()->slide_title]) !!}
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-5 col-sm-7 col-xs-8" style="display: none;">
                                            <p class="main-title">{!! $slide->data()->slide_title !!}</p>
                                            <p class="main-title">{!! $slide->data()->slide_description !!}</p>
                                        </div>
                                        <div class="col-sm-12 col-xs-12" style="position: absolute;left: 0;width: 100%;bottom: 7vh;display: flex;justify-content: center;">
                                            <a href="{{ $slide->link }}" class="banner-btn">{{ $slide->data()->button_text }}</a>
                                        </div>

                                        {{--<div class="header-slider__item">--}}
                                            {{--<img src="{{ $slide->image->url() }}" alt="{{ $slide->data()->slide_title }}">--}}
                                            {{--<div class="header-slider__item-text">--}}
                                                {{--<p class="header-slider-title">{!! $slide->data()->slide_title !!}</p>--}}
                                                {{--<p class="header-slider-descr">{{ $slide->data()->slide_description }}</p>--}}
                                                {{--<a href="{{ $slide->link }}">--}}
                                                    {{--<p  class="header-slider-btn">{{ $slide->data()->button_text }}</p>--}}
                                                {{--</a>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    <section>
        <div class="container">
            <p class="section-title">{{ trans('app.catalog') }}</p>
            <div class="row">
                @foreach($categories as $category)
                    @if(!empty($category->image))
                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <div class="category-item">
                                <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/{{ $category->url_alias }}" class="category-item__img">
                                    {{--<img src="{{ $category->image->url() }}" alt="{{ $category->name }}">--}}
                                    {!! $category->image->webp_image([200, 200], ['alt' => $category->name], 'static') !!}
                                </a>
                                <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/{{ $category->url_alias }}" class="category-item__title">{{ $category->name }}</a>
                                <div class="category-item__btn">
                                    <a  class="category-item__btn-more" href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/{{ $category->url_alias }}">{{ trans('app.More_details') }}</a>
                                    @if($category->children->count())
                                    <div class="category-details"></div>
                                    <ul class="category_links">
                                        @foreach($category->children as $children)
                                        <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/{{ $children->url_alias }}">{{ $children->name }}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>
                            </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    {{--<section class="section-2">--}}
        {{--<div class="container-fluid">--}}
            {{--<p class="section-title">Каталог товаров</p>--}}
            {{--<div class="row">--}}
                {{--<div class="col-sm-6 col-xs-12">--}}
                    {{--<div class="catalor-category man">--}}
                        {{--<ul>--}}
                            {{--<li class="catalor-category__title">Для мужчин</li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-sm-6 col-xs-12">--}}
                    {{--<div class="catalor-category women">--}}
                        {{--<ul>--}}
                            {{--<li class="catalor-category__title">Для женщин</li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-sm-6 col-xs-12">--}}
                    {{--<div class="catalor-category couple">--}}
                        {{--<ul>--}}
                            {{--<li class="catalor-category__title">Для пары</li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-sm-6 col-xs-12">--}}
                    {{--<div class="catalor-category gift">--}}
                        {{--<ul>--}}
                            {{--<li class="catalor-category__title">Для подарка</li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                            {{--<li><a href="">Категория</a></li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}

    <section class="section-3">
        <div class="container">
            @if(!empty($shops))
            <p class="section-title">{{ trans('app.our_Stores') }}</p>
            <div class="row">
                <div class="slick-slider" data-slick='{"slidesToShow": 3, "slidesToScroll": 1, "rows": 3, "arrows": false, "responsive": [{"breakpoint": 768, "settings": {"slidesToShow": 1, "rows": 1, "centerMode": true}}]}'>
                    @foreach($shops as $shop)
                    <div class="col-sm-4 col-xs-6">
                        <div class="store-address">
                            {{--<img src="{!! $shop->image->url() !!}" alt="{{ $shop->slide_title }}">--}}
                            {!! $shop->image->webp_image([360, 240], ['alt' => $shop->slide_title], 'static') !!}
                            <p>{!! $shop->slide_title !!}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            <p class="section-title">{{ trans('app.bestsellers') }}</p>
        </div>
    </section>

    @if(!empty($bestsellers))
    <section class="section-4">
        <div class="container">
            <div class="row">
                <div class="slick-prod-wrap">
                    <div class="slick-slider slick-prod" data-slick='{"slidesToShow": 6, "dots": false, "arrows": false, "responsive":[{"breakpoint":1199,"settings":{"slidesToShow": 4, "centerMode": true}}, {"breakpoint":991,"settings":{"slidesToShow": 3, "centerMode": true}}, {"breakpoint":768,"settings":{"slidesToShow": 2, "centerMode": true}}, {"breakpoint":480,"settings":{"slidesToShow": 1, "centerMode": true}}]}'>
                        @foreach($bestsellers as $bestseller)
                        <div class="col-md-2 col-sm-4 col-xs-4">
                            <div class="product-item top">
                                @include('public.layouts.product', ['product' => $bestseller, 'lazy' => 'slider'])
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="section-6">
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <div class="text-section-img">
                        {{--<img src="/images/sec-logo.jpg" alt="">--}}
                        <picture>
                            <source data-src="/images/sec-logo.webp" srcset="/images/pixel.webp" type="image/webp">
                            <source data-src="/images/sec-logo.png" srcset="/images/pixel.jpeg" type="image/jpeg">
                            <img src="/images/pixel.png" alt="Sex Shop">
                        </picture>
                    </div>
                </div>
                <div class="col-sm-offset-1 col-sm-8">
                    <p class="text-section">{{ trans('app.intimate_life_plays_an_important_role_in_everyday_life_and_a_persons_success') }}</p>
                </div>
            </div>
            <p class="section-title">{{ trans('app.assortment_sex_shop') }}</p>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <p class="assort-text">{!! trans('app.a_variety_of_goods_for_personal_and_couples_use_in_an_intimate') !!}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-7">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 feature-map">
                    <div class="feature-map__img">
                        {{--<img src="/images/feature/1.png" alt="">--}}
                        <picture>
                            <source data-src="/images/feature/1.webp" srcset="/images/pixel.webp" type="image/webp">
                            <source data-src="/images/feature/1.png" srcset="/images/pixel.png" type="image/png">
                            <img src="/images/pixel.jpg" alt="Sex Shop">
                        </picture>
                    </div>
                    <div class="feature-map__text">
                        <p class="feature-map__text-title left">{{ trans('app.reliable_store') }}</p>
                        <p>{{ trans('app.especially_for_you,_there_is_a_huge_selection_in_the_sex_shop_intimate') }}</p>
                    </div>
                </div>

                <div class="col-sm-12 feature-map">
                    <div class="feature-map__text right">
                        <p class="feature-map__text-title right">{{ trans('app.nice_prices') }}</p>
                        <p>{{ trans('app.due_to_the_fact_that_all_intermediaries_are_excluded_from_the_manufacturer-consumer_chain') }}</p>
                    </div>
                    <div class="feature-map__img">
                        {{--<img src="/images/feature/2.png" alt="">--}}
                        <picture>
                            <source data-src="/images/feature/2.webp" srcset="/images/pixel.webp" type="image/webp">
                            <source data-src="/images/feature/2.png" srcset="/images/pixel.png" type="image/png">
                            <img src="/images/pixel.jpg" alt="Sex Shop">
                        </picture>
                    </div>
                </div>

                <div class="col-sm-12 feature-map">
                    <div class="feature-map__img">
                        {{--<img src="/images/feature/3.png" alt="">--}}
                        <picture>
                            <source data-src="/images/feature/3.webp" srcset="/images/pixel.webp" type="image/webp">
                            <source data-src="/images/feature/3.png" srcset="/images/pixel.png" type="image/png">
                            <img src="/images/pixel.jpg" alt="Sex Shop">
                        </picture>
                    </div>
                    <div class="feature-map__text">
                        <p class="feature-map__text-title left">{{ trans('app.complete_confidentiality') }}</p>
                        <p>{{ trans('app.for_many_people_their_shyness_becomes_a_stopping_factor_in_visiting_an_intimate_goods_store') }}</p>
                    </div>
                </div>

                <div class="col-sm-12 feature-map">
                    <div class="feature-map__text right">
                        <p class="feature-map__text-title right">{{ trans('app.make_a_choice') }}</p>
                        <p>{{ trans('app.the_desire_to_satisfy_your_sexual_needs_depends_only_on_you') }}</p>
                    </div>
                    <div class="feature-map__img">
                        {{--<img src="/images/feature/4.png" alt="">--}}
                        <picture>
                            <source data-src="/images/feature/4.webp" srcset="/images/pixel.webp" type="image/webp">
                            <source data-src="/images/feature/4.png" srcset="/images/pixel.png" type="image/png">
                            <img src="/images/pixel.jpg" alt="Sex Shop">
                        </picture>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-8">
        <div class="container">
            <p class="about-us-title">{{ trans('app.about_store') }}</p>
            {!! $settings->{'about_'.app()->getLocale()} !!}
        </div>
    </section>
    
    <div class="mfp-hide">
        @foreach($bestsellers as $key => $product)
        <div id="cart-popup_{{ $product->id }}" class="view-popup">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 col-sm-12 col-sm-offset-0 col-xs-12">
                        <div class="question-popup__container">
                            <p class="question-popup__container-title">{{ trans('app.added_to_your_cart') }} </p>
                            <p class="product-name">{{ $product->name }}</p>
{{--                            <img class="question-popup__container-img" src="{{ $product->image == null ? '/uploads/no_image.jpg' : $product->image->url('product_list') }}" alt="{{ $product->name }}">--}}
                            @if($product->image == null)
                                <picture>
                                    <source data-src="/uploads/no_image.webp" srcset="/images/pixel.webp" type="image/webp">
                                    <source data-src="/uploads/no_image.jpg" srcset="/images/pixel.jpg" type="image/jpeg">
                                    <img src="/images/pixel.jpg" alt="{{ $product->name }}" class="question-popup__container-img">
                                </picture>
                            @else
                                {!! $product->image->webp_image('product_list', ['alt' => $product->name, 'class' => 'question-popup__container-img'], !empty($lazy) ? $lazy : 'static') !!}
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
        @endforeach
    </div>
@endsection