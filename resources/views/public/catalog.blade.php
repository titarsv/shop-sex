@extends('public.layouts.main')
@section('meta')
    <title>{!! $settings->meta_title_ru !!}</title>
    <meta name="description" content="{!! $settings->meta_description_ru !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords_ru !!}">
@endsection

@section('content')
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
@endsection