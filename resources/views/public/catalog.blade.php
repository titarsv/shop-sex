@extends('public.layouts.main')
@section('meta')
    <title>{!! $settings->meta_title !!}</title>
    <meta name="description" content="{!! $settings->meta_description !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords !!}">
@endsection

@section('content')
    <section>
        <div class="container-fluid">
            <p class="section-title">Каталог товаров</p>
            <div class="row">
                @foreach($categories as $category)
                    @if(!empty($category->image))
                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <div class="category-item">
                                <a href="{{env('APP_URL')}}/catalog/{{ $category->url_alias }}" class="category-item__img">
                                    <img src="{{ $category->image->url() }}" alt="{{ $category->name }}">
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
@endsection