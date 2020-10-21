<div class="image-container">
    <input type="hidden" name="{{ $key }}" value="{{ old($key) ? old($key) : (!empty($image) ? $image->id : '') }}" />
    @if(!empty(old($key.'_link')) || !empty($image))
        <div>
            <div>
                <i class="remove-image">-</i>
                <img src="{{ old($key.'_link') ? old($key.'_link') : $image->url() }}" />
            </div>
        </div>
        <div class="upload_image_button" data-type="single" style="display: none;">
            <div class="add-btn"></div>
        </div>
    @else
        <div class="upload_image_button" data-type="single">
            <div class="add-btn"></div>
        </div>
    @endif
</div>