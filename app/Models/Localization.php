<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localization extends Model
{
	protected $table = 'localization';

	protected $fillable = [
		'field',
		'language',
		'value',
		'localizable_type',
		'localizable_id',
	];

	public function localizable() {
		return $this->morphTo();
	}

	public function saveLocalization($request, $parent, $fields = []){
		$langs = [
			'_ru' => 'ru',
			'_ua' => 'ua',
			'_en' => 'en'
		];
		$localizations = [];
		foreach ($request->only($fields) as $key => $val){
            $lang = substr($key, -3);
            if(isset($langs[$lang])){
                $lang = $langs[$lang];
                $key = substr($key, 0, -3);
            }else{
                $lang = 'ru';
            }
            $localization_data = [
                'language' => $lang,
                'field' => $key,
                'value' => $val
            ];
            if(!isset($localizations[$lang])){
                $localizations[$lang] = [];
            }
            if(!isset($localizations[$lang][$key]))
                $localizations[$lang][$key] = $parent->localization()->where('language', $lang)->where('field', $key)->first();
            if(empty($localizations[$lang][$key])){
                $parent->localization()->create($localization_data);
            }else{
                $localizations[$lang][$key]->fill($localization_data)->save();
            }
		}
	}
}
