@extends('public.layouts.main')

@section('breadcrumbs')
    {!! Breadcrumbs::render('history') !!}
@endsection

@section('content')
    <main>
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-4 hidden-xs aside-filter-menu-container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="{{env('APP_URL')}}/user/history"><p>История покупок</p></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="javascript:void(0);" class="active-aside-link"><p>Список желаний</p></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="aside-filter-menu-item">
                                <div class="aside-filter-menu-item-title aside-block">
                                    <a href="{{env('APP_URL')}}/user"><p>Личный кабинет</p></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9 col-sm-8 col-xs-12 products-grid-container">
                    <div class="row">
                        <div class="col-md-12 hidden-xs margin">
                            <h1 class="title">Список желаний</h1>
                        </div>
                        <div class="visible-xs-inline-block col-xs-12">
                            <div>
                                <select name="site-section-select" id="redirect_select" class="chosen-select site-section-select">
                                    <option value="{{env('APP_URL')}}/user/history">История покупок</option>
                                    <option selected="selected" value="">Список желаний</option>
                                    <option value="{{env('APP_URL')}}/user">Личный кабинет</option>
                                </select>
                            </div>
                        </div>
                        @forelse($products as $key => $product)
                            <div class="col-lg-4 col-xs-6">
                                @include('public.layouts.product', ['product' => $product->product, 'slide' => false])
                            </div>
                            @if(($key+1)%3 == 0 && ceil(count($products)/6) == ($key+1)/3)
                                @include('public.layouts.banner')
                            @endif
                        @empty
                            <div class="col-md-12 margin">
                                <span>Нет избранных товаров...</span>
                            </div>
                        @endforelse

                        @if($products->count() < 3)
                            @include('public.layouts.banner')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection