@extends('public.layouts.main')
@section('meta')
    <title>Поиск: {{ $search_text }}</title>
    <meta name="description" content="Поиск по запросу: {{ $search_text }}">
    <meta name="keywords" content="{{ $search_text }}">
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('search') !!}
@endsection

@section('content')
    <main class="main-wrapper">
        <section class="siteSection">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <h1 style="padding: 25px 25px 20px;">Поиск: {{ $search_text }}</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 products-grid-container">
                        <div class="products-container">
						@forelse($products as $product)
                            <div class="product-item">
                                @include('public.layouts.product', ['product' => $product])
                            </div>
						@empty
							<article class="order">
								<h5 class="order__title">В этой категории пока нет товаров!</h5>
							</article>
						@endforelse
                        </div>
                    </div>
                </div>
            </div>
            <div class="hidden mfp-hide">
                @foreach($products as $key => $product)
                    <div id="cart-popup_{{ $product->id }}" class="view-popup">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2 col-sm-12 col-sm-offset-0 col-xs-12">
                                    <div class="question-popup__container">
                                        <p class="question-popup__container-title">К Вам в корзину добавлен: </p>
                                        <p class="product-name" itemprop="name">{{ $product->name }}</p>
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
        </section>
        @if(!empty($products))
            @include('public.layouts.pagination', ['paginator' => $products])
        @endif
    </main>
@endsection