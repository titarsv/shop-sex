<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Log;

class Newpost extends Model
{

    /**
     * Запрос в API Новой почты
     *
     * @param $parameters
     * @return bool|mixed|void
     */
    public function requestToAPI($parameters)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/json/');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if($response['success'] == true) {
            return $response;
        }

        return false;
    }

    /**
     * Обновление всех данных из API
     *
     * @return bool
     */
    public function updateAll()
    {
        if($this->getRegions(true) && $this->getAllCities(time()) && $this->getAllWarehouses(time())){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Получение списка областей из БД или из API
     *
     * @param bool $force
     * @return mixed
     */
    public function getRegions($force = false)
    {
        // TODO забить русские наименования областей массивом и включить обновление
//        $setting = new Settings;
//        $newpost = $setting->get_setting('newpost_regions_last_update');
//        $update_period = $setting->get_setting('newpost_regions_update_period');
//        $time = time();

//        if(empty($newpost->newpost_regions_last_update) || ($newpost->newpost_regions_last_update + $update_period) < $time || $force){
//
//            $parameters = [
//                'modelName' => 'Address',
//                'calledMethod' => 'getAreas',
//                'apiKey' => $setting->get_setting('newpost_api_key')
//            ];
//
//            $result = $this->requestToAPI($parameters);
//
//            $regions = [];
//            if(!empty($result['success'])) {
//                foreach ($result['data'] as $region) {
//                    $regions[] = [
//                        'region_id' => $region['Ref'],
//                        'name' => $region['Description'],
//                        'region_center' => $region['AreasCenter']
//                    ];
//                }
//                DB::table('newpost_regions')->truncate();
//                DB::table('newpost_regions')->insert($regions);
//
//                $setting->update_setting('newpost_regions_last_update', $time);
//            } elseif (!empty($result['error'])) {
//                Log::error('Ошибка API Новой Почты:', $result['error']);
//            }
//        }

        return DB::table('newpost_regions')->get();
    }

    /**
     * Получение информации об области из БД
     *
     * @param $region_id
     * @return bool
     */
    public function getRegionRef($region_id)
    {
        $result = DB::table('newpost_regions')->find($region_id);

        if ($result){
            return $result;
        }
        return false;
    }

    /**
     * Получение списка всех городов из API
     *
     * @param $time
     * @return bool
     */
    public function getAllCities($time)
    {
        $setting = new Settings;
        $parameters = [
            'modelName' => 'Address',
            'calledMethod' => 'getCities',
            'apiKey' => $setting->get_setting('newpost_api_key')
        ];

        $result = $this->requestToAPI($parameters);

        $cities = [];
        if(!empty($result['success'])) {
            foreach ($result['data'] as $city) {
                $cities[] = [
                    'city_id'   => $city['Ref'],
                    'name_ua'   => $city['Description'],
                    'name_ru'   => $city['DescriptionRu'],
                    'region_id' => $city['Area']
                ];
            }
            DB::table('newpost_cities')->truncate();
            DB::table('newpost_cities')->insert($cities);

            $setting->update_setting('newpost_cities_last_update', $time);
            return true;
        } elseif (!empty($result['error'])) {
            Log::error('Ошибка API Новой Почты:', $result['error']);
        }
        return false;
    }

    /**
     * Получение списка городов области по id области
     *
     * @param $region_id
     * @return array
     */
    public function getCities($region_id)
    {
        $setting = new Settings;
        $last_update =  $setting->get_setting('newpost_cities_last_update');
        if(empty($last_update)){
            $last_update = config('newpost.cities_last_update');
        }
        $time = time();

        if(is_null($last_update) || ($last_update + 2592000) < $time){
            $this->getAllCities($time);
        }

        $result = DB::table('newpost_cities')->where('region_id', $region_id)->get();

        if ($result) {
            return $result;
        }
        return [];
    }

    /**
     * Получение информации о городе по его id
     *
     * @param $city_id
     * @return bool
     */
    public function getCityRef($city_id)
    {
        $result = DB::table('newpost_cities')->find($city_id);

        if ($result){
            return $result;
        }
        return false;
    }

    /**
     * Получение списка всех отделений Новой Почты из API
     *
     * @param $time
     * @return bool
     */
    public function getAllWarehouses($time)
    {
        $setting = new Settings;
        $parameters = [
            'modelName' => 'Address',
            'calledMethod' => 'getWarehouses',
            'apiKey' => $setting->get_setting('newpost_api_key')
        ];

        $result = $this->requestToAPI($parameters);

        $warehouses = [];
        if(!empty($result['success'])) {
            foreach ($result['data'] as $warehouse) {
                $warehouses[] = [
                    'warehouse_id'  => $warehouse['Ref'],
                    'address_ua'    => $warehouse['Description'],
                    'address_ru'    => $warehouse['DescriptionRu'],
                    'number'        => $warehouse['Number'],
                    'city_id'       => $warehouse['CityRef'],
                    'phone'         => $warehouse['Phone']
                ];
            }
            DB::table('newpost_warehouses')->truncate();
            DB::table('newpost_warehouses')->insert($warehouses);

            $setting->update_setting('newpost_warehouses_last_update', $time);
            return true;
        } elseif (!empty($result['error'])) {
            Log::error('Ошибка API Новой Почты:', $result['error']);
        }
        return false;
    }

    /**
     * Получение списка всех отделений города по его id
     *
     * @param $city_id
     * @return array
     */
    public function getWarehouses($city_id)
    {
        $setting = new Settings;
        $last_update =  $setting->get_setting('newpost_warehouses_last_update');
        if(empty($last_update)){
            $last_update = config('newpost.warehouses_last_update');
        }
        $time = time();

        if(is_null($last_update) || ($last_update + 604800) < $time){
            $this->getAllWarehouses($time);
        }

        $result = DB::table('newpost_warehouses')->where('city_id', $city_id)->get();

        if ($result) {
            return $result;
        }
        return [];
    }

    /**
     * Получение информации об отделении по его id
     *
     * @param $warehouse_id
     * @return bool
     */
    public function getWarehouse($warehouse_id)
    {
        $result = DB::table('newpost_warehouses')->find($warehouse_id);

        if ($result){
            return $result;
        }
        return false;
    }
}
