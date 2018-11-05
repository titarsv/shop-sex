<?php

namespace App\Models;

class Filter
{
	// Текущая категория
    protected $category = null;
    // Коллекция фильтров доступных в текущей категории
    protected $product_attributes = null;
    // Необработанная строка фильтров
    protected $path = '';
    // Массив атрибутов для фильтрации товаров
    protected $filtered = [];
    // Выбранный диапазон цен
    protected $price = [];
    // Минимальная цена в текущей категории
    protected $min_price = 0;
	// Максимальная цена в текущей категории
    protected $max_price = 0;
    // Коллекция отфильтрованных товаров
    protected $products = null;
    // Фильтры для отображения
	protected $attributes = [];
	// Диапазоны цен доступные для фильтрации
	protected $price_ranges = [];

	function __construct($category = null){
        $this->categories = new Categories();
		if(!empty($category)){
			$this->setCategory($category);
		}
	}

    /**
     * Установка текущей категории
     *
     * @param $cat
     * @return $this
     */
    public function setCategory($cat){
    	if(is_int($cat)){
		    $this->category = Categories::find($cat);
	    }elseif(is_string($cat)){
		    $this->category = Categories::where('url_alias', $cat);
	    }elseif(is_object($cat) && $cat instanceof Categories){
		    $this->category = $cat;
	    }

        $this->setProductAttributes();
        $this->setPrice();
        return $this;
    }

    /**
     * Установка параметров
     *
     * @param $request_params
     * @return $this
     */
    public function setRequest($request_params){
        $this->params = $request_params;
        $this->setPrice();
        return $this;
    }

    /**
     * Установка параметров фильтрации из ЧПУ
     *
     * @param $path
     * @return $this
     */
    public function setFilterPath($path){
        $this->path = $path;
        $this->setFiltered();
	    $this->setPrice();
        return $this;
    }

    /**
     * Получить данные для фильтрации товаров
     *
     * @return array
     */
    public function getFilter(){
        return $this->filtered;
    }

    /**
     * Получить фильтр по цене
     *
     * @return array
     */
    public function getPrice(){
        return $this->price;
    }

    /**
     * Подгрузка атрибутов категории из БД
     */
    protected function setProductAttributes(){
        if(!empty($this->category)) {
            $this->product_attributes = $this->category->attributes()->with('values')->get();
        }
    }

    /**
     * Установка фильтра по цене
     *
     * @return $this
     */
    protected function setPrice(){
        $categories = $this->categories;
        if(!empty($this->category)){
            $this->min_price = $categories->min_price($this->category->id);
            $this->max_price = $categories->max_price($this->category->id);

            $price = [];
            $price[0] = empty($this->params['price_min']) ? 0 : $this->params['price_min'];
            $price[1] = empty($this->params['price_max']) || $this->params['price_max'] > $this->max_price ? $this->max_price : $this->params['price_max'];

            if(!empty($this->params['price'])){
                $price = explode('-', $this->params['price']);
            }

            if(!empty($this->path) && strpos($this->path, 'price-') !== false){
                $filter_parts = explode('_', $this->path);
                foreach($filter_parts as $part){
                    if(strpos($part, 'price-') !== false){
                        $price = explode('-', str_replace('price-', '', $part));
                    }
                }
            }

            $this->price = $price;
	        //$this->setPriceRanges();
        }

        return $this;
    }

	/**
	 * Получение коллекции отфильтрованных товаров
	 *
	 * @param array $current_sort
	 * @param int $take
	 * @param int $page
	 *
	 * @return null
	 */
    public function getProducts($current_sort = ['price', 'asc'], $take = 18, $page = 1){
	    if(empty($this->products) && !empty($this->category))
		    $this->products = $this->categories->get_products($this->category->id, null, $this->filtered, $current_sort, $take, $this->price, $page)->appends(['page' => $page]);

	    return $this->products;
    }

	/**
	 * Получение фильтров для вывода
	 *
	 * @return array
	 */
    public function getFilterAttributes(){
    	if(empty($this->attributes))
	        $this->setFilterAttributes();

    	return $this->attributes;
    }

	/**
	 * Получение ценовых диапазонов
	 *
	 * @return Filter|array
	 */
    public function getPriceRanges(){
    	if(empty($this->price_ranges)){
		    $this->setPriceRanges();
	    }

	    return $this->price_ranges;
    }

    /**
     * Ценовые диапазоны для слайдера цен
     *
     * @return array
     */
    public function getPriceSlider(){
        return array_merge([$this->min_price, $this->max_price], $this->price);
    }

	/**
	 * Настройка фильтров для вывода
	 *
	 * @return $this|array
	 */
	protected function setFilterAttributes(){
        if(empty($this->category)){
            return [];
        }else{
	        $categories = $this->categories;
            $filter = $this->filtered;
            $category_id = $this->category->id;
	        $current_price_range = $this->getCurrentPriceRange();
            if(empty($current_price_range) && !empty($this->price)){
                $current_price_range = ['slug' => implode('-', $this->price)];
            }
        }

        $attrs = [];
        foreach($this->product_attributes as $key => $attribute){
            $values = [];

            // Ценовой фильтр
            if($attribute->filter_type == 'range_list'){
                $values = $this->getFilterRanges($attribute);

            // Выбранные атрибуты
            }elseif(isset($filter[$attribute->id])){
                foreach($attribute->values as $i => $attribute_value){
                    $attr_filter = $filter;
                    $attr_filter[$attribute->id] = [$attribute_value->id];
                    $count = $categories->get_products_count($category_id, $attr_filter, $this->price, 1);
                    if($count){
                        $values[$attribute_value->id] = [
                            'name' => $attribute_value->name,
                            'value' => $attribute_value->value,
                            'checked' => in_array($attribute_value->id, $filter[$attribute->id]),
                            //'count' => $count,
                            'url' => $this->getFilterUrl($attribute_value, $current_price_range)
                        ];
                    }
                }
//                foreach ($filter[$attribute->id] as $val_id){
//                    $val = $attribute->values->first(function ($value, $key)  use ($val_id) {
//                        return $value->id == $val_id;
//                    });
//                    $values[$val->id] = [
//                        'name' => $val->name,
//                        'value' => $val->value,
//                        'checked' => true,
//                        'url' => $this->getFilterUrl($val, $current_price_range)
//                    ];
//                }

            // Не выбранные атрибуты
            }else{
                foreach($attribute->values as $i => $attribute_value){
                    $attr_filter = $filter + [$attribute->id => [$attribute_value->id]];
                    $count = $categories->get_products_count($category_id, $attr_filter, $this->price, 1);
                    if($count){
                        $values[$attribute_value->id] = [
                            'name' => $attribute_value->name,
                            'value' => $attribute_value->value,
                            'checked' => false,
                            'count' => $count,
                            'url' => $this->getFilterUrl($attribute_value, $current_price_range)
                        ];
                    }
                }
            }

            if(count($values)){
                $attrs[$attribute->id] = [
                    'name' => $attribute->name.(!empty($attribute->unit) ? ', '.$attribute->unit : ''),
                    'slug' => $attribute->slug,
                    'values' => $values
                ];
            }
        }
	    $this->attributes = $attrs;

        return $this;
    }

