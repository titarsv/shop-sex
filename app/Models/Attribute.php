<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

class Attribute extends Model
{
    protected $fillable = [
        'name',
        'slug'
//        'enable_image_overlay',
//        'image_overlay_settings'
    ];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'attributes';

    public function values(){
        return $this->hasMany('App\Models\AttributeValues', 'attribute_id');
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

    public function get_products_attributes($category_id) {
        if($category_id)
            $products = Products::select('products.id')->where('product_category_id', $category_id)->get();
        else
            $products = Products::select('products.id')->get();

        $product_attributes = ProductAttributes::select('attribute_id')->whereIn('product_id', $products)->distinct()->get();
        return $this->whereIn('id', $product_attributes)->get();
    }

    public function get_products_values($category_id) {

    }
}
