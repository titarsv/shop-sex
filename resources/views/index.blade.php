@extends('public.layouts.main')
@section('meta')
    <title>{!! $settings->meta_title !!}</title>
    <meta name="description" content="{!! $settings->meta_description !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords !!}">
@endsection
@section('page_vars')
    @include('public.layouts.microdata.local_business')
    @include('public.layouts.microdata.open_graph', [
     'title' => $settings->meta_title,
     'description' => $settings->meta_description,
     'image' => '/images/logo.png',
     'type' => 'главная'
     ])
@endsection

@section('content')

    @if($slideshow->count())
        <section class="section-1">
            <div class="slick-slider header-slider" data-slick='{"slidesToShow": 1, "dots": true}'>
                @foreach($slideshow as $slide)
                    @if($slide->status)
                        <div>
                            <div class="banner" style="background: center url('{{ $slide->image->url() }}') no-repeat; background-size: cover;">
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
            <p class="section-title">Каталог товаров</p>
            <div class="row">
                @foreach($categories as $category)
                    @if(!empty($category->image))
                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <div class="category-item">
                                <a href="{{env('APP_URL')}}/catalog/{{ $category->url_alias }}" class="category-item__img">
                                    {{--<img src="{{ $category->image->url() }}" alt="{{ $category->name }}">--}}
                                    {!! $category->image->webp_image([200, 200], ['alt' => $category->name], 'static') !!}
                                </a>
                                <a href="{{env('APP_URL')}}/catalog/{{ $category->url_alias }}" class="category-item__title">{{ $category->name }}</a>
                                <div class="category-item__btn">
                                    <a  class="category-item__btn-more" href="{{env('APP_URL')}}/catalog/{{ $category->url_alias }}">Подробнее</a>
                                    @if($category->children->count())
                                    <div class="category-details"></div>
                                    <ul class="category_links">
                                        @foreach($category->children as $children)
                                        <li><a href="{{env('APP_URL')}}/catalog/{{ $children->url_alias }}">{{ $children->name }}</a></li>
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
            <p class="section-title">Наши магазины</p>
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
            <p class="section-title">Хиты продаж</p>
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
                        <img src="/images/sec-logo.jpg" alt="">
                    </div>
                </div>
                <div class="col-sm-offset-1 col-sm-8">
                    <p class="text-section">Интимная жизнь играет немаловажную роль в повседневных делах и успехе человека. Даже полностью удовлетворяющий все потребности партнер со временем теряет свою хватку и холодеет в постели. Это ухудшает качество выполняемой им работы, влияет на здоровье и вносит разногласие в семейную жизнь. Чтобы наладить свои половые отношения и разнообразить секс, вам стоит заглянуть в секс шоп, где вы найдете много интересного для себя и партнера.</p>
                </div>
            </div>
            <p class="section-title">Ассортимент sex shop</p>
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <p class="assort-text">Разнообразие товаров для личного и парного использования в магазине интима позволит выбрать все необходимые средства и вещи, которые помогут в становлении половой жизни на прежнюю колею. Гарантированное качество позволит уверенно пользоваться ими и проводить тест-драйвы в самых необычных местах, при любых обстоятельствах.<br> Sex shop «Интим» создан специально, чтобы удовлетворять самые яркие фантазии взрослого человека. Одинокие молодые люди, любовники и семейные пары могут выбрать в свое пользование из каталогов тот товар, которые по их мнению идеально подойдет их амбициям, ведь различное наименование товара удовлетворит вкусы самого изощренного в сексуальных делах человека. Секс шоп позволит найти полезности для личных забав, необычного подарка своему товарищу и для исполнения маленьких капризов своей второй половинки.<br> Самый большой выбор сексуальных игрушек, смазок, препаратов для потенции и костюмов обязан удовлетворить вас во всех пониманиях этого слова. Sex shop необходим каждому человеку, главное знать и понимать свои желания и меру раскрепощенности избранника.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-7">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 feature-map">
                    <div class="feature-map__img">
                        <img src="/images/feature/1.png" alt="">
                    </div>
                    <div class="feature-map__text">
                        <p class="feature-map__text-title left">Надежный магазин</p>
                        <p>Специально для вас предоставлен огромный выбор в секс шоп «Интим». Приобретая игрушку или одежду у надежного магазина, вы гарантированно получаете только качественный товар, из материала, который одобрен для использования в сексуальных целях. Высококачественные ткани, гигиеничные материалы и долговечные механизмы предназначены для долгих и качественных половых актов. Приобретая интимные товары, удостоверьтесь, что посетили официальный сайт, так как наш магазин работает напрямую с изготовителем, мы ручаемся за качество каждой проданной вещи. Наш секс шоп уверен в качестве и безопасности каждой позиции в каталоге, чем не могут похвастаться другие магазины.</p>
                    </div>
                </div>

                <div class="col-sm-12 feature-map">
                    <div class="feature-map__text right">
                        <p class="feature-map__text-title right">Приятные цены</p>
                        <p>Благодаря тому, что из цепочки изготовитель-потребитель исключены все посредники вы можете выбирать дешевый товар. Низкая стоимость не говорит о плохом качестве. Ценообразующими показателями у нас является только качество товара. Каждый отдельный материал протестирован и допущен к изготовлению игрушек и одежды. Вся Украина оценила низкую стоимость, что и повлекло за собой крупное расширение ассортимента, который вы можете увидеть. Интернет магазин исключает дополнительную надбавку стоимости на коммунальные платежи, транспортные расходы и зарплаты работникам обычного магазина. Покупая в sex shop «Интим», вы платите только за приобретенный товар.Благодаря тому, что из цепочки изготовитель-потребитель исключены все посредники вы можете выбирать дешевый товар. Низкая стоимость не говорит о плохом качестве. Ценообразующими показателями у нас является только качество товара. Каждый отдельный материал протестирован и допущен к изготовлению игрушек и одежды. Вся Украина оценила низкую стоимость, что и повлекло за собой крупное расширение ассортимента, который вы можете увидеть. Интернет магазин исключает дополнительную надбавку стоимости на коммунальные платежи, транспортные расходы и зарплаты работникам обычного магазина. Покупая в sex shop «Интим», вы платите только за приобретенный товар.</p>
                    </div>
                    <div class="feature-map__img">
                        <img src="/images/feature/2.png" alt="">
                    </div>
                </div>

                <div class="col-sm-12 feature-map">
                    <div class="feature-map__img">
                        <img src="/images/feature/3.png" alt="">
                    </div>
                    <div class="feature-map__text">
                        <p class="feature-map__text-title left">Полная конфиденциальность</p>
                        <p>Для многих людей, останавливающим фактором в посещении магазина интимных товаров становится их стеснительность, другие не могут позволить себе посетить секс шоп из-за своего положения в обществе. Благодаря нашим услугам, вы легко сможете подобрать себе понравившийся товар, не опасаясь огласки коллег или знакомых. Каждая покупка отправляется получателю в упаковке, без опознавательных знаков. Курьеры и почтовые службы не узнают, какой характер несет ваша покупка, поэтому интимная жизнь клиента остается вне осуждений и разговоров. Большой выбор крупного и мелкого товара, который предлагает покупателю секс шоп «Интим» носит разнообразный характер. Разнообразие в постели — дело личное, хотя и вполне нормальное. Мы ценим желания каждого клиента и не разглашаем никакой информации о ваших покупках и гарантирует полную конфиденциальность.</p>
                    </div>
                </div>

                <div class="col-sm-12 feature-map">
                    <div class="feature-map__text right">
                        <p class="feature-map__text-title right">Делайте выбор</p>
                        <p>От вас зависит только желание удовлетворить свои потребности в половой жизни. Секс шоп предоставляет все необходимое для этого. Пользоваться интимным бельем вполне нормально для мужчины и женщины во всей Украине, поэтому ваш заказ доставят не только в Харьков, но и в любой населенный пункт страны. Улучшите, разнообразьте и украсьте свои достижения в кровати, а мы в этом поможем.</p>
                    </div>
                    <div class="feature-map__img">
                        <img src="/images/feature/4.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-8">
        <div class="container">
            <p class="about-us-title">О магазине</p>
            {!! $settings->about !!}
        </div>
    </section>
    
    <div class="mfp-hide">
        @foreach($bestsellers as $key => $product)
        <div id="cart-popup_{{ $product->id }}" class="view-popup">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 col-sm-12 col-sm-offset-0 col-xs-12">
                        <div class="question-popup__container">
                            <p class="question-popup__container-title">К Вам в корзину добавлен: </p>
                            <p class="product-name">{{ $product->name }}</p>
                            <img class="question-popup__container-img" src="{{ $product->image == null ? '/uploads/no_image.jpg' : $product->image->url('product_list') }}" alt="{{ $product->name }}">
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
        @endforeach
    </div>

    {{--@if($slideshow->count())--}}
        {{--<div class="header-slider-wrp">--}}
            {{--<div class="container-fluid">--}}
                {{--<div class="row">--}}
                    {{--<div class="js-slider slick-slider header-slider" data-slick='{"slidesToShow": 1, "dots": true, "responsive": [{"breakpoint":768,"settings":{"slidesToShow": 1, "dots": false}}]}'>--}}
                        {{--@foreach($slideshow as $slide)--}}
                            {{--@if($slide->status)--}}
                                {{--<div>--}}
                                    {{--<div class="slider-item-1" style="background-image: url({{ $slide->image->url() }})">--}}
                                        {{--<div class="row">--}}
                                            {{--<div class="com-md-12 slider-title">--}}
                                                {{--<h2>{{ $slide->data()->slide_title }}</h2>--}}
                                                {{--<h3>{{ $slide->data()->slide_description }}</h3>--}}
                                            {{--</div>--}}
                                            {{--<div class="com-md-12 slider-btn-wrp">--}}
                                                {{--<a href="{{ $slide->link }}" class="slider-btn">--}}
                                                    {{--<p>{{ $slide->data()->button_text }}</p>--}}
                                                {{--</a>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                        {{--@endforeach--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--@endif--}}

    {{--<section class="brand-navigation">--}}
        {{--<div class="container-fluid">--}}
            {{--<div class="row index-brand-wrp">--}}
                {{--<div class="col-sm-12">--}}
                    {{--<h3 class="section-title">--}}
                        {{--Популярные бренды--}}
                    {{--</h3>--}}
                {{--</div>--}}
                {{--<div class="col-sm-12 col-xs-12">--}}
                    {{--<div class="js-slider slick-slider slider-brands"--}}
                         {{--data-slick='{"slidesToShow": 6, "responsive":[{"breakpoint":1200,"settings":{"slidesToShow": 4}},{"breakpoint":768,"settings":{"slidesToShow": 3, "arrows": false}},{"breakpoint":480,"settings":{"slidesToShow": 2, "arrows": false}}]}'>--}}
                        {{--@foreach($brands as $brand)--}}
                            {{--<div class="slider-brand-item">--}}
                                {{--<a href="{{env('APP_URL')}}/catalog/tovary/brend-{{ $brand->value }}">--}}
                                    {{--<p>{{ $brand->name }}</p>--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--@endforeach--}}
                    {{--</div>--}}
                    {{--<div class="col-md-12 all-brands-link">--}}
                        {{--<a href="{{env('APP_URL')}}/brands">--}}
                            {{--<p>Все бренды</p>--}}
                        {{--</a>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
    {{--<section class="main-content">--}}
        {{--<div class="container-fluid">--}}
            {{--<div class="row main-content-wrp">--}}
                {{--<a href="{{env('APP_URL')}}/catalog/dlya-zhenschin/specpredlozhenija-newcollection">--}}
                    {{--<div class="col-md-6 col-sm-5 col-xs-12">--}}
                        {{--<div class="new-post new-for-her">--}}
                            {{--<div>--}}
                                {{--<h4>Новинки для нее</h4>--}}
                                {{--<p>Смотреть</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</a>--}}
                {{--<div class="col-md-6 col-sm-7 col-xs-12">--}}
                    {{--<div class="slick-prod-wrap">--}}
                        {{--<div class="slick-slider slick-prod popular-slider"--}}
                             {{--data-slick='{"slidesToShow":2, "slidesToScroll":2, "arrows": false, "lazyLoad": "ondemand", "responsive":[ {"breakpoint":768,"settings":{"slidesToShow":2, "slidesToScroll":1, "arrows": false, "lazyLoad": "ondemand"}}]}'>--}}
                            {{--@foreach($women_new_prod as $prod)--}}
                                {{--<div>--}}
                                    {{--<div class="grid-product-card card-margin">--}}
                                        {{--@include('public.layouts.product', ['product' => $prod, 'slide' => true])--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endforeach--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<div class="container-fluid">--}}
            {{--<div class="row main-content-wrp">--}}
                {{--<div class="col-md-6 col-md-push-6 col-sm-5 col-sm-push-7 col-xs-12">--}}
                    {{--<a href="{{env('APP_URL')}}/catalog/dlya-muzhchin/specpredlozhenija-newcollectionn">--}}
                        {{--<div class="new-post new-for-him">--}}
                            {{--<div>--}}
                                {{--<h4>Новинки для него</h4>--}}
                                {{--<p>Смотреть</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</a>--}}
                {{--</div>--}}

                {{--<div class="col-md-6 col-md-pull-6 col-sm-7 col-sm-pull-5 col-xs-12">--}}
                    {{--<div class="slick-prod-wrap">--}}
                        {{--<div class="slick-slider slick-prod popular-slider"--}}
                             {{--data-slick='{"slidesToShow":2, "slidesToScroll":2, "arrows": false, "lazyLoad": "ondemand", "responsive":[ {"breakpoint":768,"settings":{"slidesToShow":2, "slidesToScroll":1, "arrows": false, "lazyLoad": "ondemand"}}]}'>--}}
                            {{--@foreach($men_new_prod as $prod)--}}
                                {{--<div>--}}
                                    {{--<div class="grid-product-card card-margin">--}}
                                        {{--@include('public.layouts.product', ['product' => $prod, 'slide' => true])--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endforeach--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
    {{--<section class="sales-banner">--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--@include('public.layouts.banner')--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
    {{--<section>--}}
        {{--<div class="container">--}}
            {{--<div class="row bag-category-section">--}}
                {{--<div class="col-md-8 col-sm-12 col-xs-12 bag-category-section-img">--}}
                    {{--<a href="{{env('APP_URL')}}/catalog/tovary/tip-sumki_specpredlozhenija-newcollection">--}}
                        {{--<div class="bag-category-img">--}}
                            {{--<div>--}}
                                {{--<h4>Новинки сумки</h4>--}}
                                {{--<p>Смотреть</p>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</a>--}}
                    {{--<div class="bag-category-banner">--}}
                        {{--<div class="bag-category-banner-img">--}}
                            {{--<img src="/images/homepage-images/sale-banner-man.png" alt="">--}}
                        {{--</div>--}}
                        {{--<div class="bag-category-banner-title">--}}
                            {{--<p>Обувь</p>--}}
                            {{--<span>БОЛЬШИХ размеров</span>--}}
                        {{--</div>--}}
                        {{--<a href="{{env('APP_URL')}}/catalog/tovary/specpredlozhenija-bolshierazmery"--}}
                           {{--class="sales-banner-btn bag-category-banner-btn">--}}
                            {{--<p>Смотреть</p>--}}
                        {{--</a>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-md-4 col-sm-12 hidden-xs">--}}
                    {{--@foreach($big_sizes as $prod)--}}
                        {{--<div class="homepage-product-card">--}}
                            {{--@include('public.layouts.product', ['product' => $prod, 'slide' => true])--}}
                        {{--</div>--}}
                    {{--@endforeach--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
    {{--<section class="inform-card-wrp">--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-sm-4 col-xs-12 inform-card">--}}
                    {{--<a href="{{env('APP_URL')}}/page/delivery">--}}
                        {{--<h5>Доставка</h5>--}}
                        {{--<p>Харьков самовывоз из магазинов. В остальные города доставка осуществляется курьерской--}}
                            {{--компанией "Новая почта" по тарифам перевозчика. </p>--}}
                        {{--<img src="/images/homepage-icons/delivery icon.svg" alt="">--}}
                    {{--</a>--}}
                {{--</div>--}}
                {{--<div class="col-sm-4 col-xs-12 inform-card">--}}
                    {{--<a href="">--}}
                        {{--<h5>Бонусная программа</h5>--}}
                        {{--<p>При покупке от 1000 грн консультант по номеру телефона сделает Вам скидку. Узнать больше о--}}
                            {{--системе начислений Бонусной программы</p>--}}
                        {{--<img src="/images/homepage-icons/bonus icon.svg" alt="">--}}
                    {{--</a>--}}
                {{--</div>--}}
                {{--<div class="col-sm-4 col-xs-12 inform-card">--}}
                    {{--<a href="">--}}
                        {{--<h5>Оплата и возврат</h5>--}}
                        {{--<p>Вы можете оплатить покупки наличными при получении.--}}
                            {{--На все модели действует гарантия, и в случае необходимости вы можете ее вернуть.</p>--}}
                        {{--<img src="/images/homepage-icons/payment icon.svg" alt="">--}}
                    {{--</a>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
    {{--<section class="article-slider-container">--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-sm-12">--}}
                    {{--<h3 class="section-title">--}}
                        {{--Новости и Акции--}}
                    {{--</h3>--}}
                {{--</div>--}}
                {{--<div class="col-sm-12">--}}
                    {{--<div class="social-links">--}}
                        {{--<a href=""><img src="/images/homepage-icons/instagram icon.svg" alt=""></a>--}}
                        {{--<a href=""><img src="/images/homepage-icons/facebook icon.svg" alt=""></a>--}}
                        {{--<a href=""><img src="/images/homepage-icons/vkontakte icon.svg" alt=""></a>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-sm-12">--}}
                    {{--<div class="js-slider slick-slider article-slider"--}}
                         {{--data-slick='{"slidesToShow": 2, "responsive":[{"breakpoint":768,"settings":{"slidesToShow":1}}]}'>--}}
                        {{--@foreach($articles as $article)--}}
                            {{--<div class="article-slider-item">--}}
                                {{--<div class="article-slider-item-img">--}}
                                    {{--<a href="{{env('APP_URL')}}/news/{!!$article->url_alias !!}">--}}
                                        {{--<img src="{!! $article->image->url('blog_list') !!}" alt="">--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                                {{--<h5 class="article-slider-item-title">--}}
                                    {{--<a href="{{env('APP_URL')}}/news/{!!$article->url_alias !!}">{!! $article->title !!}</a>--}}
                                {{--</h5>--}}
                                {{--<span class="article-slider-item-data">--}}
                                        {{--{!! $article->created_at !!}--}}
                                    {{--</span>--}}
                            {{--</div>--}}
                        {{--@endforeach--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<a href="#" class="fixed-up-btn fixed-up-btn-center">--}}
            {{--<i>&#xE809</i>--}}
        {{--</a>--}}

    {{--</section>--}}
    {{--<section class="insta-section">--}}
        {{--<div class="container-fluid">--}}
            {{--<div class="row">--}}
                {{--<div class="col-sm-12">--}}
                    {{--<h3 class="section-title">--}}
                        {{--Поделись своим образом в Instagram--}}
                    {{--</h3>--}}
                    {{--<p>Ставь хештег <a href="#">#tyflicom</a> в Instagram дай возможность другим увидеть твой образ</p>--}}
                {{--</div>--}}
                {{--<div class="col-sm-12">--}}
                    {{--<div class="js-slider slick-slider slider-margins"--}}
                         {{--data-slick='{"slidesToShow": 6,"autoplay":true, "autoplaySpeed": 1000, "arrows": false, "lazyLoad": "ondemand", "responsive":[{"breakpoint":768,"settings":{"slidesToShow": 4, "arrows": false, "autoplay":true, "autoplaySpeed": 1000, "arrows": false, "lazyLoad": "ondemand"}}, {"breakpoint":480,"settings":{"slidesToShow":1, "autoplay":true, "autoplaySpeed": 1000, "arrows": false, "lazyLoad": "ondemand"}}]}'>--}}
                        {{--<div class="insta-img"><img src="/images/images-instagram/1.jpg" alt=""></div>--}}
                        {{--<div class="insta-img"><img src="/images/images-instagram/2.jpg" alt=""></div>--}}
                        {{--<div class="insta-img"><img src="/images/images-instagram/3.jpg" alt=""></div>--}}
                        {{--<div class="insta-img"><img src="/images/images-instagram/4.jpg" alt=""></div>--}}
                        {{--<div class="insta-img"><img src="/images/images-instagram/5.jpg" alt=""></div>--}}
                        {{--<div class="insta-img"><img src="/images/images-instagram/6.jpg" alt=""></div>--}}
                        {{--<div class="insta-img"><img src="/images/images-instagram/3.jpg" alt=""></div>--}}
                        {{--<div class="insta-img"><img src="/images/images-instagram/4.jpg" alt=""></div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
    {{--<section>--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-md-6 col-sm-12 hidden-xs home-page-about-us-text">--}}
                    {{--<span>О нас</span>--}}
                    {{--{!! $settings->about !!}--}}
                {{--</div>--}}
                {{--<div class="col-md-6 col-sm-12 hidden-xs home-page-about-us-text">--}}
                    {{--<p>Почему покупатели выбирают TYFLI.COM</p>--}}
                    {{--<p>Больше не нужно тратить время на посещение обувных магазинов - мы собрали большую коллекцию--}}
                        {{--товаров от популярных производителей.</p>--}}
                    {{--<p>TYFLI.COM предлагает большой выбор модной и качественной продукции по разумным ценам, регулярно--}}
                        {{--проводятся акции и выгодные предложения.</p>--}}
                    {{--<p>В нашем магазине представлены туфли классика для мужчин, которые пользуются популярностью среди--}}
                        {{--покупателей. Линейка наших размеров: 35-40 или 36-41 для женщин и 40-45 для мужчин. Отдельные--}}
                        {{--бренды предлагают мужскую обувь меньших размеров - от 38. </p>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</section>--}}
@endsection