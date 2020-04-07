@if ($breadcrumbs)
    @include('public.layouts.microdata.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    <ul class="col-sm-12 col-xs-12 breadcrumbs">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$breadcrumb->last)
                <li><a href="{{ $breadcrumb->url }}" class="site-path-link">{{ $breadcrumb->title }} -</a></li>
            @else
                <li><a href="javascript:void(0);" class="site-path-link-active">{{ $breadcrumb->title }}</a></li>
            @endif
        @endforeach
    </ul>
@endif
