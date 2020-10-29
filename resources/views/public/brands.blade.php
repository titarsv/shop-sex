@extends('public.layouts.main')
@section('meta')
    <title>{{ trans('app.news') }}</title>
    <meta name="description" content="{!! $settings->meta_description !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords !!}">
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('brand') !!}
@endsection

@section('content')
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-3 no-padding hidden-xs">
                    <div class="path-underline"></div>
                    <div class="brand-link-wrp">
                        <a href="javascript:void(0);" class="brand-link">Drop Shipping</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12 margin">
                            <h1 class="title">{{ trans('app.brands') }}</h1>
                        </div>
                        @foreach($brands as $brand)
                            <a href="{{env('APP_URL')}}/catalog/tovary/brend-{{ $brand->value }}">
                                <div class="col-md-4 col-xs-6 brand-item">{{ $brand->name }}</div>
                            </a>
                        @endforeach
                        @include('public.layouts.banner')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection