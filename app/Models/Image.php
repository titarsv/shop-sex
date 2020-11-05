<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\ImageManagerStatic as Img;

class Image extends Model
{
	protected $files_path = '/uploads/';
	protected $max_width = 1920;
	protected $max_height = 1920;

    public function blog()
    {
        return $this->hasOne('App\Models\Blog', 'image_id', 'id');
    }

    public function user_data()
    {
        return $this->hasOne('App\Models\UserData', 'image_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Categories', 'id', 'image_id');
    }

    /**
     * Получение изображения по id
     * @param int $id Id изображения
     * @return object
     */
    public function get_image($id)
    {
        return $this->where('id', $id)
            ->take(1)
            ->get()
            ->first();
    }

    /**
     * Удаление изображения по id
     * @param int $id Id изображения
     * @return mixed
     */
    public function remove_image($id)
    {
        return $this->where('id', $id)
            ->delete();
    }

    /**
     * Получение необновлённого изображения
     * @param string $date Время начала обновления
     * @return object
     */
    public function get_not_updated_image($date)
    {
        return $this->where('updated_at', '<', $date)
            ->orderBy('id', 'asc')
            ->take(1)
            ->get()
            ->first();
    }

    /**
     * Колличество обновлённых изображений
     * @param string $date Время начала обновления
     * @return mixed
     */
    public function get_updated_images_count($date)
    {
        return $this->where('updated_at', '>=', $date)
            ->count();
    }

    /**
     * Прогресс обновления изображений
     * @param string $date Время начала обновления
     * @return mixed
     */
    public function get_updating_progress($date)
    {
        $count = $this->count();
        $updated = $this->get_updated_images_count($date);
        $progress = floor($updated/$count*100);
        return $progress;
    }

    /**
     * Обновление размеров изображения
     * @param int $id
     * @param array $sizes
     */
    public function update_images_sizes($id, $sizes)
    {
        $this->where('id', $id)
            ->update(['sizes' => json_encode($sizes)]);
    }

	/**
	 * Получение нужного размера изображения
	 * @param int|array|object $image id, массив, объект изображения
	 * @param string|array $size Размер изображения ('full', 'product', 'product_list', 'blog', [100, 100])
	 * @return string Абсолютный путь к изображению
	 */
	public function get_file_url($image, $size = 'full')
	{
		if(is_object($image))
			$image = $image->toArray();
		elseif(is_int($image))
			$image = $this->where('id', $image)
			              ->take(1)
			              ->get()
			              ->first()
			              ->toArray();

		if($size == 'full'){
			return $this->files_path . $image['href'];
		}

		$img_sizes = json_decode($image['sizes']);

		if(is_array($size)){
			foreach ($img_sizes as $img_size){
				if($img_size->w == $size[0] && $img_size->h == $size[1])
					return $this->files_path . $img_size->href;
			}
			return $this->create_size($image, implode('_', $size), ['width' => $size[0], 'height' => $size[1]]);
//            foreach ($img_sizes as $img_size){
//                if($img_size->w >= $size[0] && $img_size->h >= $size[1])
//                    return $this->files_path . $img_size->href;
//            }
		}else{
			if(isset($img_sizes->$size)){
				return $this->files_path . $img_sizes->$size->href;
			}
			$image_sizes = config("image.sizes");
			if(isset($image_sizes[$size])){
				return $this->create_size($image, $size, $image_sizes[$size]);
			}
		}

		return $this->files_path . $image['href'];
	}

	/**
	 * Создание недостающей миниатюры
	 *
	 * @param $image
	 * @param $name
	 * @param $sizes
	 * @return string
	 */
	public function create_size($image, $name, $sizes){
		$img_sizes = json_decode($image['sizes'], true);
		$new_file = $this->update_image_size($image['href'], $sizes['width'], $sizes['height'], 'contain');

		if(!empty($new_file)) {
			$href = $new_file;
		}else{
			$href = $image['href'];
		}

		$img_sizes[$name]['href'] = $href;
		$img_sizes[$name]['w'] = $sizes['width'];
		$img_sizes[$name]['h'] = $sizes['height'];
		$this->where('id', $image['id'])
		     ->take(1)
		     ->get()
		     ->first()->update_images_sizes($image['id'], $img_sizes);
		return $this->files_path . $href;
	}

    /**
     * Получение нужного размера текущего изображения
     * @param string|array $size Размер изображения ('full', 'product', 'product_list', 'blog', [100, 100])
     * @return string Абсолютный путь к изображению
     */
    public function url($size = 'full')
    {
        return $this->get_file_url($this, $size);
    }

	/**
	 * Вывод оптимизированного изображения
	 *
	 * @param string $size
	 * @param array $attributes
	 * @param bool $lazy
	 * @return string
	 * @throws \Throwable
	 */
	public function webp_image($size = 'full', $attributes = [], $lazy = false){
		$image_data = $this->toArray();
		if(is_array($size)){
			$size = $this->find_size_name($image_data, $size);
		}
		$data = $this->find_by_size($image_data, $size);

		if(empty($data)){
			$size = 'full';
			$data = $this->find_by_size($image_data, $size);
			if(empty($data)){
				$sizes = json_decode($image_data['sizes']);
				if(empty($sizes)){
					$sizes = (object)[];
				}
				$path = public_path($this->files_path) . $image_data['href'];
				if(is_file($path)){
					$imagesizes = getimagesize($path);
					if($imagesizes[0]*$imagesizes[1] > $this->max_width*$this->max_height){
						$sizes->full = (object)[
							'w' => $imagesizes[0],
							'h' => $imagesizes[1],
							'href' => $image_data['href']
						];
					}elseif($imagesizes[0] > $this->max_width || $imagesizes[1] > $this->max_height){
						$name = $this->update_image_size($image_data['href'], $this->max_width, $this->max_height,'contain');
						if(is_file(public_path($this->files_path) . $name)) {
							$imagesizes = getimagesize(public_path($this->files_path) . $name);
						}
						$sizes->full = (object)[
							'w' => $imagesizes[0],
							'h' => $imagesizes[1],
							'href' => $name
						];
					}else{
						$sizes->full = (object)[
							'w' => $imagesizes[0],
							'h' => $imagesizes[1],
							'href' => $image_data['href']
						];
					}
				}
				$this->update_images_sizes($this->id, $sizes);
				if(isset($sizes->full)){
					$data = $sizes->full;
				}
			}
		}

		if(!empty($data)){
			$original = $data->href;
			if(isset($data->webp) && is_file(public_path($this->files_path) . $data->webp)){
				$webp = $data->webp;
			}elseif(isset($data->w) && isset($data->h) && ($data->w <= $this->max_width || $data->h <= $this->max_height)){
				$webp = $this->createWebp($size);
			}else{
				if(isset($data->w) && isset($data->h) && ($data->w > $this->max_width || $data->h > $this->max_height) && is_file(public_path($this->files_path) . $original) && !is_file(public_path('/big_images/') . $original)){
					copy(public_path($this->files_path) . $original, public_path('/big_images/') . $original);
				}
				$webp = '';
			}

			$mime = strtolower(pathinfo($original, PATHINFO_EXTENSION ));
			if($mime == 'jpg'){
				$mime = 'jpeg';
			}

			return view('public.layouts.webp')
				->with('original', $original)
				->with('original_mime', $mime)
				->with('webp', $webp)
				->with('attributes', $attributes)
				->with('lazy', $lazy)
				->render();
		}

		return view('public.layouts.webp')
			->with('attributes', $attributes)
			->render();
	}

	/**
	 * Создание webp-изображения
	 *
	 * @param $size
	 *
	 * @return mixed|null
	 */
	public function createWebp($size){
		$image_data = $this->toArray();
		$data = $this->find_by_size($image_data, $size);
		$sizes = json_decode($image_data['sizes']);

		if(!empty($data)){
			$filepath = public_path() . $this->files_path . $data->href;
			$extension = strtolower(pathinfo($data->href, PATHINFO_EXTENSION ));
			$webp_name = str_replace('.'.$extension, '_'.$extension.'.webp', $data->href);
			$webp_path = public_path() . $this->files_path . $webp_name;
			if(is_file($filepath) && function_exists('imagewebp')){
				$image = $this->imagecreatefromfile($filepath);
				imagepalettetotruecolor($image);
				imagealphablending($image, true);
				imagesavealpha($image, true);
				imagewebp($image, $webp_path);
				imagedestroy($image);

				$sizes->$size->webp = $webp_name;
				$this->update_images_sizes($this->id, $sizes);
				return $webp_name;
			}
		}

		return null;
	}

	/**
	 * Поиск названия размера миниатюры
	 *
	 * @param $image_data
	 * @param array $size
	 *
	 * @return int|null|string
	 */
	public function find_size_name($image_data, $size = [0, 0]){
		$img_sizes = json_decode($image_data['sizes'], true);

		foreach ($img_sizes as $name => $img_size){
			if($img_size['w'] == $size[0] && $img_size['h'] == $size[1])
				return $name;
		}
		$size_name = implode('_', $size);
		$href = $this->create_size($image_data, $size_name, ['width' => $size[0], 'height' => $size[1]]);
		return $size_name;
	}

	/**
	 * Поиск миниатюры необходимого размера
	 *
	 * @param $image_data
	 * @param $size
	 *
	 * @return null
	 */
	public function find_by_size($image_data, $size){
		$img_sizes = json_decode($image_data['sizes']);
		$data = null;

		if(is_array($size)){
			$name = $this->find_size_name($image_data, $size);
			if(!empty($name)){
				return $img_sizes->$name;
			}
		}else{
			if(isset($img_sizes->$size)){
				$data = $img_sizes->$size;
			}
		}

		return $data;
	}

    /**
     * Используется ли изображение
     * @param int $id Id изображения
     * @return bool
     */
    public function is_used($id)
    {
        $blog = Blog::where('image_id', $id)
            ->take(1)
            ->count();
        if($blog > 0)
            return true;

        $products = Products::where('image_id', $id)
            ->take(1)
            ->count();
        if($products > 0)
            return true;

        $slideshow = ModuleSlideshow::where('image_id', $id)
            ->take(1)
            ->count();
        if($slideshow > 0)
            return true;

        return false;
    }

	/**
	 * Динамическое создание объекта изображения из файла
	 * @param $filename
	 * @return resource
	 */
	public function imagecreatefromfile($filename)
	{
		$mime = mime_content_type($filename);
		if($mime == 'image/webp'){
			return imagecreatefromwebp($filename);
		}elseif($mime == 'image/jpeg'){
			return imagecreatefromjpeg($filename);
		}elseif($mime == 'image/png'){
			return imagecreatefrompng($filename);
		}elseif($mime == 'image/gif'){
			return imagecreatefromgif($filename);
		}

		switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION ))) {
			case 'jpeg':
			case 'jpg':
				return imagecreatefromjpeg($filename);
				break;

			case 'png':
				return imagecreatefrompng($filename);
				break;

			case 'gif':
				return imagecreatefromgif($filename);
				break;
		}
	}

    public function create_thumbnail($filepath, $overlays)
    {
        $image = $this->imagecreatefromfile($filepath);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        list($src_width, $src_height) = getimagesize($filepath);

        foreach ($overlays as $overlay) {
            $attribute_images = array_slice($overlay['images'], 0, $overlay['settings']['max_quantity']);
            $offset = 0;

            foreach ($attribute_images as $i => $attribute_image) {
                $overlay_image = $this->imagecreatefromfile(public_path('assets/attributes_images/') . $attribute_image);
                list($width, $height) = getimagesize(public_path('assets/attributes_images/') . $attribute_image);

                $percent = $overlay['settings']['image_percent'];
                $newwidth = $src_width * $percent;
                $new_percent = $newwidth / $width;
                $newheight = $height * $new_percent;

                $offset_x = $overlay['settings']['offset_x'];
                $offset_y = $overlay['settings']['offset_y'];

                $thumb = imagecreatetruecolor($newwidth, $newheight);

                imagesavealpha($thumb, true);
                $trans_colour = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
                imagefill($thumb, 0, 0, $trans_colour);

                imagecopyresampled($thumb, $overlay_image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                $position = $overlay['settings']['coordinates'];

                if ($i > 0) {
                    $offset += $newwidth / 2;
                }

                if ($position == 'left_top') {
                    $x = $offset_x + $offset;
                    $y = $offset_y;
                } elseif ($position == 'right_top') {
                    $x = $src_width - $newwidth - $offset - $offset_x;
                    $y = $offset_y;
                } elseif ($position == 'left_bottom') {
                    $x = $offset_x + $offset;
                    $y = $src_height - $newheight - $offset_y;
                } elseif ($position == 'right_bottom') {
                    $x = $src_width - $newwidth - $offset - $offset_x;
                    $y = $src_height - $newheight - $offset_y;
                }

                imagecopy($image, $thumb, $x, $y, 0, 0, $newwidth, $newheight);

                imagedestroy($thumb);
            }

        }
        $new_file_name = str_random(10) . '.png';
        imagepng($image, public_path('uploads/') . $new_file_name, 0);

        $id = $this->insertGetId(
            ['title' => 'product_thumbnail', 'href' => $new_file_name, 'type' => 'thumb']
        );

        $this->find($id)->update_image_sizes();

        return $id;

    }

    /**
     * Получение id изображения по его имени
     * @param $title
     * @return int
     */
    public function get_id_by_title($title)
    {
        $image = $this->select('id')
            ->where('title', $title)
            ->take(1)
            ->first();

        if($image == null)
            return 0;
        else
            return $image->id;
    }

    /**
     * Получение предустановленных размеров для данного типа изображения
     * @return array
     */
    public function get_image_type_sizes(){
        $image_types = config('image.types');
        $image_type_data = isset($image_types[$this->type])?$image_types[$this->type]:$image_types['default'];
        $image_sizes = [];
        if(isset($image_type_data['sizes'])){
            foreach ($image_type_data['sizes'] as $size){
                $image_sizes[$size] = config("image.sizes.$size");
            }
        }else{
            $image_sizes = config("image.sizes");
        }
        return $image_sizes;
    }

    /**
     * Обновление размеров изображения
     */
    public function update_image_sizes()
    {
        $image_data = $this->toArray();
        $created_sizes = json_decode($image_data['sizes'], true);
        if(!is_array($created_sizes))
            $created_sizes = array();

        // Получение настроек
        $registered_sizes = $this->get_image_type_sizes();

        // Удаление лишних изображений
        foreach ($created_sizes as $size => $data){
            //if(!isset($registered_sizes[$size]) || isset($data['href']) && ($data['w'] != $registered_sizes[$size]['width'] || $data['h'] != $registered_sizes[$size]['height'])){
                $path = public_path('uploads/' . $data['href']);
                if(is_file($path))
                    unlink($path);
                unset($created_sizes[$size]);
            //}
        }

        // Создание новых изображений
        foreach ($registered_sizes as $size => $data){
            if(!isset($created_sizes[$size]))
                $created_sizes[$size] = array('w' => 0, 'h' => 0);

            if($created_sizes[$size]['w'] != $data['width'] || $created_sizes[$size]['h'] != $data['height']){
                $new_file = $this->update_image_size($image_data['href'], $data['width'], $data['height'], 'contain');

                if(!empty($new_file)) {
                    $created_sizes[$size]['href'] = $new_file;
                    $created_sizes[$size]['w'] = $data['width'];
                    $created_sizes[$size]['h'] = $data['height'];
                }else{
                    unset($created_sizes[$size]);
                }
            }
        }

        $this->update_images_sizes($image_data['id'], $created_sizes);
    }

    /**
     * Создание изображения заданного размера
     * @param string $href Имя файла
     * @param int $w Ширина
     * @param int $h Высота
     * @param string $method (contain|cover|crop)/(уместить/заполнить/обрезать)
     * @return string Имя созданного файла
     */
    public function update_image_size($href, $w, $h, $method = 'contain')
    {
        $name_parts = explode('.', $href);
        $extension = end($name_parts);

        $path = str_replace('\\', '/', public_path('uploads/' . $href));
        if(!is_file($path)){
        	return null;
        }
        $new_name = '';

        $image = Img::make($path);
        $original_width = $image->width();
        $original_height = $image->height();

        if(($original_width < $w && $original_height < $h) || ($method != 'contain' && ($original_width < $w || $original_height < $h)))
            return $new_name;

        if($original_width/$w >= $original_height/$h) {
            switch ($method) {
                case 'contain':
                    $image->resize($w, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $new_name = $this->save_image_file($image, $original_width, $original_height, $w, $image->height(), $extension, $href);
                    break;
                case 'cover':
                    $image->resize(null, $h, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $new_name = $this->save_image_file($image, $original_width, $original_height, $image->width(), $h, $extension, $href);
                    break;
                case 'crop':
                    $image->resize(null, $h, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $new_name = $this->save_image_file($image, $original_width, $original_height, $w, $h, $extension, $href, 'crop');
                    break;
            }
        }elseif($original_width/$w <= $original_height/$h) {
            switch ($method) {
                case 'contain':
                    $image->resize(null, $h, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $new_name = $this->save_image_file($image, $original_width, $original_height, $image->width(), $h, $extension, $href);
                    break;
                case 'cover':
                    $image->resize($w, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $new_name = $this->save_image_file($image, $original_width, $original_height, $w, $image->height(), $extension, $href);
                    break;
                case 'crop':
                    $image->resize($w, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $new_name = $this->save_image_file($image, $original_width, $original_height, $w, $h, $extension, $href, 'crop');
                    break;
            }
        }else {
            $this->save_image_file($image, $original_width, $original_height, $w, $h, $extension, $href, 'resize');
        }
        return $new_name;
    }

    public function save_image_file($image, $original_width, $original_height, $result_width, $result_height, $extension, $href, $method = ''){
        if($original_width != $result_width || $original_height != $result_height){
            $new_name = str_replace('.'.$extension, '_'.$result_width.'x'.$result_height.'.'.$extension, $href);
            $new_path = public_path('uploads/' . $new_name);
            switch ($method) {
                case 'resize':
                    $image->resize($result_width, $result_height)->save($new_path);
                    break;
                case 'crop':
                    $image->crop($result_width, $result_height)->save($new_path);
                    break;
                default:
                    $image->save($new_path);
            }
            return $new_name;
        }else{
            return '';
        }
    }

    public function getFullData(){
        $url = $this->url();
        $filedata = $this->fileData();
        $mime = explode('/', $filedata['mime']);
        $sizes = [];
        $s = json_decode($this->sizes);
        if(is_object($s)) {
            foreach ($s as $name => $size) {
                $sizes[$name] = [
                    'url' => $this->url($name),
                    'height' => $size->h,
                    'width' => $size->w,
                    'orientation' => 'landscape',
                ];
            }
        }
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'filename' => $this->href,
            'url' => $url,
            'link' => $url,
            'alt' => $this->title,
            'author' => 'admin',
            'description' => '',
            'caption' => '',
            'name' => $this->title,
            'status' => 'inherit',
            'uploadedTo' => 0,
            'date' => $this->created_at->timestamp,
            'modified' => empty($this->updated_at) ? $this->created_at->timestamp : $this->updated_at->timestamp,
            'menuOrder' => 0,
            'mime' => $filedata['mime'],
            'type' => $mime[0],
            'subtype' => $mime[1],
            'icon' => '/images/larchik/default.png',
            'dateFormatted' =>  empty($this->updated_at) ? $this->created_at->format('d.m.Y') : $this->updated_at->format('d.m.Y'),
            'nonces' => [
                "update" => "2c7fa5b435",
                "delete" =>"77118a539c",
                "edit" => "fb580011e5"
            ],
            'editLink' => '',
            'meta' => false,
            'authorName' => 'admin',
            'filesizeInBytes' => $filedata['filesize'],
            'filesizeHumanReadable' => $filedata['filesizeHumanReadable'],
            'context' => '',
            'height' => $filedata[1],
            'width' => $filedata[0],
            'orientation' => "landscape",
            'sizes' => $sizes,
            'compat' => [
                'item' => '',
                'meta' => '',
            ],
        ];

        return $data;
    }

    public function fileData(){
        $filepath = public_path() . $this->files_path . $this->href;
        if(is_file($filepath)){
            try {
                $data                          = getimagesize( $filepath );
                $data['filesize']              = filesize( $filepath );
                $data['filesizeHumanReadable'] = $this->size_format( $data['filesize'] );
            }catch (Exception $e){
                $data = [0 => 0, 1 => 0, 'mime' => ' / ', 'filesize' => '', 'filesizeHumanReadable' => ''];
            }
        }else{
            $data = [0 => 0, 1 => 0, 'mime' => ' / ', 'filesize' => '', 'filesizeHumanReadable' => ''];
        }

        return $data;
    }

    public function size_format( $bytes, $decimals = 0 ) {
        $quant = array(
            'TB' => 1024*1024*1024*1024,
            'GB' => 1024*1024*1024,
            'MB' => 1024*1024,
            'KB' => 1024,
            'B'  => 1,
        );

        if ( 0 === $bytes ) {
            return number_format( 0, abs(intval( $decimals )) ) . ' B';
        }

        foreach ( $quant as $unit => $mag ) {
            if ( doubleval( $bytes ) >= $mag ) {
                return number_format( $bytes / $mag, $decimals ) . ' ' . $unit;
            }
        }

        return false;
    }
}