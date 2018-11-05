@extends('public.layouts.main')
@section('meta')
    <title>Новости</title>
    <meta name="description" content="{!! $settings->meta_description !!}">
    <meta name="keywords" content="{!! $settings->meta_keywords !!}">
@endsection

@section('content')
    <div class="container hidden-xs">
        <div class="row">
            <div class="col-md-12">
                <div class="site-path">
                    <a href="{{env('APP_URL')}}" class="site-path-link" itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb" itemprop="url">Главная</a>
                    <a href="javascript:void(0);" class="site-path-link-active">{{ $title }}</a>
                </div>
            </div>
        </div>
    </div>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-4">
                    <div class="row">
                        <div class="col-md-12 hidden-xs">
                            <div class="information-accordion-wrp path-underline">
                                <div class="information-accordion">
                                    <div class="aside-filter-menu-item-title">
                                        <p>Новости и акции</p>
                                    </div>
                                    <div class="aside-filter-menu-item-btn-toggle filters-open">
                                        <div></div>
                                        <div></div>
                                    </div>
                                </div>
                                <div class="information-accordion-links unactive">
                                    {{--<h5>Повседневная обувь</h5>--}}
                                    @foreach($news as $item)
                                        <a href="{{env('APP_URL')}}/news/{!!$item->url_alias !!}"><p>{{ $item->title }}</p> </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="information-accordion-wrp path-underline">
                                <div class="information-accordion">
                                    <div class="aside-filter-menu-item-title">
                                        <p>Статьи</p>
                                    </div>
                                    <div class="aside-filter-menu-item-btn-toggle filters-open">
                                        <div></div>
                                        <div></div>
                                    </div>
                                </div>
                                <div class="information-accordion-links unactive">
                                    {{--<h5>Повседневная обувь</h5>--}}
                                    @foreach($articles as $item)
                                        <a href="{{env('APP_URL')}}/articles/{!!$item->url_alias !!}"><p>{{ $item->title }}</p> </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="information-accordion-wrp path-underline">
                                <div class="information-accordion">
                                    <div class="aside-filter-menu-item-title">
                                        <p>Уход за обувью</p>
                                    </div>
                                    <div class="aside-filter-menu-item-btn-toggle filters-open">
                                        <div></div>
                                        <div></div>
                                    </div>
                                </div>
                                <div class="information-accordion-links unactive">
                                    {{--<h5>Повседневная обувь</h5>--}}
                                    @foreach($handling as $item)
                                        <a href="{{env('APP_URL')}}/handling/{!!$item->url_alias !!}"><p>{{ $item->title }}</p> </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="aside-filter-menu-item path-underline">
                                <div class="aside-filter-menu-item-title">
                                    <a href="{{env('APP_URL')}}/page/voprosy--otvety"><p>Вопросы и ответы</p></a>
                                </div>
                            </div>
                            <div class="aside-filter-menu-item path-underline">
                                <div class="aside-filter-menu-item-title">
                                    <a href="{{env('APP_URL')}}/page/bonusnyya-programma"><p>Бонусная программа</p></a>
                                </div>
                            </div>
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title">
                                    <a href="{{env('APP_URL')}}/page/otkrytye-vakansii"><p>Открытые вакансии</p></a>
                                </div>
                            </div>
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title">
                                    <a href="{{env('APP_URL')}}/page/drop-shipping"><p>Drop shipping</p></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9 col-sm-8">
                    <div class="row">
                        <div class="col-md-12 hidden-xs margin">
                            <h1 class="title">{{ $title }}</h1>
                        </div>
                        <div class="visible-xs-inline-block col-xs-12">
                            <div>
                                <select name="" id="redirect_select"  class="chosen-select site-section-select">
                                    <option value="{{env('APP_URL')}}/news"{{ $title == 'Новости и Акции' ? ' selected="selected"' : '' }}>Новости и Акции</option>
                                    <option value="{{env('APP_URL')}}/articles"{{ $title == 'Статьи' ? ' selected="selected"' : '' }}>Статьи</option>
                                    <option value="{{env('APP_URL')}}/page/voprosy--otvety">Вопросы и ответы</option>
                                    <option value="{{env('APP_URL')}}/handling"{{ $title == 'Уход за обувью' ? ' selected="selected"' : '' }}>Уход за обувью</option>
                                    <option value="{{env('APP_URL')}}/page/bonusnyya-programma">Бонусная программа</option>
                                    <option value="{{env('APP_URL')}}/page/otkrytye-vakansii">Открытые вакансии</option>
                                    <option value="{{env('APP_URL')}}/page/drop-shipping">Drop Shipping</option>
                                </select>
                            </div>
                        </div>
                        @if($items->count() > 1)
                            @foreach($items as $i => $article)
                                <div class="col-sm-6 col-xs-12">
                                    <div class="article-slider-item">
                                        <div class="article-slider-item-img">
                                            <a href="{{env('APP_URL')}}/{{ $slug }}/{!!$article->url_alias !!}">
                                                <img src="{!! $article->image->url('blog_list') !!}" alt="">
                                            </a>
                                        </div>
                                        <h5 class="article-slider-item-title">
                                            <a href="{{env('APP_URL')}}/{{ $slug }}/{!!$article->url_alias !!}">{!! $article->title !!}</a>
                                        </h5>
                                        <span class="article-slider-item-data">
                                            {!! $article->created_at !!}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        @include('public.layouts.pagination', ['paginator' => $articles])
                        <div class="visible-xs-inline-block col-xs-12">
                            <p class=" sections-links-title">Разделы</p>
                            <ul class="sections-links">
                                <li><a href="{{env('APP_URL')}}/news">Новости и Акции</a> </li>
                                <li><a href="{{env('APP_URL')}}/articles">Статьи</a> </li>
                                <li><a href="{{env('APP_URL')}}/page/voprosy--otvety">Вопросы и ответы</a> </li>
                                <li><a href="{{env('APP_URL')}}/handling">Уход за обувью</a> </li>
                                <li><a href="{{env('APP_URL')}}/page/bonusnyya-programma">Бонусная программа</a> </li>
                                <li><a href="{{env('APP_URL')}}/page/otkrytye-vakansii">Открытые вакансии</a> </li>
                                <li><a href="{{env('APP_URL')}}/page/drop-shipping">Drop Shipping</a> </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection