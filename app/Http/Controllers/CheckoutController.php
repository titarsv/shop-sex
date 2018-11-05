<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Newpost;
use App\Models\Order;
use App\Models\Settings;
use App\Models\UserData;
use App\Models\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Http\Request;
use App\Models\Modules;
use App\Models\Products;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Validator;

class CheckoutController extends Controller
{
    /**
     * Создание заказа
     *
     * @param Request $request
     * @param Order $order
     * @param Cart $cart
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrder(Request $request, Order $order, Cart $cart)
    {
        $cart = $cart->current_cart();

        if(!$cart->total_quantity){
            return response()->json(['error' => ['cart' => 'В корзине нет товаров!']]);
        }

//        $errors = $this->validateFields($request->all());
//        if ($errors) {
//            return response()->json(['error' => $errors]);
//        }

        $rules = [
            'phone' => 'required'
        ];

        $messages = [
            'phone.required' => 'Не указан контактный номер телефона!'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails() || is_null($cart)){
            $errors = is_null($cart) ? 'Ваша корзина пуста!' : $validator->messages();
            return response()->json(['error' => $errors]);
        }

        $delivery_method = $request->delivery;
        $delivery_info = [
            'method'    => $delivery_method,
            'info'      => ''
        ];

        $data = [
            'user_id'   => 0,
            'products'  => $cart->products,
            'total_quantity'    => $cart->total_quantity,
            'total_price'       => $cart->total_price,
            'user_info'         => json_encode([
                'phone' => isset($request->phone) ? $request->phone : "",
                'comment' => isset($request->comment) ? $request->comment : ""
            ]),
            'delivery'  => json_encode($delivery_info),
            'payment'   => $request->payment,
            'status_id' => 0,
            'created_at' => Carbon::now()
        ];

        $id = $order->insertGetId($data);

        $this->sendOrderMails($id);
        if($order->payment == 'card')
            return $this->get_liqpay_data($order);
        else
            return response()->json(['success' => 'redirect', 'order_id' => $id]);
    }

    public function sendOrderMails($order_id){
        $setting = new Settings();
        $cart = new Cart();

        $order = Order::find($order_id);

        $order->update(['status_id' => 1]);

        $order_user = json_decode($order->user_info, true);

        $cart = $cart->current_cart();

        $cart->current_cart()->delete();

        Mail::send('emails.order', ['user' => $order_user, 'order' => $order, 'admin' => true], function($msg) use ($setting){
            $msg->from('admin@shop-sex.com.ua', 'Интернет-магазин shop-sex.com.ua');
            $msg->to(get_object_vars($setting->get_setting('notify_emails')));
            $msg->subject('Новый заказ');
        });

        Mail::send('emails.order', ['user' => $order_user, 'order' => $order, 'admin' => false], function($msg) use ($order_user){
            $msg->from('admin@shop-sex.com.ua', 'Интернет-магазин shop-sex.com.ua');
            $msg->to($order_user['email']);
            $msg->subject('Новый заказ');
        });
    }

    /**
     * Подгрузка различных темплейтов в зависимости от выбранного способа доставки
     * @param Request $request
     * @param Newpost $newpost
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delivery(Request $request, Newpost $newpost)
    {
        if (!is_null($request->cookie('current_order_id'))) {
            $current_order_id = $request->cookie('current_order_id');
        } elseif ($request->order_id) {
            $current_order_id = $request->order_id;
        }else {
            $current_order_id = 0;
        }

        if ($request->delivery == 'newpost'){
            $regions = $newpost->getRegions();

            return view('public.checkout.newpost', [
                'regions'           => $regions,
                'current_order_id'  => $current_order_id
            ]);
        } else {
            return view('public.checkout.' . $request->delivery, ['current_order_id' => $current_order_id]);
        }
    }

    /**
     * Валидация полей доставки
     *
     * @param $data
     * @return mixed
     */
    public function validateFields($data)
    {
        $errors = [];
        $messages = [];

        if(isset($data['delivery'])) {
            if ($data['delivery'] == 'newpost') {
//                $rules = [
//                    'newpost.region' => 'not_in:0',
//                    'newpost.city' => 'not_in:0',
//                    'newpost.warehouse' => 'not_in:0',
//                ];
//
//                $messages = [
//                    'newpost.region.not_in' => 'Выберите область!',
//                    'newpost.city.not_in' => 'Выберите город!',
//                    'newpost.warehouse.not_in' => 'Выберите отделение Новой Почты!',
//                ];
            } elseif ($data['delivery'] == 'courier') {
//                $rules = [
//                    'courier.street' => 'required',
//                    'courier.house' => 'required',
//                ];
//
//                $messages = [
//                    'courier.street.required' => 'Не указана улица!',
//                    'courier.house.required' => 'Не указан номер дома!',
//                ];
            } elseif ($data['delivery'] == 'ukrpost') {
                $rules = [
                    'ukrpost.region' => 'required',
                    'ukrpost.city' => 'required',
                    'ukrpost.index' => 'required|numeric',
                    'ukrpost.street' => 'required',
                    'ukrpost.house' => 'required',
                ];

                $messages = [
                    'ukrpost.region.required' => 'Не указана область!',
                    'ukrpost.city.required' => 'Не указан город!',
                    'ukrpost.index.required' => 'Не указан почтовый индекс!',
                    'ukrpost.index.numeric' => 'Индекс должен быть числовым!',
                    'ukrpost.street.required' => 'Не указана улица!',
                    'ukrpost.house.required' => 'Не указан номер дома!',
                ];
            } elseif (!$data['delivery']) {
//                $errors = [
//                    'delivery' => 'Не выбран метод доставки!',
//                ];
            }
        }else{
//            $errors = [
//                'delivery' => 'Не выбран метод доставки!',
//            ];
        }

//        $rules['payment'] = 'required|in:cash,prepayment';
//        $rules['payment'] = 'required|in:privat,nal_delivery,nal_samovivoz,nalogenniy';
//        $messages['payment.required'] = 'Не выбран способ оплаты!';
//        $messages['payment.in'] = 'Выбран некорректный способ оплаты!';

        $rules['phone'] = 'required';
        $messages['phone'] = 'required';

        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()){
            $errors = array_merge($errors, $validator->messages()->toArray());
        }

