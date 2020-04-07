<script type='application/ld+json'>
{
  "@context": "http://www.schema.org",
  "@type": "product",
  @if(!empty($brand = $product->get_attribute('brand')))
  "brand": "{{ $brand }}",
  @endif
  "logo": "{{env('APP_URL')}}/images/logo.png",
  "name": "{{ $product->name }}",
  "sku": "{{ $product->sku }}",
  @if(!empty($category = $product->main_category()))
  "category": "{{ $category->name }}",
  @endif
  @if(!empty($product->image))
  "image": "{{env('APP_URL')}}{{ $product->image->url() }}",
  @endif
  "description": "{{ empty($product->description) ? $product->name : strip_tags($product->description) }}",
  "offers": {
    "@type": "Offer",
    "priceCurrency": "UAH",
    "price": "{{ $product->price }}",
    "priceValidUntil": "{{ date('Y-m-d', time() + 86400 * 30) }}",
    "itemCondition": "http://schema.org/UsedCondition",
    "availability": "http://schema.org/InStock",
    "url": "{{ env('APP_URL')}}/product/{{ $product->url_alias }}",
    "seller": {
      "@type": "Organization",
      "name": "КамТех"
    }
  }
  @if(isset($reviews))
  @php
        $bestRating = 0;
        $sumRating = 0;
        $reviewCount = 0;
        foreach($reviews as $review){
            if($review['parent']->grade > $bestRating){
                $bestRating = $review['parent']->grade;
            }
            $sumRating += $review['parent']->grade;
            $reviewCount++;
        }
  @endphp
  @if($reviewCount > 0)
,
  "aggregateRating": {
    "@type": "aggregateRating",
    "worstRating": "1",
    "ratingValue": "{{ round($sumRating/$reviewCount, 2) }}",
      "bestRating": "{{ $bestRating }}",
      "reviewCount": "{{ $reviewCount }}"
  }
  @else
,
  "aggregateRating": {
    "@type": "aggregateRating",
    "worstRating": "1",
    "ratingValue": "5",
      "bestRating": "5",
      "reviewCount": "1"
  }
  @endif
@endif
}
</script>