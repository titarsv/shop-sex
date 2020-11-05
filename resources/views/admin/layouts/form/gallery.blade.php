<div class="gallery-container">
    @if(!is_null($gallery))
        @foreach($gallery as $image)
            @if(is_object($image) && !empty($image->image))
                <div class="col-sm-3">
                    <div>
                        <i class="remove-gallery-image">-</i>
                        <input name="{{ $key }}[]" value="{{ $image->file_id }}" type="hidden">
                        <img src="{{ $image->url() }}">
                    </div>
                </div>
            @endif
        @endforeach
    @endif
    <div class="col-sm-3 add-gallery-image upload_image_button" data-type="multiple">
        <div class="add-btn"></div>
    </div>
</div>