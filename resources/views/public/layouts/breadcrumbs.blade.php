@if ($breadcrumbs)
    <ul class="col-sm-12 col-xs-12 breadcrumbs">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$breadcrumb->last)
                <li><a href="{{ $breadcrumb->url }}" class="site-path-link" itemscope itemtype="http://data-vocabulary.org/Breadcrumb" itemprop="url">{{ $breadcrumb->title }} -</a></li>
            @else
                <li><a href="javascript:void(0);" class="site-path-link-active">{{ $breadcrumb->title }}</a></li>
            @endif
        @endforeach
    </ul>
@endif
