<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Date\DateFormat;

class News extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'news';

    public $categories = [
        1 => 'Новости и акции',
        2 => 'Статьи',
        3 => 'Уход за обувью'
    ];

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