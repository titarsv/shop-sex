@extends('public.layouts.main', ['pagination' => $products, 'root_category' => $category->get_root_category()])
@section('meta')
    <title>
        @if(empty($category->meta_title)))
            {!! $category->name !!}} | {{ trans('app.sex_shop_in_kharkov_online_store') }} | Shop-sex.com.ua
        @else
            {!! $category->meta_title !!} | {{ trans('app.sex_shop_in_kharkov_online_store') }} | Shop-sex.com.ua
        @endif
        @if(!empty($products) && $products->currentPage() > 1) - Страница №{!! $products->currentPage() !!}@endif
    </title>

    <meta name="description" content="{{ $category->name }} {{ trans('app.at_the_best_prices_in_kharkov_ukraine_anonymity_guarantee') }}{{ (!empty($products) && $products->currentPage() > 1) ? ' - Страница №'.$products->currentPage() : '' }}">
    @if(empty($products) || $products->currentPage() == 1)
        <meta name="keywords" content="{{ $category->meta_keywords or '' }}">
    @endif

    @if(!empty($category->canonical) && empty($_GET['page']))
        <meta name="canonical" content="{!! $category->canonical !!}">
    @endif
    @if(!empty($category->robots))
        <meta name="robots" content="{!! $category->robots !!}">
    @endif
    @if(!empty($products) && $products->currentPage() > 1)
        <link rel="prev" href="{!! $cp->url($products->url($products->currentPage() - 1), $products->currentPage() - 1) !!}">
    @endif
    @if(!empty($products) && $products->currentPage() < $products->lastPage())
        <link rel="next" href="{!! $cp->url($products->url($products->currentPage() + 1), $products->currentPage() + 1) !!}">
    @endif
@endsection
@section('page_vars')
    @include('public.layouts.microdata.open_graph', [
     'title' => $category->name,
     'description' => null,
     'image' => !empty($category->image) ? $category->image->url() : '/images/no_image.jpg',
     'type' => 'catalog'
     ])
@endsection

