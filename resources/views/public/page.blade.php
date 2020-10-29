@extends('public.layouts.main')
@section('meta')
    <title>{!! $content->meta_title !!}</title>
    <meta name="description" content="{!! $content->meta_description !!}">
    <meta name="keywords" content="{!! $content->meta_keywords !!}">
    @if(!empty($content->robots))
        <meta name="robots" content="{!! $content->robots !!}">
    @endif

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsArticle",
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{env('APP_URL')}}/{{ Request::path() }}"
      },
      "headline": "{{ $content->meta_title }}",
      "image": [
        "{{env('APP_URL')}}/images/forum-bg.jpg"
       ],
      "datePublished": "{{ $content->created_at->format('m/d/Y') }}",
      "dateModified": "{{ $content->updated_at->format('m/d/Y') }}",
      "author": {
        "@type": "Person",
        "name": "ventelator"
      },
       "publisher": {
        "@type": "Organization",
        "name": "{{ trans('app.internet_store_intim') }}",
        "logo": {
          "@type": "ImageObject",
          "url": "https://shop-sex.com.ua/images/logo.png"
        }
      }
    }
    </script>
@endsection
@section('page_vars')
    @if($content->url_alias == 'kontakty')
        @include('public.layouts.microdata.local_business')
    @endif
    @include('public.layouts.microdata.open_graph', [
     'title' => $content->meta_title,
     'description' => null,
     'image' => '/images/forum-bg.jpg',
     'type' => 'NewsArticle'
     ])
@endsection

@section('content')
    <section>
        <div class="container">
            <div class="row">
                {!! Breadcrumbs::render('html', $content) !!}
            </div>
        </div>
    </section>
    {!! html_entity_decode($content->content) !!}
@endsection