<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class AttributeValues extends Model
{

    protected $fillable = [
        'attribute_id',
        'name',
        'image_href'
    ];

    protected $table = 'attribute_values';
    public $timestamps = false;

    public function attribute()
    {
        return $this->belongsTo('App\Models\Attribute', 'id');
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
}
