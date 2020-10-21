<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

class Seo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical',
        'robots',
        'url',
    ];

    protected $dates = ['deleted_at'];

    protected $table = 'seo';

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

    public function getDescriptionAttribute(){
        return $this->getAttributeByName('description');
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
}
