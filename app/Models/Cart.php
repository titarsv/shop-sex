<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use App\Models\Variation;

class Cart extends Model
{
    protected $table = 'cart';
    protected $fillable = [
        'user_id',
        'user_ip',
        'session_id',
        'products',
        'total_quantity',
        'total_price',
        'total_sale',
        'user_data',
        'cart_data'
    ];

    /**
     * Получение корзины пользователя
     *
     * @param bool $create - Создавать новую корзину или нет
     * @return mixed
     */
    public function current_cart($create = true){

        $user = Sentinel::check();

        if($user) {
            $user_id = $user->id;
            $cart = $this->where('user_id', $user_id)->first();

            if(!is_null($cart) && $cart->session_id != Session::getId())
                $cart->update(['session_id' => Session::getId()]);
        } else {
            $user_id = 0;
            $cart = $this->where('session_id', Session::getId())->first();
        }

        if(is_null($cart) && $create) {
            $cart = $this->create_cart($user_id);
        }

        return $cart;
    }

    /**
     * Создание новой корзины
     *
     * @param $user_id
     * @return mixed
     */
    public function create_cart($user_id){

        $id = $this->insertGetId([
            'user_id' => $user_id,
            'session_id' => Session::getId(),
            'products' => null,
            'total_quantity' => 0,
            'total_price' => 0,
            'total_sale' => 0
        ]);

        return $this->where('id', $id)->first();
    }

    /**
     * Обновление корзины
     */
    public function update_cart(){
        $products = json_decode($this->products, true);

        $total_quantity = 0;
        $total_price = 0;
        $total_sale = 0;

        foreach ($products as $product_id => $data) {
            $total_quantity += $data['quantity'];
            $total_price += (float)$data['price'] * (int)$data['quantity'];
            $total_sale += (float)$data['sale'] * (int)$data['quantity'];
        }

        $this->update(['total_quantity' => $total_quantity, 'total_price' => $total_price, 'total_sale' => $total_sale]);

    }

    public function full_cart_update(){
        $products = json_decode($this->products, true);
        if(is_null($products))
            $products = [];
//        dd($products);
        foreach ($products as $id => $data){
            $ids = explode('_', $id);
            $product_id = (int)$ids[0];
            $variation = isset($ids[1]) ? (int)$ids[1] : 0;
            $price = $this->get_product_price($product_id, $variation);
            $products[$id]['price'] = $price['price'];
            $products[$id]['sale'] = $price['sale'];
            $products[$id]['sale_percent'] = $price['sale_percent'];
        }

        $this->update(['products' => json_encode($products)]);
        $this->update_cart();
    }

    /**
     * Получение продуктов из корзины
     *
     * @return array
     */
    public function get_products()
    {
        $current_cart = $this->current_cart();
        $products_in_cart = json_decode($current_cart->products, true);
        $products = [];

        if(!is_null($products_in_cart)) {
            foreach ($products_in_cart as $product_id => $data) {
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

                $ids = explode('_', $product_id);
                $products[$product_id] = [
                    'product'   => Products::where('id', $ids[0])->with('attributes.value')->first(),
                    'quantity'  => $data['quantity'],
                    'variations'  => $variation_attrs,
                    'price'  => $data['price'],
                    'sale' => $data['sale'],
                    'sale_percent' => $data['sale_percent']
                ];
            }
        }
        
        return $products;
    }

    /**
     * Код товара
     *
     * @param $product_id
     * @param $variation
     * @return string
     */
    private function get_product_code($product_id, $variation){
        if(!empty($variation)){
//            foreach ($variations as $key => $val){
//                $product_id .= '_'.$key.'-'.$val;
//            }
            $product_id .= '_'.$variation;
        }
        return $product_id;
    }

    /**
     * Добавление товара в корзину
     *
     * @param $product_id
     * @param int $quantity
     * @param $variation
     */
    public function add_product($product_id, $quantity = 1, $variation){
        $product_code = $this->get_product_code($product_id, $variation);

        $products = json_decode($this->products, true);
        if(is_null($products))
            $products = [];

        if(array_key_exists($product_code, $products)){
            $products[$product_code] = ['quantity' => $products[$product_id] + $quantity];
        } else {
            $products[$product_code] = ['quantity' => $quantity];
            if(!empty($variation)){
                $products[$product_code]['variation'] = $variation;
            }
            $price = $this->get_product_price($product_id, $variation);
            $products[$product_code]['price'] = $price['price'];
            $products[$product_code]['sale'] = $price['sale'];
            $products[$product_code]['sale_percent'] = $price['sale_percent'];
        }

        $this->update(['products' => json_encode($products)]);
        $this->update_cart();
    }

    /**
     * Удаление товара из корзины
     *
     * @param $product_code
     */
    public function remove_product($product_code){
        $products = json_decode($this->products, true);
        unset($products[$product_code]);

        $this->update(['products' => json_encode($products)]);
        $this->update_cart();
    }

    /**
     * Изменение колличества товара в корзине на указанную величину
     *
     * @param $product_id
     * @param $delta
     * @param $variation
     */
    public function increment_product_quantity($product_id, $delta, $variation = 0){
        $product_code = $this->get_product_code($product_id, $variation);

        if ($this->product_isset($product_code)) {

            $products = json_decode($this->products, true);
            $products[$product_code]['quantity'] = $products[$product_code]['quantity'] + $delta;
            $this->products = json_encode($products);
            $this->update_cart();

        } elseif ($delta > 0) {
            $this->add_product($product_id, $delta, $variation);
        }
    }

    /**
     * Изменение колличества товара в корзине
     *
     * @param $product_id
     * @param $quantity
     * @param array $variations
     */
    public function update_product_quantity($product_id, $quantity, $variations = []){
        $product_code = $this->get_product_code($product_id, $variations);

        if ($quantity <= 0) {
            $this->remove_product($product_code);
        }

        if ($this->product_isset($product_code)) {
            $products = json_decode($this->products, true);
            $products[$product_code]['quantity'] = $quantity;
            $this->update(['products' => json_encode($products)]);
            $this->update_cart();
        } else {
            $this->add_product($product_id, $quantity, $variations);
        }
    }

    /**
     * Проверка наличия товара в корзине
     *
     * @param $product_code
     * @return bool
     */
    public function product_isset($product_code){
        $products = json_decode($this->products, true);

        if(is_null($products)) {
            return false;
        } else {
            return array_key_exists($product_code, $products);
        }
    }

    /**
     * Стоимость вариативного товара
     *
     * @param $product_id
     * @param $variation
     * @return mixed
     */
    public function get_product_price($product_id, $variation){
        $product = Products::find($product_id);
        $price = $product->price;

        $variation = $product->variations()->where('id', $variation)->first();
        if(!empty($variation)){
            $price = $variation->price;
        }

        return $this->get_sale_price($product, $price);
    }

    /**
     * Стоимость с учётом скидки
     *
     * @param $product
     * @param $price
     * @return array
     */
    public function get_sale_price($product, $price){
        $s = 0;
        $sale = 0;
        if(empty($product->old_price)){
            $user = Sentinel::check();
            if(!empty($user)){
                $user = User::find($user->id);

                if($user) {
                    $s = $user->sale();
                    if(!empty($s)){
                        $sale = $price * $s / 100;
                        $price = $price - $sale;
                    }
                }
            }
        }

        return ['price' => $price, 'sale' => $sale, 'sale_percent' => $s];
    }
}