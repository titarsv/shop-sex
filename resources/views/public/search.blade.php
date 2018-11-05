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
                <h1>Поиск: {{ $search_text }}</h1>
                <div class="row">
                    <div class="col-sm-12 products-grid-container">
						@forelse($products as $product)
							<div class="col-lg-4 col-xs-6">
								<div class="grid-product-card card-margin">
									@include('public.layouts.product', ['product' => $product, 'slide' => false])
								</div>
							</div>
						@empty
							<article class="order">
								<h5 class="order__title">В этой категории пока нет товаров!</h5>
							</article>
						@endforelse
                    </div>

                    <div class="col-sm-12">
                        {{--{!! $products->appends(['text' => $search_text])->render() !!}--}}
                        @include('public.layouts.pagination', ['paginator' => $paginator])
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection