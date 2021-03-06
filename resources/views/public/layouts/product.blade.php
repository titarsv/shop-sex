<a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/product/{{ $product->url_alias }}">
{{--<img src="{{ $product->image == null ? '/uploads/no_image.jpg' : $product->image->url('product_list') }}" alt="{{ $product->name }}">--}}
@if($product->image == null)
    <picture>
        <source data-src="/uploads/no_image.webp" srcset="/images/pixel.webp" type="image/webp">
        <source data-src="/uploads/no_image.jpg" srcset="/images/pixel.jpg" type="image/jpeg">
        <img src="/images/pixel.jpg" alt="{{ $product->name }}">
    </picture>
@else
{!! $product->image->webp_image('product_list', ['alt' => $product->name], !empty($lazy) ? $lazy : 'static') !!}
@endif
</a>
<div class="product-item__info">
    <p class="product-item__info-title">{{ $product->name }}</p>
    <p class="product-item__info-price">{{ number_format($product->price, 2, '.', ' ') }}{{ trans('app.hryvnias') }}</p>
    <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/product/{{ $product->url_alias }}" class="product-item__info-link">{{ trans('app.more_details') }}</a>
    <button class="product-item__info-btn btn_buy popup-btn"  data-mfp-src="#cart-popup_{{ $product->id }}" data-prod-id="{{ $product->id }}">{{ trans('app.in_garbage') }}</button>
</div>