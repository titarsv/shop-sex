<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserData;

class Order extends Model
{
    public $table = 'orders';
    protected $fillable = [
        'user_id',
        'products',
        'total_price',
        'total_quantity',
        'status_id',
        'user_info',
        'delivery',
        'payment'
    ];

    /**
     * Связь с моделью статусов заказа
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\OrderStatus', 'status_id');
    }

    /**
     * Получение незавершенного заказа по id пользователя
     *
     * @param $user
     * @return int
     */
    public function getCurrentIncompleteOrder($user)
    {
        if ($user) {
            $order = $this->where('user_id', $user->id)->where('status_id', 0)->first();

            if (!is_null($order)) {
                return $order->id;
            }
        }
        return 0;
    }

    /**
     * Товары в заказе
     *
     * @return array
     */
    public function getProducts()
    {
        $products = json_decode($this->products, true);
        $result = [];

        foreach ($products as $product_code => $data) {
            $variation_attrs = [];
            if(!empty($data['variation'])){
                $v = new Variation();
                $variation = $v->find($data['variation']);
                $values = $variation->attribute_values;
                foreach($values as $value){
                    $attr = $value->attribute;
                    if(!isset($variation_attrs[$attr->name])){
                        $variation_attrs[$attr->name] = $value->name;
                    }
                }
            }

            $product_vars = explode('_', $product_code);
            $result[] = [
                'product'   => Products::where('id', $product_vars[0])->with('attributes.value', 'image')->first(),
                'quantity'  => $data['quantity'],
                'variations'  => $variation_attrs,
                'price'  => $data['price']
            ];
        }

        return $result;
    }

    /**
     * Информация о доставке в удобночитаемом формате
     *
     * @return array
     */
    public function getDeliveryInfo()
    {
        $delivery_info = json_decode($this->delivery, true);
        if ($delivery_info['method'] == 'newpost') {
            $newpost = new Newpost();
            $result = [
                'method'    => 'Новая Почта'
            ];
            $data = UserData::where('user_id', $this->user_id)->first();
            if(!empty($data)){
                $address = $data->address();
                if(!empty($address->npregion))
                    $result['region'] = $newpost->getRegionRef($address->npregion)->name_ru;
                if(!empty($address->npcity))
                    $result['city'] = $newpost->getCityRef($address->npcity)->name_ru;
                if(!empty($address->npdepartment))
                    $result['warehouse'] = $newpost->getWarehouse($address->npdepartment)->address_ru;
            }
            return $result;
        } elseif ($delivery_info['method'] == 'ukrpost') {
            $delivery_info['info']['method'] = 'Укрпочта';
            return $delivery_info['info'];
        } elseif ($delivery_info['method'] == 'courier') {
            $result = [
                'method' => 'Доставка курьером по Харькову'
            ];
            $data = UserData::where('user_id', $this->user_id)->first();
            if(!empty($data)){
                $address = $data->address();
                foreach ($address as $key => $val){
                    $result[$key] = $val;
                }
            }
            return $result;
        } elseif ($delivery_info['method'] == 'pickup') {
            return [
                'method' => 'Самовывоз'
            ];
        } elseif ($delivery_info['method'] == 'samovivoz_hol_gor') {
            return [
                'method' => 'Самовывоз Холодная Гора'
            ];
        } elseif ($delivery_info['method'] == 'samovivoz_nauki') {
            return [
                'method' => 'Самовывоз проспект Науки'
            ];
        }

        return [
            'error' => 'Невозможно отобразить информацию о доставке!'
        ];
    }
}