@section('content')
    <section>
        <div class="container">
            <div class="row">
                {!! Breadcrumbs::render('categories', $category) !!}
                <div class="visible-xs-block col-xs-12">
                    <div class="mobile-product-title">
                        <p>Каталог</p>
                        <p class="mobile-filters-toggle">{{ trans('app.search_settings') }}</p>
                        <form class="filters">
                            <div class="close-btn"></div>
                            <p class="filters__item-title">{{ trans('app.search_settings') }}</p>
                            <div class="filters__price">
                                <p class="filters__item-title">{{ trans('app.price') }}</p>
                                <div class="tab_content first tab_price active">
                                    <fieldset>
                                        <div class="price-range" data-value="{{ isset($price[2]) ? $price[2] : $price[0] }};{{ isset($price[3]) ? $price[3] : $price[1] }}" data-max="{{ $price[1] }}" data-min="{{ $price[0] }}"></div>
                                        <div class="price-inputs">
                                            <div class="price-inputs__inner">
                                                <input type="text" name="price_min" class="sliderValue val1" data-index="0" value="{{ isset($price[2]) ? $price[2] : $price[0] }}" />
                                                <span> - </span>
                                                <input type="text" name="price_max" class="sliderValue val2" data-index="1" value="{{ isset($price[3]) ? $price[3] : $price[1] }}" />
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div id="filters">
                                @if(!empty($attributes))
                                    @foreach($attributes as $key => $attribute)
                                        <div class="filters__item">
                                            <p class="filters__item-title">{{ $attribute['name'] }}</p>
                                            @php
                                                $attr_values = $attribute['values'];
                                            @endphp
                                            @foreach($attr_values as $i => $attribute_value)
                                                @if(!empty($attribute_value['name']))
                                                    <input type="checkbox"
                                                           name="filter_attributes[{!! $key !!}][value][{!! $i !!}]"
                                                           data-attribute="{{ $key }}"
                                                           data-value="{{ $i }}"
                                                           data-url="/catalog{{ $attribute_value['url'] }}"
                                                           id="product-filters-{!! $key !!}__check-{!! $i !!}"
                                                           class="radio"
                                                           @if($attribute_value['checked'])
                                                           checked
                                                            @endif>
                                                    <label class="radio-label" for="product-filters-{!! $key !!}__check-{!! $i !!}">{!! $attribute_value['name'] !!}</label>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit">{{ trans('app.apply') }}</button>
                        </form>
                    </div>
                </div>
                <div class="col-sm-3 hidden-xs">
                    <form class="filters">
                        <div class="filters__price">
                            <p class="filters__item-title">{{ trans('app.price') }}</p>
                            <p class="filters__item-title">{{ trans('app.search_settings') }}</p>
                            <div class="tab_content first tab_price active">
                                <fieldset>
                                    <div class="price-range" data-value="{{ isset($price[2]) ? $price[2] : $price[0] }};{{ isset($price[3]) ? $price[3] : $price[1] }}" data-max="{{ $price[1] }}" data-min="{{ $price[0] }}"></div>
                                    <div class="price-inputs">
                                        <div class="price-inputs__inner">
                                            <input type="text" name="price_min" class="sliderValue val1" data-index="0" value="{{ isset($price[2]) ? $price[2] : $price[0] }}" />
                                            <span> - </span>
                                            <input type="text" name="price_max" class="sliderValue val2" data-index="1" value="{{ isset($price[3]) ? $price[3] : $price[1] }}" />
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div id="filters-min">
                            <div class="filters__item">
                                <p class="filters__item-title">{{ trans('app.categories') }}</p>
                                @foreach($categories as $cat)
                                    @php
                                        $children = $cat->children;
                                    @endphp
                                    @if($children->count())
                                        @if($category->id == $cat->id || $category->parent_id == $cat->id)
                                            <a class="root_cat active" href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/{{ $cat->url_alias }}">{{ $cat->name }}<span></span></a>
                                        @else
                                            <a class="root_cat" href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/{{ $cat->url_alias }}">{{ $cat->name }}<span></span></a>
                                        @endif
                                        <ul class="subcats">
                                            @foreach($children as $cat)
                                                <li><a class="root_cat" href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/{{ $cat->url_alias }}">{{ $cat->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <a class="root_cat" href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/{{ $cat->url_alias }}">{{ $cat->name }}</a>
                                    @endif
                                @endforeach
                            </div>
                        @if(!empty($attributes))
                            @foreach($attributes as $key => $attribute)
                                <div class="filters__item">
                                    <p class="filters__item-title">{{ $attribute['name'] }}</p>
                                    @php
                                        $attr_values = $attribute['values'];
                                    @endphp
                                    @foreach($attr_values as $i => $attribute_value)
                                        @if(!empty($attribute_value['name']))
                                            <input type="checkbox"
                                                   name="filter_attributes[{!! $key !!}][value][{!! $i !!}]"
                                                   data-attribute="{{ $key }}"
                                                   data-value="{{ $i }}"
                                                   data-url="/catalog{{ $attribute_value['url'] }}"
                                                   id="product-filter-{!! $key !!}__check-{!! $i !!}"
                                                   class="radio"
                                                   @if($attribute_value['checked'])
                                                   checked
                                                   @endif>
                                            <label class="radio-label" for="product-filter-{!! $key !!}__check-{!! $i !!}">{!! $attribute_value['name'] !!}</label>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                        </div>
                    </form>
                </div>
                <div class="col-sm-8 col-xs-12">
                    <div class="row">
                        <div class="mob-filters">
                            <select name="sorting" class="sumo-select sorting sorting-select" id="sorting-select-mob">
                                <option selected disabled>{{ trans('app.sort') }}</option>
                                <option value="id-desc"{{ isset($_GET['order']) && $_GET['order'] == 'id-desc' ? ' selected="selected"' : '' }}>{{ trans('app.new_arrivals') }}</option>
                                <option value="price-asc"{{ isset($_GET['order']) && $_GET['order'] == 'price-asc' ? ' selected="selected"' : '' }}>{{ trans('app.by_ascending_price') }}</option>
                                <option value="price-desc"{{ isset($_GET['order']) && $_GET['order'] == 'price-desc' ? ' selected="selected"' : '' }}>{{ trans('app.by_descending_prices') }}</option>
                            </select>
                            <span class="mobile-filters-toggle">{{ trans('app.filter') }}</span>
                        </div>
                        <div class="col-sm-12 hidden-xs">
                            <div class="sort-prod">
                                {{ trans('app.sort_by') }}
                                <select name="sorting" class="chosen-select sorting sorting-select" id="sorting-select">
                                    <option value="id-desc"{{ isset($_GET['order']) && $_GET['order'] == 'id-desc' ? ' selected="selected"' : '' }}>{{ trans('app.new_arrivals') }}</option>
                                    <option value="price-asc"{{ isset($_GET['order']) && $_GET['order'] == 'price-asc' ? ' selected="selected"' : '' }}>{{ trans('app.ascending_price') }}</option>
                                    <option value="price-desc"{{ isset($_GET['order']) && $_GET['order'] == 'price-desc' ? ' selected="selected"' : '' }}>{{ trans('app.descending_prices') }}</option>
                                </select>
                            </div>
                        </div>

                        @if(empty($products))
                            <div class="col-md-12">
                                <span>{{ trans('app.there_are_no_such_items') }}</span>
                            </div>
                        @else
                            <div class="products-container">
                                @foreach($products as $key => $product)
                                    {{--<div class="col-xl-2 col-sm-4 col-xs-6">--}}
                                    <div class="product-item">
                                        @include('public.layouts.product', ['product' => $product])
                                    </div>
                                    {{--</div>--}}
                                @endforeach
                            </div>
                        @endif
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
                                <p class="question-popup__container-title">{{ trans('app.added_to_your_cart') }} </p>
                                <p class="product-name">{{ $product->name }}</p>
                                <img class="question-popup__container-img" src="{{ $product->image == null ? '/uploads/no_image.jpg' : $product->image->url('product_list') }}" alt="{{ $product->name }}">
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
    </section>
    @if(!empty($products))
        @include('public.layouts.pagination', ['paginator' => $products])
    @endif

    @if($products->currentPage() < 2)
    <div class="category-description">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    {!! $category->description !!}
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection