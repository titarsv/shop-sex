<a href="{{env('APP_URL')}}/product/{{ $product->url_alias }}">
<img src="{{ $product->image == null ? '/uploads/no_image.jpg' : $product->image->url('product_list') }}" alt="{{ $product->name }}">
</a>
<div class="product-item__info">
    <p class="product-item__info-title">{{ $product->name }}</p>
    <p class="product-item__info-price">{{ number_format($product->price, 2, '.', ' ') }}грн</p>
    <a href="{{env('APP_URL')}}/product/{{ $product->url_alias }}" class="product-item__info-link">подробнее</a>
    <button class="product-item__info-btn btn_buy" data-prod-id="{{ $product->id }}">В корзину</button>
</div>