    /**
     * Генерация ЧПУ URL для фильтров
     *
     * @param $attribute_value
     * @return string
     */
    protected function getFilterUrl($attribute_value, $current_price_range){
	    $alias = $this->category->url_alias;
	    $filter = $this->filtered;
	    $product_attributes = $this->product_attributes;

        $url = '/'.$alias;
        $search = '';
	    if(!empty($attribute_value)) {
		    if ( isset( $filter[ $attribute_value->attribute_id ] ) && in_array( $attribute_value->id, $filter[ $attribute_value->attribute_id ] ) ) {
			    unset( $filter[ $attribute_value->attribute_id ][ array_search( $attribute_value->id, $filter[ $attribute_value->attribute_id ] ) ] );
		    } else {
			    $filter[ $attribute_value->attribute_id ][] = $attribute_value->id;
		    }
	    }

        asort($filter);

        foreach ($filter as $attr_id => $values){
            if(!empty($values)){
                asort($values);
                $attr = $product_attributes->find($attr_id);
                if(!empty($attr)){
                    if(!empty($search)){
                        $search .= '_';
                    }
                    $search .= str_replace(array('#', '-', '_', '?'), '', $attr->slug);
                    foreach ($values as $value_id){
                        $value = $attr->values->find($value_id);
                        $search .= '-'.str_replace(array('#', '-', '_', '?'), '', $value->value);
                    }
                }
            }
        }

	    if(!empty($current_price_range)){
		    if(!empty($search)){
			    $search .= '_';
		    }
		    $search .= 'price-'.$current_price_range['slug'];
        }

        if(!empty($search)){
            $url .= '/'.$search;
        }

        return $url;
    }

	/**
	 * Генерация ценовых диапазонов
	 *
	 * @return $this
	 */
    protected function setPriceRanges(){
    	if(empty($this->category)){
		    return $this;
	    }

	    $min_price = $this->min_price;
	    $max_price = $this->max_price;
	    $price = $this->price;
	    $category_id = $this->category->id;
	    $attr_filter = $this->filtered;

        $values = [];
        $categories = $this->categories;

        $step = ($max_price - $min_price) / 6;
        $round = 8;
        while (round($step, $round) > 0){
            $round--;
        }
        $step = round($step, $round+1);

        for($i=1; $i<6; $i++){
            if($i == 1){
                $size = '< ' . ($min_price + $step * $i);
                $fprice = [0, $min_price + $step * $i];
                $id = '0-' . ($min_price + $step * $i);
            }elseif($i == 5){
                $size = '> ' . ($min_price + $step * ($i-1));
                $fprice = [$min_price + $step * ($i-1), $max_price];
                $id = ($min_price + $step * ($i-1)) . '-' . $max_price;
            }else{
                $size = ($min_price + $step * ($i-1)) . ' - ' . ($min_price + $step * $i);
                $fprice = [$min_price + $step * ($i-1), ($min_price + $step * $i)];
                $id = str_replace(' ', '', $size);
            }

            if($price[0] > 0 || $price[1] < $max_price){
                if($fprice == $price){
                    $values[$id] = [
                        'name' => $size,
                        'checked' => true,
	                    'slug' => $id,
                        'url' => $this->getFilterUrl(null, null)
                    ];
                }
            }else{
                $count = $categories->get_products_count($category_id, $attr_filter, $fprice, 1);
                if($count){
                    $values[$id] = [
                        'name' => $size,
                        'checked' => false,
                        'count' => $count,
                        'slug' => $id,
                        'url' => $this->getFilterUrl(null, ['slug' => $id])
                    ];
                }
            }
        }

        $this->price_ranges = $values;

        return $this;
    }

    /**
     * Установка структурированного фиьтра
     *
     * @return $this
     */
    protected function setFiltered(){
        $filters = $this->path;
        $filter = [];
        $attr = new Attribute();

        if (isset($this->params['filter_attributes']) && $this->params['filter_attributes'] !== null) {
            foreach ($this->params['filter_attributes'] as $attribute_id => $value) {
                $cattr = $attr->find($attribute_id);

                if(isset($cattr->filter_type) && $cattr->filter_type == 'range_list'){
                    foreach ($value['value'] as $attribute_value => $on) {
                        $filter[$attribute_id] = $this->getFiltersForRange($attribute_value, $cattr->values);
                    }
                }else{
                    foreach ($value['value'] as $attribute_value_id => $on) {
                        $filter[$attribute_id][] = $attribute_value_id;
                    }
                }
            }
        }elseif(!empty($filters)){
            $filter = $this->prepared_filters($filters);
            $filter = $this->parseValuesIds($filter);
        }
        $this->filtered = $filter;

        return $this;
    }

    /**
     * Получение ID фильтров по их слагу
     *
     * @param $filter
     * @return array
     */
    protected function parseValuesIds($filter){
        $new_filter = [];
        $attr = new Attribute();
        foreach ($filter as $attr_slug => $values){
            if($attr_slug != 'price') {
                $attribute = $attr->where('slug', $attr_slug)->first();
                if ($attribute !== null) {
                    foreach ($values as $value_slug) {
//                        if ($attribute->name == 'Цвет') {
//                            $value_slug = '#' . $value_slug;
//                        }
                        $val = $attribute->values()->where('value', $value_slug)->first();
                        if ($val !== null) {
                            $new_filter[$attribute->id][] = $val->id;
                        }
                    }
                }
            }
        }
        return $new_filter;
    }

	/**
	 * Получение фильтра для диапазона цен
	 *
	 * @param $attribute_value
	 * @param $all_values
	 *
	 * @return array
	 */
    protected function getFiltersForRange($attribute_value, $all_values){
        $filters = [];

        if($attribute_value[0] == '<'){
            $value = (float)str_replace('<', '', $attribute_value);
            foreach ($all_values as $val){
                if((float)$val->value < $value){
                    $filters[] = $val->id;
                }
            }
        }else if($attribute_value[0] == '>'){
            $value = (float)str_replace('>', '',$attribute_value);
            foreach ($all_values as $val){
                if((float)$val->value > $value){
                    $filters[] = $val->id;
                }
            }
        }else{
            $values = explode('-', $attribute_value);

            foreach ($all_values as $val){
                if((float)$val->value >= (float)$values[0] && (float)$val->value <= (float)$values[1]){
                    $filters[] = $val->id;
                }
            }
        }

        return $filters;
    }

	/**
	 * Получение ценовых диапазонов
	 *
	 * @param $attribute
	 *
	 * @return array
	 */
    protected function getFilterRanges($attribute){
	    $filter = $this->filtered;
	    $price = $this->price;
	    $category_id = $this->category->id;
        $values = [];

        $min = $attribute->values->min('value');
        $max = $attribute->values->max('value');

        $step = ($max - $min) / 7;

        $round = 8;

        while (round($step, $round) > 0){
            $round--;
        }

        $step = round($step, $round+1);

        $categories = $this->categories;

        for($i=1; $i<7; $i++){
            if($i == 1){
                $size = '< ' . ($min + $step * $i);
            }elseif($i == 6){
                $size = '> ' . ($min + $step * ($i-1));
            }else{
                $size = ($min + $step * ($i-1)) . ' - ' . ($min + $step * $i);
            }

            $id = str_replace(' ', '', $size);
            $f = $this->getFiltersForRange($id, $attribute->values);

            if(isset($filter[$attribute->id])){
                if(!empty($f) && $f == $filter[$attribute->id]){
                    $values[$id] = [
                        'name' => $size,
                        'checked' => true
                    ];
                }
            }elseif(!empty($f)){
                $attr_filter = $filter + [$attribute->id => $f];
                $count = $categories->get_products_count($category_id, $attr_filter, $price, 1);
                if($count){
                    $values[$id] = [
                        'name' => $size,
                        'checked' => false,
                        'count' => $count
                    ];
                }
            }
        }

        return $values;
    }

    /**
     * Парсинг фильтров
     *
     * @param string $string
     * @return array
     */
    protected function prepared_filters($string = ''){
        $data = explode('_', $string);

        $filters = [];
        foreach ($data as $filter){
            $filter_data = explode('-', $filter);
            if(count($filter_data) >= 2) {
                $key = $filter_data[0];
                array_splice($filter_data, 0, 1);
                $filters[$key] = $filter_data;
            }
        }

        return $filters;
    }

	/**
	 * Получение выбранного ценового интервала
	 *
	 * @return mixed|null
	 */
    public function getCurrentPriceRange(){
    	if(!empty($this->price_ranges)){
    		foreach ($this->price_ranges as $range){
    			if($range['checked']){
    				return $range;
			    }
		    }
	    }

	    return null;
    }
}