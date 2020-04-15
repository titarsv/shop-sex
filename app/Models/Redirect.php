<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'old_url',
        'new_url',
    ];

    protected $table = 'redirects';
	public $timestamps = false;
}
