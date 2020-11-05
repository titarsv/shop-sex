<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Date\DateFormat;
use App;

class News extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'news';

    public $fillable = [
        'user_id',
        'url_alias',
        'title',
        'subtitle',
        'text',
        'published',
        'image_id',
        'category',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'robots'
    ];

    public function getCreatedAtAttribute($attr)
    {
        return DateFormat::post($attr);
    }

    public function getUpdatedAtAttribute($attr)
    {
        return DateFormat::post($attr);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function image()
    {
        return $this->belongsTo('App\Models\Image');
    }

    public function localization(){
        return $this->morphMany('App\Models\Localization', 'localizable');
    }

    public function saveLocalization($request){
        $localization = new Localization();
        $localization->saveLocalization($request, $this, localizationFields(['title', 'text', 'meta_title', 'meta_description', 'meta_keywords']));
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

    public function getTitleAttribute(){
        return $this->getAttributeByName('title');
    }

    public function getTextAttribute(){
        return $this->getAttributeByName('text');
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

    /**
     * Получение случайных постов
     * @param $count
     * @param $exclusion
     * @return mixed
     */
    public function get_recommended($count, $exclusion = 0){
        return $this->where('published', true)
            ->take($count)
            ->whereNotIn('id', array($exclusion))
            ->inRandomOrder()
            ->get();
    }

    public function next(){
        return $this->where('published', true)
            ->where('id', '>', $this->id)
            ->first();
    }

    public function prev(){
        return $this->where('published', true)
            ->where('id', '<', $this->id)
            ->first();
    }
}