<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

class HTMLContent extends Model
{
    protected $table = 'html_content';
    protected $fillable = [
        'name',
        'url_alias',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'robots',
        'content',
        'parent_id',
        'status',
        'sort_order'
    ];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function children(){
        return $this->hasMany('App\Models\HTMLContent', 'parent_id', 'id')->with('children');
    }

    public function parent(){
        return $this->belongsTo('App\Models\HTMLContent', 'parent_id');
    }

    public function localization(){
        return $this->morphMany('App\Models\Localization', 'localizable');
    }

    public function saveLocalization($request){
        $localization = new Localization();
        $localization->saveLocalization($request, $this, localizationFields(['name', 'content', 'meta_title', 'meta_description', 'meta_keywords']));
    }

    public function localize($language, $field){
        $localization = $this->localization->first(function ($value, $key) use ($language, $field){
            return $value->language == $language && $value->field == $field;
        });
        if(empty($localization))
            $localization = $this->localization()->where(['language' => $language, 'field' => $field])->first();

        if(empty($localization)) {
            $value = $language == 'ru' && isset($this->attributes[$field]) ? $this->attributes[$field] : '';
        }else{
            $value = $localization->value;
        }

        if(\Request::segment( 1 ) == 'admin'){
            $value = preg_replace('/<source.*?data-src="(.*?)" type="image\/(.*?)" \/>/s', '<source srcset="$1" type="image/$2" />', $value);
            return $value;
        }

        return str_replace('editor-image', '', $value);
    }

    private function getAttributeByName($name){
        return $this->localize(App::getLocale(), $name);
    }

    public function getNameAttribute(){
        return $this->getAttributeByName('name');
    }

    public function getContentAttribute(){
        return $this->getAttributeByName('content');
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

    public function hasChildren(){
        if($this->where('parent_id', $this->id)->count()){
            return true;
        }else
            return false;
    }

    public function get_parent_pages($page = ''){
        $pages = [];

        if(!empty($page)){
            if(is_int($page)){
                $page = $this->where('id', $page)->first();
            }elseif(is_string($page)){
                $page = $this->where('url_alias', $page)->first();
            }
        }else{
            $page = $this;
        }

        $pages[] = $page;
        if($page->parent_id > 0)
            $pages = array_merge ($pages, $this->get_parent_pages($page->parent_id));

        return $pages;
    }
}
