<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
