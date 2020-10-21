<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use App\Models\Products;
use Illuminate\Pagination\Paginator;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use App\Models\Localization;
use App;

class Categories extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical',
        'robots',
        'url_alias',
//        'related_attribute_id',
        'parent_id',
        'sort_order',
        'status'
    ];

    protected $dates = ['deleted_at'];

    protected $table = 'categories';

    public function image()
    {
        return $this->hasOne('App\Models\Image', 'id', 'image_id');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Products', 'product_categories', 'category_id', 'product_id');
    }

    public function attributes()
    {
        return $this->belongsToMany('App\Models\Attribute', 'category_attributes', 'category_id', 'attribute_id');
    }

    public function children(){
        return $this->hasMany('App\Models\Categories', 'parent_id', 'id')->with('children');
    }

    public function localization(){
        return $this->morphMany('App\Models\Localization', 'localizable');
    }

    public function saveLocalization($request){
        $localization = new Localization();
        $localization->saveLocalization($request, $this, localizationFields(['name', 'description', 'meta_title', 'meta_description', 'meta_keywords']));
    }

    public function localize($language, $field){
        $localization = $this->localization()->where(['language' => $language, 'field' => $field])->first();
        if(empty($localization)) {
            return $language == 'ru' && isset($this->attributes[$field]) ? $this->attributes[$field] : '';
        }else{
            return $localization->value;
        }
    }

    private function getAttributeByName($name){
        $localization = $this->localization->where('language', App::getLocale())->where('field', $name)->first();
        if(empty($localization)){
            return isset($this->attributes[$name]) ? $this->attributes[$name] : '';
        }else{
            return $localization->value;
        }
    }

    public function getNameAttribute(){
        return $this->getAttributeByName('name');
    }

    public function getMetaTitleAttribute(){
        return $this->getAttributeByName('meta_title');
    }

    public function getMetaDescriptionAttribute(){
        return $this->getAttributeByName('meta_description');
    }

    public function getMetaKeywordsAttribute(){
        return $this->getAttributeByName('meta_keywords');
    }

    public function get_products($category_id, $subcategory_id, $filter, $sort, $take = false, $price = [], $page = 1)
    {
        $orderBy = $sort[0];
        $route = $sort[1];

        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        $products = Products::select('products.*');

        $products->where('stock', 1)->with('image', 'attributes');

        if($category_id !== null) {
            $categories = [];
            if(is_array($category_id)){
                foreach ($category_id as $id){
                    $categories = array_merge($categories, [$id], $this->get_children_categories($id));
                }
            }else
                $categories = array_merge([$category_id], $this->get_children_categories($category_id));
            $products->join('product_categories AS cat', 'products.id', '=', 'cat.product_id');
            $products->whereIn('cat.category_id', $categories);
        }

        if($subcategory_id !== null) {
            $categories = array_merge([$subcategory_id], $this->get_children_categories($subcategory_id));
            $products->join('product_categories AS sub_cat', 'products.id', '=', 'sub_cat.product_id');
            $products->whereIn('sub_cat.category_id', $categories);
        }

        if (!empty($filter)) {

            foreach ($filter as $key => $attribute) {

                $products->join('product_attributes AS attr' . $key, 'products.id', '=', 'attr' . $key . '.product_id');
                $products->where('attr' . $key . '.attribute_id', $key);
                $products->where(function($query) use($attribute, $key){

                    foreach ($attribute as $attribute_value) {
                        $query->orWhere('attr' . $key . '.attribute_value_id', $attribute_value);
                    }
                });

            }
        }

        if(!empty($price)){
            $products->whereBetween('products.price', $price);
        }

        $products->orderBy($orderBy, $route);
        $products->groupBy('products.id');
        $products->with('image', 'sizes', 'colors', 'related.colors', 'related.image');

        $user = Sentinel::check();
        if(!empty($user)){
            $products->with(['wishlist' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);
        }

        if(!$take){
            return $products->paginate(12);
        } else {
            return $products->paginate($take);
            //return $products->take($take)->get();
        }

    }

    public function get_products_count($category_id, $filter, $price = [], $limit = 0)
    {
        $hash = md5($category_id.serialize($filter).serialize($price));

        $count = Cache::remember($hash, 480, function () use (&$category_id, $filter, $price, $limit) {
            $products = Products::select('products.*');

            $products->where('stock', 1);

            if($category_id !== null) {
                $categories = [];
                if(is_array($category_id)){
                    foreach ($category_id as $id){
                        $categories = array_merge($categories, [$id], $this->get_children_categories($id));
                    }
                }else
                    $categories = array_merge([$category_id], $this->get_children_categories($category_id));
                $products->join('product_categories AS cat', 'products.id', '=', 'cat.product_id');
                $products->whereIn('cat.category_id', $categories);
            }

            if (!empty($filter)) {

                foreach ($filter as $key => $attribute) {

                    $products->join('product_attributes AS attr' . $key, 'products.id', '=', 'attr' . $key . '.product_id');
                    $products->where('attr' . $key . '.attribute_id', $key);
                    $products->where(function($query) use($attribute, $key){

                        foreach ($attribute as $attribute_value) {
                            $query->orWhere('attr' . $key . '.attribute_value_id', $attribute_value);
                        }
                    });

                }
            }

            if(!empty($price)){
                $products->whereBetween('products.price', $price);
            }

            $products->groupBy('products.id');

            if(!empty($limit)){
                $products->limit($limit);
            }

            return $products->count();
        });

        return $count;
    }

    public function get_children_categories($cat_id){

        if(isset($this->{'children_categories_'.$cat_id})){
            $categories = $this->{'children_categories_'.$cat_id};
        }else{
            $categories = Cache::remember('children_categories_'.$cat_id, 60, function () use (&$cat_id) {
                $children_categories = $this->select('id')->where('parent_id', $cat_id)->get()->toArray();
                $categories = [];
                if(count($children_categories)) {
                    foreach ($children_categories as $cat) {
                        $categories[] = $cat['id'];
                        $categories = array_merge ($categories, $this->get_children_categories($cat['id']));
                    }
                }
                return $categories;
            });

            $this->{'children_categories_'.$cat_id} = $categories;
        }

        return $categories;
    }

    /**
     * Минимальная стоимость товара в категории
     *
     * @param $category_id
     * @return int
     */
    public function min_price($category_id){

        if(isset($this->{'min_price_'.$category_id})){
            $price = $this->{'min_price_'.$category_id};
        }else{
            $price = Cache::remember('min_price_'.$category_id, 60, function () use (&$category_id) {
                $product = Products::select('products.price');
                $categories = array_merge([$category_id], $this->get_children_categories($category_id));
                $product->join('product_categories AS cat', 'products.id', '=', 'cat.product_id');
                $product->whereIn('cat.category_id', $categories);
                $result = $product->orderBy('products.price', 'asc')
                    ->first();

                if(is_null($result)){
                    return 0;
                }else{
                    return $result->price;
                }
            });

            $this->{'min_price_'.$category_id} = $price;
        }

        return $price;
    }

    /**
     * Максимальная стоимость товара в категории
     *
     * @param $category_id
     * @return int
     */
    public function max_price($category_id){

        if(isset($this->{'max_price_'.$category_id})){
            $price = $this->{'max_price_'.$category_id};
        }else{
            $price = Cache::remember('max_price_'.$category_id, 60, function () use (&$category_id) {
                $product = Products::select('products.price');
                $categories = array_merge([$category_id], $this->get_children_categories($category_id));
                $product->join('product_categories AS cat', 'products.id', '=', 'cat.product_id');
                $product->whereIn('cat.category_id', $categories);
                $result = $product->orderBy('products.price', 'desc')
                    ->first();

                if(is_null($result)){
                    return 0;
                }else{
                    return $result->price;
                }
            });

            $this->{'max_price_'.$category_id} = $price;
        }

        return $price;
    }

    /**
     * Популярные категории
     *
     * @param $count
     * @return mixed
     */
    public function get_popular($count){
        //TODO Добавить условие популярности категорий
        $categories = $this->take($count)
            ->get();

        return $categories;
    }

    /**
     * Корневые категории
     * @return mixed
     */
    public function get_root_categories(){
        $categories = $this->where('parent_id', 5)
            ->where('status', '1')
            ->orderBy('sort_order', 'ASC')
            ->with('children')
            ->with('image')
            ->get();

        return $categories;
    }
    
    /**
     * Дочерние категории
     * @param int $parent_id
     * @return mixed
     */
    public function get_children($parent_id = 0){

        $children = Cache::remember('children_categories_objects_'.$parent_id, 60, function () use (&$parent_id) {
            if(empty($parent_id))
                $parent_id = $this->id;
            $children = $this->where('parent_id', $parent_id)
                ->orderBy('name', 'DESC')
                ->get();
            return $children;
        });

        return $children;
    }

    /**
     * Наличие дочерних категорий
     * @return bool
     */
    public function hasChildren(){
        if($this->where('parent_id', $this->id)->count()){
            return true;
        }else
            return false;
    }

    /**
     * Получение категории по алиасу
     * @param $alias
     * @return mixed
     */
    public function getByAlias($alias){
        return $this->where('url_alias', $alias)->first();
    }

    /**
     * Получение массива родительских категорий
     * @param string $category
     * @return array
     */
    public function get_parent_categories($category = ''){
        $categories = [];

        if(!empty($category)){
            if(is_int($category)){
                $category = $this->where('id', $category)->first();
            }elseif(is_string($category)){
                $category = $this->where('url_alias', $category)->first();
            }
        }else{
            $category = $this;
        }

        $categories[] = $category;
        if($category->parent_id > 0)
            $categories = array_merge ($categories, $this->get_parent_categories($category->parent_id));

        return $categories;
    }

    public function get_root_category(){
        $categories = $this->get_parent_categories($this->id);

        if(count($categories) > 1)
            return $categories[count($categories) - 2];
        else
            return $this;
    }

    public function all_categories_with_parent_name(){
        return $this->select('categories.*', 'p.name AS parent_name')
            ->leftJoin('categories AS p', 'categories.parent_id', '=', 'p.id')
            ->get();
    }
}
