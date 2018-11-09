@extends('public.layouts.main', ['pagination' => $products, 'root_category' => $category->get_root_category()])
@section('meta')
    <title>
        @if(empty($category->meta_title)))
            {!! $category->name !!}}
        @else
            {!! $category->meta_title !!}
        @endif
        @if(!empty($products) && $products->currentPage() > 1) - Страница {!! $products->currentPage() !!}@endif
    </title>

    @if(empty($products) || $products->currentPage() == 1)
        <meta name="description" content="{!! $category->meta_description or '' !!}">
        <meta name="keywords" content="{!! $category->meta_keywords or '' !!}">
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

@section('content')
    <section>
        <div class="container">
            <div class="row">
                {!! Breadcrumbs::render('categories', $category) !!}
                <div class="visible-xs-block col-xs-12">
                    <div class="mobile-product-title">
                        <p>Каталог</p>
                        <p class="mobile-filters-toggle">Настройки поиска</p>
                        <form class="filters">
                            <div class="close-btn"></div>
                            <p class="filters__item-title">Настройки поиска</p>
                            <div class="filters__price">
                                <p class="filters__item-title">Цена</p>
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
                            <button type="submit">Применить</button>
                        </form>
                    </div>
                </div>
                <div class="col-sm-3 hidden-xs">
                    <form class="filters">
                        <div class="filters__price">
                            <p class="filters__item-title">Цена</p>
                            <p class="filters__item-title">Настройки поиска</p>
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
                                <p class="filters__item-title">Категории</p>
                                @foreach($categories as $cat)
                                    @php
                                        $children = $cat->children;
                                    @endphp
                                    @if($children->count())
                                        @if($category->id == $cat->id || $category->parent_id == $cat->id)
                                            <a class="root_cat active" href="/catalog/{{ $cat->url_alias }}">{{ $cat->name }}<span></span></a>
                                        @else
                                            <a class="root_cat" href="/catalog/{{ $cat->url_alias }}">{{ $cat->name }}<span></span></a>
                                        @endif
                                        <ul class="subcats">
                                            @foreach($children as $cat)
                                                <li><a class="root_cat" href="/catalog/{{ $cat->url_alias }}">{{ $cat->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <a class="root_cat" href="/catalog/{{ $cat->url_alias }}">{{ $cat->name }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div id="filters-min">
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
                        <div class="col-sm-12 hidden-xs">
                            <div class="sort-prod">
                                Сортировать по:
                                <select name="sorting" class="chosen-select sorting" id="sorting-select">
                                    <option value="price-asc"{{ isset($_GET['order']) && $_GET['order'] == 'price-asc' ? ' selected="selected"' : '' }}>Возрастанию цены</option>
                                    <option value="price-desc"{{ isset($_GET['order']) && $_GET['order'] == 'price-desc' ? ' selected="selected"' : '' }}>Убыванию цены</option>
                                </select>
                            </div>
                        </div>

                        @if(empty($products))
                            <div class="col-md-12">
                                <span>Нет таких товаров...</span>
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
        <div class="mfp-hide">
        <div id="cart-popup" class="view-popup">
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
    </div>
    </section>
    @if(!empty($products))
        @include('public.layouts.pagination', ['paginator' => $products])
    @endif
@endsection