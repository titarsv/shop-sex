<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulebanners extends Model
{
    protected $table = 'module_banners';
    protected $fillable = [
        'image_id',
        'sort_order',
        'link',
        'enable_link',
        'slide_data',
        'status'
    ];

    public function image()
    {
        return $this->hasOne('App\Models\Image', 'id', 'image_id');
    }

    public function data(){
        return json_decode($this->slide_data);
    }
}
