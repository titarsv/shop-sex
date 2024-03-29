@extends('public.layouts.main')
@section('meta')
    <title>{{ trans('app.thanks_for_your_order') }}</title>
    @if(!empty($ecommerce))
    <script>
        window.dataLayer = window.dataLayer || [];
        {{--dataLayer.push({--}}
            {{--transactionId: {{ $order->id }},--}}
            {{--transactionTotal: {{ $order->total_price }},--}}
            {{--transactionShipping: 0,--}}
            {{--transactionProducts: [--}}
                {{--@foreach($order->getProducts() as $i => $product)--}}
                {{--{{$i > 0 ? ',' : ''}}{--}}
                {{--id: {{ $product['product']->id }},--}}
                {{--name: "{{ $product['product']->name }}",--}}
                {{--sku: "{{ $product['product']->articul }}",--}}
                {{--category: "{{ $product['product']->category()->name }}",--}}
                {{--price: {{ $product['price'] }},--}}
                {{--quantity: {{ $product['quantity'] }}--}}
                {{--}--}}
                {{--@endforeach--}}
            {{--]--}}
        {{--});--}}

        gtag("event", "purchase", {
            transaction_id: {{ $order->id }},
            value: {{ $order->total_price }},
            items: [
                @foreach($order->getProducts() as $i => $product)
                {{$i > 0 ? ',' : ''}}{
                id: {{ $product['product']->id }},
                name: "{{ $product['product']->name }}",
                sku: "{{ $product['product']->articul }}",
                category: "{{ $product['product']->category()->name }}",
                price: {{ $product['price'] }},
                quantity: {{ $product['quantity'] }}
                }
                @endforeach
            ],
            currency: "UAH",
            send_to: "G-Y9W5S3LTY4"
        })
    </script>
    @endif
@endsection
@section('content')
    <main id="main-container">
        <div>
            <div class="container">
                <div class="row">
                    <ul class="col-sm-12 col-xs-12 breadcrumbs">
                        <li itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}" class="site-path-link" itemprop="url">{{ trans('app.home') }} -</a></li>
                        <li><a href="javascript:void(0);" class="site-path-link-active">{{ trans('app.checkout') }}</a></li>
                    </ul>
                </div>
                <div class="col-xs-12">
                    <div style="text-align: center; margin-bottom: 40px;">
                        <div class="container succes__container">
                            <h1 class="succes__container-title">{{ trans('app.thanks') }}</h1>
                            <div class="succes__card">
                                <span class="succes__descr">{{ trans('app.tour_order_has_been_successfully_completed') }}</span>
                            </div>
                            <span class="succes__wait-call">{{ trans('app.expect_a_call_from_our_manager') }}</span>
                            <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}" class="main-btn main-btn_accent succes__btn">{{ trans('app.continue_shopping') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    @if($bestsellers && count($bestsellers) > 0)
                        <div class="section-title"><span>{{ trans('app.bestsellers') }}</span></div>
                        <div class="slick-slider slick-prod" data-slick='{"slidesToShow": 6, "dots": false, "arrows": false, "responsive":[{"breakpoint":1199,"settings":{"slidesToShow": 4, "centerMode": true}}, {"breakpoint":991,"settings":{"slidesToShow": 3, "centerMode": true}}, {"breakpoint":768,"settings":{"slidesToShow": 2, "centerMode": true}}, {"breakpoint":480,"settings":{"slidesToShow": 1, "centerMode": true}}]}'>
                            @foreach($bestsellers as $bestseller)
                                <div class="col-md-2 col-sm-4 col-xs-4">
                                    <div class="product-item top">
                                        @include('public.layouts.product', ['product' => $bestseller])
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection