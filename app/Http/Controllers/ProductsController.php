<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Variation;
use App\Product;
use Illuminate\Http\Request;
use Validator;

use Illuminate\Support\Facades\Cookie;
use App\Models\Categories;
use App\Models\Products;
use App\Models\Image;
use App\Models\ModuleRecommended;
use Excel;
use App\Models\Gallery;
use App\Models\Sync;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{

    private $products;
    private $rules = [
        //'name' => 'required|unique:products',
        'price' => 'required|numeric',
        //'articul' => 'required|unique:products',
        'quantity' => 'numeric',
//        'capacity' => 'numeric',
//        'product_category_id' => 'required',
//        'meta_title' => 'required|max:75',
//        'meta_description' => 'max:180',
//        'meta_keywords' => 'max:180',
        //'url_alias' => 'required|unique:products|unique:categories',
    ];

    private $messages = [
        'name.required' => 'Поле должно быть заполнено!',
        'name.unique' => 'Название товара должно быть уникальным!',
        'price.required' => 'Поле должно быть заполнено!',
        'price.numeric' => 'Значение должно быть числовым!',
        'articul.required' => 'Поле должно быть заполнено!',
        'articul.unique' => 'Артикул товара должен быть уникальным!',
        'quantity.numeric' => 'Значение должно быть числовым!',
        'capacity.numeric' => 'Значение должно быть числовым!',
        'product_category_id.not_in' => 'Не выбрана категория товара!',
        'meta_title.required' => 'Поле должно быть заполнено!',
//        'meta_title.max' => 'Максимальная длина заголовка не может превышать 75 символов!',
//        'meta_description.max' => 'Максимальная длина описания не может превышать 180 символов!',
//        'meta_keywords.max' => 'Максимальная длина ключевых слов не может превышать 180 символов!',
        'url_alias.required' => 'Поле должно быть заполнено!',
        'url_alias.unique' => 'Значение должно быть уникальным для каждого товара!',
    ];

    public $sort = [
        'date_added' => [
            'name'  => 'Дата добавления',
            'dest'  => 'DESC',
            'sort'  => 'id'
        ],
        'date_modified' => [
            'name'  => 'Дата изменения',
            'dest'  => 'DESC',
            'sort'  => 'updated_at'
        ],
        'name_asc' => [
            'name'  => 'Имя (А-Я)',
            'dest'  => 'ASC',
            'sort'  => 'name'
        ],
        'name_desc' => [
            'name'  => 'Имя (Я-А)',
            'dest'  => 'DESC',
            'sort'  => 'name'
        ]
    ];

    public $show = [15,30,45,60];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Categories $categories)
    {

        $category_id = false;
        $stock = false;
        $current_sort = false;

        if(empty($request->sort)){
            $request->sort = 'date_added';
        }

        if($request->sort) {
            $current_sort = $this->sort[$request->sort];
            $current_sort['value'] = $request->sort;
        }

        if($request->show) {
            if ($request->cookie('show_list') == null || $request->cookie('show_list') !== $request->show) {
                Cookie::queue('show_list', $request->show);
            }
            $current_show = $request->show;
        } else {
            if ($request->cookie('show_list') == null) {
                Cookie::queue('show_list', 60);
                $current_show = 60;
            } else {
                $current_show = $request->cookie('show_list');
            }
        }

        if (isset($request->category)) {
            $category_id = $request->category;
        }
        if (isset($request->stock)) {
            $stock = $request->stock;
        }

        if ($request->search) {
            $products = new Products();
            $search_text = $request->search;
            $per_page = $current_show;

            //$products = $products->search($search_text);
            $products = $products->where('name', 'like', '%'.$search_text.'%')->orWhere('articul', 'like', '%'.$search_text.'%')->get();

            // Пагинация
            $paginator_options = [
                'path'=>url($request->url()),
                'query' => [
                    'sort' => $request->sort,
                    'search' => $request->search
                ]
            ];

            if(empty($per_page))
                $per_page = config('view.product_quantity');
            $current_page = $request->page ? $request->page : 1;
            $current_page_products = $products->slice(($current_page - 1) * $per_page, $per_page)->all();
            $products = new LengthAwarePaginator($current_page_products, count($products), $per_page, $current_page, $paginator_options);
            $current_search = $request->search;
        } else {
            $products = Products::select('products.*')->when($category_id, function($query) use ($category_id){
                return $query->join('product_categories AS cat', 'products.id', '=', 'cat.product_id')->where('cat.category_id', $category_id);
            })
            ->when(($stock !== false), function($query) use ($stock){
                return $query->where('products.stock', $stock);
            })
            ->when($current_sort, function($query) use ($current_sort){
                return $query->orderBy($current_sort['sort'], $current_sort['dest']);
            })
            ->paginate($current_show);

	        // Пагинация
//	        $paginator_options = [
//		        'path'=>url($request->url()),
//		        'query' => []
//	        ];
//
	        if(!empty($category_id)){
		        $products->appends(['category' => $category_id]);
//		        $paginator_options['query']['category'] = $category_id;
	        }
//
//	        if(empty($per_page))
//		        $per_page = config('view.product_quantity');
//	        $current_page = $request->page ? $request->page : 1;
//	        $current_page_products = $products->slice(($current_page - 1) * $per_page, $per_page)->all();
//	        $products = new LengthAwarePaginator($current_page_products, count($products), $per_page, $current_page, $paginator_options);

            $current_search = false;
        }

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Categories::all(),
            'array_sort' => $this->sort,
            'current_sort' => $current_sort,
            'array_show' => $this->show,
            'current_show' => $current_show,
            'current_search' => $current_search
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Products();

        return view('admin.products.create')
            ->with('categories', Categories::all())
            ->with('labels', $product->labels())
            ->with('attributes', Attribute::all());
    }

    /**
     * Создание товара
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Image $image, Products $products, Gallery $gallery)
    {
        $attributes_error = $this->validate_attributes($request->product_attributes);

        $validator = Validator::make($request->all(), $this->rules, $this->messages);

//        dd($image->find($request->image_id)->href);
//
//        $image_href = $image->find($request->image_id)->href;
//
//        $request->merge(['href' => $image_href]);

        if ($validator->fails() || $attributes_error) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator)
                ->with('attributes_error', $attributes_error);
        }

//        $gallery_id = $gallery->add_gallery($request->gallery);
//
//        $request->merge(['gallery_id' => $gallery_id]);

        $data = ['products' => $request->except('product_category_id')];
        if (!empty($request->product_attributes)) {
            $data['product_attributes'] = [];
            foreach ($request->product_attributes as $attribute) {
                $data['product_attributes'][] = [
                    'attribute_id' => $attribute['id'],
                    'attribute_value_id' => $attribute['value']
                ];
            }
        }

        if(!empty($request->product_category_id)){
            $data['categories'] = $request->product_category_id;
        }

        $data['galleries'] = $request->gallery;
//        $data['photos'] = $request->photos;

        $id = $products->insert_product($data);

        if($id != 'already_exist'){
            $this->updateVariations($products->find($id), $request->variations);
        }

        return redirect('/admin/products')
            ->with('message-success', 'Товар ' . $products->name . ' успешно добавлен.');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Products::find($id);

        if(empty($product))
            abort(404);

        $categories = [];
        if(!empty($product->categories)) {
            foreach ($product->categories as $category){
                $categories[] = $category->id;
            }
        }

        $sets = Products::where('id', '<>', $id)->get();
        $added_set = $product->set_products->pluck('id')->toArray();

        return view('admin.products.edit')
            ->with('product', $product)
            ->with('categories', Categories::all())
            ->with('added_categories', $categories)
            ->with('sets', $sets)
            ->with('added_set', $added_set)
            ->with('labels', $product->labels())
            ->with('related', $product->related->pluck('id')->toArray())
            ->with('attributes', Attribute::all());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, Image $image)
    {
        $rules = $this->rules;
        $rules['url_alias'] = 'required|unique:categories|unique:products,url_alias,'.$id;

        $attributes_error = $this->validate_attributes($request->product_attributes);

        $validator = Validator::make($request->all(), $rules, $this->messages);

        if ($validator->fails() || $attributes_error) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator)
                ->with('attributes_error', $attributes_error);
        }

        $product_table_fill = $request->only([
            'name',
            'description',
            'image_id',
            'price',
            'old_price',
            'articul',
            'stock',
            'label',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'robots',
            'url_alias',
            'product_related_category_id'
        ]);

        $product = Products::find($id);

        if(is_null($product->gallery)){
            $gallery = new Gallery();
            $product_table_fill['gallery_id'] = $gallery->add_gallery($request->gallery);
        }else{
            $product->gallery->images = json_encode($request->gallery);
        }

        if(!empty($request->related)) {
            foreach (Products::whereIn('id', $request->related)->get() as $prod) {
                $r = [$product->id];
                foreach ($request->related as $rel_id) {
                    if ($rel_id != $prod->id)
                        $r[] = $rel_id;
                }
                $prod->related()->attach($r);
            }
        }
        $product->related()->sync($request->related);

        $product->fill($product_table_fill);

        $product->push();

        $product->categories()->sync($request->product_category_id);

        if (!empty($request->product_attributes)) {
            foreach ($request->product_attributes as $attribute) {
                $product_attributes[] = [
                    'product_id' => $product->id,
                    'attribute_id' => $attribute['id'],
                    'attribute_value_id' => $attribute['value'],
                ];
            }

            $product->attributes()->delete();
            $product->attributes()->createMany($product_attributes);
            $product->create_product_thumnail($product->id);
        }

        $this->updateVariations($product, $request->variations);

        return redirect('/admin/products')
            ->with('message-success', 'Товар ' . $product->name . ' успешно отредактирован.');
    }

    /**
     * Обновление вариаций
     *
     * @param $product
     * @param $variations
     */
    public function updateVariations($product, $variations){
        $current_variations = $product->variations;
        $add = [];
        $update = [];
        $remove = $current_variations->pluck(['id'])->toArray();
        if(!empty($variations)){
            foreach ($variations as $variation){
                $add_var = true;
                foreach ($current_variations as $var){
                    if(empty($variation['id'])){
                        $add_var = false;
                        break;
                    }
                    if($var->price == $variation['price']){
                        if(empty(array_diff($variation['id'], $var->attribute_values->pluck(['id'])->toArray()))){
                            $add_var = false;
                            unset($remove[array_search($var->id,$remove)]);
                            if($var->stock != $variation['stock']){
                                $update[$var->id] = $variation;
                            }
                            break;
                        }
                    }
                }
                if($add_var){
                    $add[] = $variation;
                }
            }
        }

        //foreach ($product->variations as $variation){
            //$variation->attribute_values()->detach();
            //$variation->delete();
            //$variation->update(['product_id' => 0]);
        //}

        foreach ($remove as $id){
            $v = new Variation();
            $v->find($id)->update(['product_id' => 0]);
        }
        foreach ($add as $variation){
            if(!empty($variation['price']) && !empty($variation['id'])){
                $v = new Variation();
                $id = $v->insertGetId(['product_id' => $product->id, 'price' => $variation['price'], 'stock' => $variation['stock']]);
                $v->find($id)->attribute_values()->attach($variation['id']);
            }
        }
        foreach ($update as $id => $variation){
            $v = Variation::where('id', $id);
            $v->update(['stock' => $variation['stock']]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Products::find($id);
        $product->delete();

        return redirect()->back()
            ->with('message-success', 'Товар ' . $product->name . ' успешно удален.');
    }

    /**
     * Получение списка всех атрибутов
     *
     * @param Attribute $attributes
     * @return string|void
     */
    public function getAttributes(Attribute $attributes)
    {

        $attr = $attributes->all();
        $response = [];

        if(!empty($attr)){
            foreach ($attr as $attribute) {
                $response[] = [
                    'attribute_id'    => $attribute->id,
                    'attribute_name'  => $attribute->name
                ];
            }
        }

        return json_encode($response);

    }

    /**
     * Получение списка значений переданного атрибута
     *
     * @param Attribute $attributes
     * @param Request $request
     * @return string|void
     */
    public function getAttributeValues(Attribute $attributes, Request $request)
    {

        $attribute = $attributes->find((int)$request->attribute_id);
        $response = [];

        if ($attribute !== null) {
            foreach ($attribute->values as $value) {
                $response[] = [
                    'attribute_value_id'    => $value->id,
                    'attribute_value'       => $value->name
                ];
            }
        }

        return json_encode($response);

    }

    /**
     * Живой поиск для модулей
     *
     * @param Request $request
     * @param Products $products
     * @return string|void
     */
    public function livesearch(Request $request, Products $products)
    {
        $products = $products->where('name', 'like', '%' . $request->search . '%')->with('image')->paginate(5);

        return view('public.layouts.search_results')->with('products', $products);
    }

    /**
     * @param $alias
     * @return mixed
     */
    public function show($alias, Request $request, Products $products)
    {
        $product = $products->where('url_alias', $alias)->first();

        if(empty($product)){
            abort(404);
        }

        setlocale(LC_TIME, 'RU');
        $reviews = $product->reviews()
            ->where('published', 1)
            ->orderBy('created_at', 'desc')
            ->get();

//        $product_reviews = [];
//        foreach ($reviews as $review) {
////            $review->date = iconv("cp1251", "UTF-8", $review->updated_at->formatLocalized('%d.%m.%Y'));
//            if(!is_null($review->parent_review_id)){
//                $product_reviews[$review->parent_review_id]['comments'][] = $review;
//            } else {
//                $product_reviews[$review->id]['parent'] = $review;
//            }
//        }

        // Если у товара нет галлереи возвращаем его изображение
        if(empty($product->gallery))
            $gallery = [$product->image];
        else
            $gallery = $product->gallery->objects();

        array_splice($gallery, 4);

        $attributes = [];

        foreach ($product->attributes as $attribute){
            if (!isset($attributes[$attribute->info->name])) {
                $attributes[$attribute->info->name] = [];
            }
            $attributes[$attribute->info->name][] = ['name' => $attribute->value->name, 'value' => $attribute->value->value];
        }

        $max_price = $product->price;
        $variations_attrs = [];
        $variations_prices = [];

        foreach($product->variations as $variation){
            $values = $variation->attribute_values;
            $variations_prices[implode('_', $values->pluck(['id'])->sort()->values()->all())] = [
                'price' => $variation->price,
                'id' => $variation->id
            ];
            if($max_price < $variation->price){
                $max_price = $variation->price;
            }
            foreach($values as $value){
                $attr = $value->attribute;
                if(!isset($variations_attrs[$attr->id])){
                    $variations_attrs[$attr->id] = [
                        'name' => $attr->name,
                        'values' => [
                            $value->id => ['name' => $value->name, 'stock' => $variation->stock]
                        ]
                    ];
                }elseif(!isset($variations_attrs[$attr->id]['values'][$value->id])){
                    $variations_attrs[$attr->id]['values'][$value->id] =  ['name' => $value->name, 'stock' => $variation->stock];
                }
            }

        }

        if(!empty($variations_attrs['7'])){
            ksort($variations_attrs['7']['values']);
        }

        $colors = [];

        if($product->colors->count()) {
            $colors[$product->id] = ['color' => $product->colors->first()->value, 'slug' => $product->url_alias];
            foreach ($product->related as $prod) {
                if ($prod->colors->count())
                    $colors[$prod->id] = ['color' => $prod->colors->first()->value, 'slug' => $prod->url_alias];
            }
            sort($colors);
        }

        $popular = $products->popular();

        if(!empty($product->categories()->first()))
            $root_category = $product->categories()->first()->get_root_category();
        else
            $root_category = false;

        return response(view('public.product')
            ->with('product', $product)
            ->with('root_category', $root_category)
            ->with('max_price', $max_price)
            ->with('gallery', $gallery)
//            ->with('reviews', $product_reviews)
            ->with('product_attributes', $attributes)
//            ->with('related', $product->related())
            ->with('variations_prices', $variations_prices)
            ->with('brand', $product->brand())
            ->with('colors', $colors)
            ->with('sizes', $product->sizes)
            ->with('popular', $popular)
            ->with('variations', $variations_attrs));
    }

    /**
     * Страница поиска
     *
     * @param Products $products
     * @param Request $request
     * @return mixed
     */
    public function search(Products $products, Request $request, $page = 'page1')
    {
        $search_text = urldecode($request->input('text'));

        //$id = $request->get('page', 1);

		 // Установка текущей страницы пагинации
        $request->page = (int) str_replace('page', '', $page);

        $data = $products->search($search_text, str_replace('page', '', $page), 18);

        if(method_exists($data, 'total')) {
            $paginator = $data->appends(['text' => $search_text]);
        } else {
            $paginator = false;
        }

        return view('public.search')->with('products', $data)
            ->with('paginator', $paginator)
            ->with('search_text', $search_text);
    }

    /**
     * Валидация атрибутов товара на одинаковые значения
     *
     * @param $attributes
     * @return bool|string
     */
    public function validate_attributes($attributes) {
        $attributes_error = false;

        if (!empty($attributes)) {
            foreach ($attributes as $product_attribute) {
                $product_attribute_values[] = $product_attribute['value'];
            }

            foreach (array_count_values($product_attribute_values) as $count_value) {
                if ($count_value > 1) {
                    $attributes_error = 'Значения атрибутов не могут быть одинаковы!';
                    break;
                }
            }
        }

        return $attributes_error;
    }

    /**
     * Импорт товаров
     *
     * @param Request $request
     * @param Products $products
     * @return mixed
     */
    public function upload(Request $request, Products $products)
    {
        $update = $request->input('update');
        $errors = false;

        if($request->hasFile('import_file')){
            $errors = [];
            $path = $request->file('import_file')->getRealPath();

            $data = Excel::load($path, function($reader) {

            })->get();

            if(!empty($data) && $data->count()){
                $prepared_data = [];

                foreach ($data as $row) {

                    $row_data = ['tables' => []];
                    foreach ($row as $key => $val){
                        $field = ['options' => $this->get_field_options($key)];

                        // Если данные для этой таблицы ещё не заполнялись
                        if(!isset($row_data['tables'][$field['options']['table']]))
                            $row_data['tables'][$field['options']['table']] = [];

                        // Если поле содержит несколько значений
                        if(isset($field['options']['selector'])){
                            $vals = explode($field['options']['selector'], $val);

                            // Обходим каждое значение отдельно
                            foreach ($vals as $result){
                                $new_row = [];

                                // Дополнительное поле
                                if(isset($field['options']['relations'])){
                                    $relation = preg_replace('/^([^{]+)\{([^}]+)\}/u', '$2', $result);
                                    if($relation != $result) {
                                        $new_row = array_merge($new_row, [$field['options']['relations'] => preg_replace('/^([^{]+)\{([^}]+)\}/u', '$2', $result)]);
                                        $result = trim(preg_replace('/^([^{]+)\{([^}]+)\}/u', '$1', $result));
                                    }
                                }

                                if(isset($field['options']['with_stock'])){
                                    if(strpos($result, ' * ') !== false){
                                        $params = explode(' * ', $result);
                                        if(count($params) == 2){
                                            $field['options']['with_stock'] = trim($params[0]);
                                            $result = trim($params[1]);
                                        }
                                    }
                                }

                                // Делаем необходимые подмены
                                if(isset($field['options']['replace']) && !empty($result)) {
                                    $result = $this->replace_inserted_data($result, $field['options']['replace']['table'], $field['options']['replace']['find'], $field['options']['replace']['replaced']);
                                }

                                // Подгружаем файлы
                                if(isset($field['options']['load']) && !empty($result)) {
                                    $url = $result;
                                    $table = $field['options']['load'][0];
                                    $replaced = $field['options']['load'][1];
                                    if($table == 'images'){
                                        $file = new ImagesController(new Image());
                                    }
                                    if(isset($file)){
                                        $file = $file->uploadFromUrlImages($url);
                                        if($file){
                                            $result = $file->$replaced;
                                        }else{
                                            $result = 1;
                                        }
                                    }
                                }

                                $new_row = array_merge($new_row, [$field['options']['field'] => $result]);
                                // Заполняем связанное поле
                                if(isset($field['options']['attached_fields'])){
                                    $new_row = array_merge($new_row, $field['options']['attached_fields']);
                                }

                                // Заполняем остатки
                                if(isset($field['options']['with_stock'])){
                                    $new_row['with_stock'] = $field['options']['with_stock'];
                                }

                                // Добавляем данные в общий поток
                                if($result !== '')
                                    $row_data['tables'][$field['options']['table']][] = $new_row;
                            }
                        }else{
                            if(isset($field['options']['replace']) && !empty($val)) {
                                $val = $this->replace_inserted_data($val, $field['options']['replace']['table'], $field['options']['replace']['find'], $field['options']['replace']['replaced']);
                            }

                            // Подгружаем файлы
                            if(isset($field['options']['load']) && !empty($val)) {
                                $url = $val;
                                $table = $field['options']['load'][0];
                                $replaced = $field['options']['load'][1];
                                if($table == 'images'){
                                    $file = new ImagesController(new Image());
                                }
                                if(isset($file)){
                                    $file = $file->uploadFromUrlImages($url);
                                    if($file){
                                        $val = $file->$replaced;
                                    }else{
                                        $val = 1;
                                    }
                                }
                            }

                            if(isset($field['options']['unique'])) {
                                $row_data['tables'][$field['options']['table']][$field['options']['field']] = $val;
                            }else {
                                $row_data['tables'][$field['options']['table']][] = [$field['options']['field'] => $val];
                            }
                        }
                    }

                    $row_data = $this->addSizesVariations($row_data);

                    $prepared_data[] = $row_data;
                }

                if($request->update){
                    $errors = $this->updatePricesAndStock($prepared_data);
                    return view('admin.products.upload')
                        ->with('errors', $errors);
                }

                $errors = $this->validate_prepared_data($prepared_data);

                if(empty($errors)) {
                    foreach ($prepared_data as $product) {
                        if ($update)
                            $products->update_product($product['tables']);
                        else{
                            $id = $products->insert_product($product['tables']);
                            if(!empty($product['tables']['variations']) && $id != 'already_exist'){
                                $this->updateVariations($products->find($id), $product['tables']['variations']);
                            }
                        }

                        if (isset($product['errors']))
                            $errors[] = $product;
                    }
                }else{
                    return view('admin.products.upload')
                        ->with('errors', $errors);
                }
            }

        }

        return view('admin.products.upload')
            ->with('errors', $errors);
    }

    /**
     * Добавление размеров в вариации
     *
     * @param $data
     * @return mixed
     */
    public function addSizesVariations($data){
        if(isset($data['tables']['product_attributes'])){
            $price = $data['tables']['products']['price'];
            $variations = [];
            foreach ($data['tables']['product_attributes'] as $key => $attr){
                if($attr['attribute_id'] == 7){
                    $variations[] = [
                        'id' => [$attr['attribute_value_id']],
                        'price' => $price,
                        'stock' => isset($attr['with_stock']) ? $attr['with_stock'] : 1
                    ];
                    if(isset($data['tables']['product_attributes'][$key]['with_stock'])){
                        unset($data['tables']['product_attributes'][$key]['with_stock']);
                    }
                }
            }
            $data['tables']['variations'] = $variations;
        }

        return $data;
    }


    public function updatePricesAndStock($data){
        $errors = [];
        foreach($data as $key => $row){
            if(empty($row['tables']['products']['articul'])){
                $errors[] = [
                    'id' => $key+1,
                    'errors' => ['Не указан артикул товара']
                ];
                break;
            }
            $product = Products::where('articul', $row['tables']['products']['articul'])->first();
            if(empty($product)){
                $errors[] = [
                    'id' => $key+1,
                    'errors' => ['Не удалось найти товар с артикулом: '.$row['tables']['products']['articul']]
                ];
                break;
            }
            $old_price = $product->price;
            if($old_price == $row['tables']['products']['price']){
                $old_price = $product->old_price;
            }elseif($old_price < $row['tables']['products']['price']){
                $old_price = 0;
            }

            $product->fill([
                'price' => $row['tables']['products']['price'],
                'old_price' => $old_price
            ]);

            $product->push();

            if (!empty($row['tables']['product_attributes'])) {
                $ids = [];
                foreach ($row['tables']['product_attributes'] as $attribute) {
                    $product_attributes[] = [
                        'product_id' => $product->id,
                        'attribute_id' => $attribute['attribute_id'],
                        'attribute_value_id' => $attribute['attribute_value_id'],
                    ];
                    if(!in_array($attribute['attribute_id'], $ids)){
                        $ids[] = $attribute['attribute_id'];
                    }
                }

                $product->attributes()->whereIn('attribute_id', $ids)->delete();
                $product->attributes()->createMany($product_attributes);
            }

            if(isset($row['tables']['variations'])){
                foreach ($row['tables']['variations'] as $key => $variation) {
                    $row['tables']['variations'][$key]['price'] = $row['tables']['products']['price'];
                }
                $this->updateVariations($product, $row['tables']['variations']);
            }
        }

        return $errors;
    }

    /**
     * Валидация импортируемых данных
     *
     * @param $prepared_data
     * @return array
     */
    public function validate_prepared_data($prepared_data){
        $products = new Products();
        $names = [];
        foreach ($products->select('name')->get() as $product){
            $names[] = $product->name;
        }
        $errors = [];
        foreach ($prepared_data as $id => $row){

            $err = [];

            if(empty($row['tables']['products']['name'])){
                $err[] = 'Не заполнено название товара.';
            }
//            if(in_array($row['tables']['products']['name'], $names)){
//                $err[] = 'Дубль названия товара.';
//            }
//            if(empty($row['tables']['products']['price'])){
//                $err[] = 'Не заполнена цена товара.';
//            }

            foreach ($row['tables']['galleries'] as $image){
                if(empty($image['images'])){
                    $err[] = 'Неизвестное изображение.';
                }
            }

//            foreach ($row['tables']['product_categories'] as $category){
//                if(empty($category['category_id'])){
//                    $err[] = 'Неизвестная категория.';
//                }
//            }

            if(isset($row['tables']['product_attributes'])){
                foreach ($row['tables']['product_attributes'] as $attribute){
                    if(empty($attribute['attribute_value_id'])){
                        $err[] = 'Неизвестное значение атрибута (id атрибута '.$attribute['attribute_id'].').';
                    }
                }
            }

            if(!empty($err)){
                $errors[] = [
                    'id' => $id+1,
                    'errors' => $err
                ];
            }
        }

        return $errors;
    }

    /**
     * Парсинг опций вставки
     *
     * @param $field
     * @return array|bool
     */
    public function get_field_options($field){
        $params = explode('.', $field);
        if(count($params) < 2)
            return false;
        $options = [
            'table' => $params[0],
            'field' => $params[1]
        ];
        $count = count($params);
        if($count > 2){
            for($i=2; $i<$count; $i++){
                if(strpos($params[$i], 'selector') === 0){
                    $options['selector'] = preg_replace('/selector\((.+)\)/', '$1', $params[$i]);
                }elseif(strpos($params[$i], 'attached_field') === 0){
                    if(!isset($options['attached_fields']))
                        $options['attached_fields'] = [];
                    $attached_field = explode(':', preg_replace('/attached_field\((.+)\)/', '$1', $params[$i]), 2);
                    $options['attached_fields'][$attached_field[0]] = $attached_field[1];
                }elseif($params[$i] == 'unique'){
                    $options['unique'] = true;
                }elseif($params[$i] == 'with_stock'){
                    $options['with_stock'] = 1;
                }elseif(strpos($params[$i], 'replace') === 0){
                    if(!isset($options['attached_fields']))
                        $options['attached_fields'] = [];
                    $replace = explode(':', preg_replace('/replace\((.+)\)/', '$1', $params[$i]), 3);
                    $options['replace'] = ['table' => $replace[0], 'find' => $replace[1], 'replaced' => $replace[2]];
                }elseif(strpos($params[$i], 'relations') === 0){
                    $options['relations'] = preg_replace('/relations\((.+)\)/', '$1', $params[$i]);
                }elseif(strpos($params[$i], 'load') === 0){
                    $options['load'] = explode(':', preg_replace('/load\((.+)\)/', '$1', $params[$i]), 2);
                }
            }
        }

        return $options;
    }

    /**
     * Получение одного поля таблицы по другому
     *
     * @param $data
     * @param $table
     * @param $find
     * @param $replaced
     * @return mixed
     */
    public function replace_inserted_data($data, $table, $find, $replaced){
        $model_name = 'App\Models\\'.str_replace(' ', '', ucwords(str_replace('_', ' ', preg_replace('/s$/', '', $table))));

        if(!class_exists($model_name))
            $model_name = 'App\Models\\'.str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
        if(!class_exists($model_name) || $data == '')
            return null;

        $table = new $model_name;
        $result = $table->select($replaced)->where($find, '=', trim($data))->take(1)->get()->first();

        return $result !== null ? $result->$replaced : $result;
    }

    /**
     * Удаление нежелательных символов
     *
     * @param $value
     */
    function trim_value(&$value)
    {
        if(is_string($value)) {
            $value = preg_replace('/(^"|"$|;$|\.$|,$|,\s?,)/', '', preg_replace('@^\s*|\s*$@u', '', $value));
        }
    }

    /**
     * Транслит
     * @param $string
     * @return mixed
     */
    public function rus2lat($string)
    {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => "",  'ы' => 'y',   'ъ' => "",
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => "",  'Ы' => 'Y',   'Ъ' => "",
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    public function groupAction(Request $request, Products $products, Categories $categories){
		if(in_array($request->action, ['enable', 'disable', 'change_category', 'add_category', 'change_price'])){
			$products = $products->whereIn('id', $request->products);
			if($request->action == 'enable'){
				$products->update(['stock' => 1]);
			}elseif($request->action == 'disable'){
				$products->update(['stock' => 0]);
			}elseif($request->action == 'change_category'){
				foreach($products->get() as $product){
					$product->categories()->sync($request->category);
				}
			}elseif($request->action == 'add_category'){
				foreach($products->get() as $product){
					$product->categories()->attach($request->category);
				}
			}elseif($request->action == 'change_price'){
				$percent = 1 + (float)str_replace(['%', ','], ['', '.'], $request->percent)/100;
				Products::where('id', '>=', 1)->update(['price' => DB::raw("CEIL(price * $percent)")]);
			}
		}
    }

    public function syncProducts(){
	    $sync = new Sync();
        $sync->initSync();
	    echo '<script>location = location;</script>';
//        $sync->getAllFiles();
//        $sync->parseFiles();
//	      $sync->getToystoreFile();
//	      $sync->parseToystoreFile();
    }
}