        if (!empty($errors))
            return $errors;

        return false;
    }

    /**
     * Загрузка списка городов Новой Почты
     *
     * @param Request $request
     * @param Newpost $newpost
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCities(Request $request, Newpost $newpost)
    {
        $region = $newpost->getRegionRef($request->region_id);

        if (!is_null($region)) {
            $cities = $newpost->getCities($region->region_id);
        } else {
            return response()->json(['error' => 'При загрузке городов произошла ошибка. Пожалуйста, попробуйте еще раз!']);
        }

        if ($cities) {
            return response()->json(['success' => $cities]);
        } else {
            return response()->json(['error' => 'При загрузке городов произошла ошибка. Пожалуйста, попробуйте еще раз!']);
        }
    }

    /**
     * Загрузка списка отделений Новой Почты
     *
     * @param Request $request
     * @param Newpost $newpost
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWarehouses(Request $request, Newpost $newpost)
    {
        $city = $newpost->getCityRef($request->city_id);

        if (!is_null($city)) {
            $warehouses = $newpost->getWarehouses($city->city_id);
        } else {
            return response()->json(['error' => 'При загрузке отделений произошла ошибка. Пожалуйста, попробуйте еще раз!']);
        }

        if ($warehouses) {
            return response()->json(['success' => $warehouses]);
        } else {
            return response()->json(['error' => 'При загрузке отделений произошла ошибка. Пожалуйста, попробуйте еще раз!']);
        }
    }
}
