<div class="gallery-container">
	@if(!is_null($gallery))
	@foreach($gallery->objects() as $image)
	<div class="col-sm-3">
		<div>
			<!-- Кнопка для удаления изображения из галереи -->
			<i class="remove-gallery-image">-</i>
			<!-- Скрытое поле для хранения ID изображения -->
			<input type="hidden" name="gallery[]" value="{{ old($key) ? old($key) : (!empty($image) ? $image->id : '') }}" />
			<!-- Отображение изображения -->
			<img src="{{ old($key.'_link') ? old($key.'_link') : $image->url() }}">
		</div>
	</div>
	@endforeach
	@endif

	<!-- Кнопка для добавления нового изображения в галерею -->
	<div class="col-sm-3 add-gallery-image upload_image_button" data-type="multiple">
		<div class="add-btn"></div>
	</div>
</div>