<script type='application/ld+json'>
{
  "@context": "http://www.schema.org",
  "@type": "product",
  "logo": "{{env('APP_URL')}}/images/logo.png",
  @if(!empty($title))
  "name": "{{ $title }}",
  @endif
  {{--@if(!empty($category->image))--}}
  {{--"image": "{{env('APP_URL')}}{{ $category->image->url() }}",--}}
  {{--@endif--}}
  @if(!empty($image))
  "image": "{{env('APP_URL')}}{{ $image }}",
  @endif
  "offers": {
    "@type": "AggregateOffer",
    "offerCount": "{{ $total }}",
    "highPrice": "{{ $category->max_price($category->id) }}",
    "lowPrice": "{{ $category->min_price($category->id) }}",
    "priceCurrency": "UAH"
  }
  @if(isset($shop_reviews))
    @php
      $bestRating = 0;
      $sumRating = 0;
      $reviewCount = 0;
      foreach($shop_reviews as $review){
          if($review->grade > $bestRating){
              $bestRating = $review->grade;
          }
          $sumRating += $review->grade;
          $reviewCount++;
      }
    @endphp
    @if($reviewCount > 0)
      ,
      "review": [
      @foreach($shop_reviews as $i => $review)
        {
          "@type": "Review",
          "author": "{{ $review->author }}",
          "datePublished": "{{ $review->date() }}",
          "description": "{{ $review->review }}",
          "reviewRating": {
            "@type": "Rating",
            "bestRating": "5",
            "ratingValue": "{{ $review->grade }}",
            "worstRating": "1"
          }
        }
        @if($i + 1 < count($shop_reviews))
          ,
    @endif
      @endforeach
      ],
        "aggregateRating": {
          "@type": "aggregateRating",
          "ratingValue": "{{ round($sumRating/$reviewCount, 2) }}",
      "bestRating": "{{ $bestRating }}",
      "reviewCount": "{{ $reviewCount }}"
    }
    @endif
  @endif
}
</script>