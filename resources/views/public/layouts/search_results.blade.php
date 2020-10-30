@foreach($products as $product)
    <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/product/{{ $product->url_alias }}">
        @if($product->image == null)
            <picture>
                <source srcset="/uploads/no_image.webp" type="image/webp">
                <source srcset="/uploads/no_image.jpg" type="image/jpeg">
                <img src="/uploads/no_image.jpg" alt="{{ $product->name }}">
            </picture>
        @else
            {!! $product->image->webp_image('product_list', ['alt' => $product->name]) !!}
        @endif
        <div>
            <span class="search-name">{{ $product->name }}</span>
            <span class="search-price">{{ number_format($product->price, 2, '.', ' ') }} {{ trans('app.hryvnias') }}</span>
        </div>
    </a>
@endforeach
