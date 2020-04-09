@if(!empty($webp))
<picture>
    @if(empty($lazy) || $lazy != 'slider')
    <source
    @if($lazy == 'slider')
        data-lazy="/uploads/{{ $webp }}"
    @elseif($lazy == 'static')
        data-src="/uploads/{{ $webp }}" srcset="/images/pixel.webp"
    @else
        srcset="/uploads/{{ $webp }}"
    @endif
    type="image/webp">
    @endif
    @if(empty($lazy) || $lazy != 'slider')
    <source
    @if($lazy == 'slider')
        data-lazy="/uploads/{{ $original }}"
    @elseif($lazy == 'static')
        data-src="/uploads/{{ $original }}" srcset="/images/pixel.{{ $original_mime }}"
    @else
        srcset="/uploads/{{ $original }}"
    @endif
    type="image/{{ $original_mime }}">
    @endif
    <img
    @if($lazy == 'slider')
        data-lazy="/uploads/{{ $original }}"
        src="/images/pixel.jpg"
    @elseif($lazy == 'static')
        src="/images/pixel.jpg"
    @else
        src="/uploads/{{ $original }}"
    @endif
    @foreach($attributes as $key => $attr) {{ $key }}="{{ $attr }}"@endforeach>
</picture>
@else
@if(!empty($original))
<picture>
    <img src="/uploads/{{ $original }}" @foreach($attributes as $key => $attr){{ $key }}="{{ $attr }}"@endforeach>
</picture>
@else
<picture>
    <img src="/uploads/no_image.jpg" @foreach($attributes as $key => $attr){{ $key }}="{{ $attr }}"@endforeach>
</picture>
@endif
@endif