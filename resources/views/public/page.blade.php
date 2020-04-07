@extends('public.layouts.main')
@section('meta')
    <title>{!! $content->meta_title !!}</title>
    <meta name="description" content="{!! $content->meta_description !!}">
    <meta name="keywords" content="{!! $content->meta_keywords !!}">
    @if(!empty($content->robots))
        <meta name="robots" content="{!! $content->robots !!}">
    @endif
@endsection
@section('page_vars')
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