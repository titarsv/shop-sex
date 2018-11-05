<?php

namespace App\Models;

//use Illuminate\Foundation\Auth\User as Authenticatable;
//use Cartalyst\Sentinel\Users\EloquentUser as SentinelUser;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

//class User extends Authenticatable
class User extends \Cartalyst\Sentinel\Users\EloquentUser
{
    public $sales = [
        1000 => 3,
        2500 => 5,
        5000 => 7,
        7000 => 10
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function blog()
    {
        return $this->hasOne('App\Models\Blog', 'user_id', 'id');
    }
    public function user_data()
    {
//        return $this->belongsTo('App\Models\UserData', 'user_id', 'id');
        return $this->hasOne('App\Models\UserData', 'user_id', 'id');
    }
    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'user_id', 'id');
    }
    public function wishlist()
    {
        return $this->hasMany('App\Models\Wishlist', 'user_id', 'id');
    }
    public function reviews()
    {
        return $this->hasMany('App\Models\Review', 'user_id', 'id');
    }
    public function role()
    {
        return Sentinel::findById($this->id)->roles()->pluck('slug')->toArray();
    }

    /**
     * Получение списка желаний пользователя в виде массива id товаров или коллекции товаров
     * в зависимости от входящего аргумента
     *
     * @param $type = array or object
     * @return array|mixed|void
     */
//    public function wishlist($type = 'object')
//    {
//        if (!is_null($this->user_data->wishlist)) {
//            if ($type == 'array') {
//                return json_decode($this->user_data->wishlist, true);
//            } elseif ($type == 'object') {
//                $product = new Product;
//                return $product->getProducts(json_decode($this->user_data->wishlist, true));
//            }
//        } else {
//            return [];
//        }
//    }

    public function checkIfUnregistered($phone, $email){
        return $this->where('email', $email)->orWhere('phone', $phone)->first();
    }

    /**
     * Сумма покупок
     *
     * @return int
     */
    public function ordersTotal(){
        $total = 0;
        foreach ($this->orders()->where('status_id', 6)->get() as $order){
            $total += $order->total_price;
        }
        return $total;
    }

    /**
     * Размер скидки
     *
     * @return int
     */
    public function sale(){
        $sale = 0;
        $total = $this->ordersTotal();

        foreach ($this->sales as $t => $s) {
            if ($total >= $t) {
                $sale = $s;
            }
        }

        return $sale;
    }

    public function nextSale(){
        $current_sale = $this->sale();

        foreach ($this->sales as $t => $s) {
            if($s > $current_sale){
                return [$t, $s];
            }
        }

        return false;
    }
}
