<?php

namespace App\Http\Controllers;

use App\Models\UserSession;
use App\Models\Order;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\User;

class AdminController extends Controller
{
    public function dash(UserSession $session)
    {
//        $user_exists = User::where('email', 'admin@laravel.com')->first();
//        if ($user_exists) {
//            $user = Sentinel::findById($user_exists->id);
//            ($reminder = Reminder::exists($user)) || ($reminder = Reminder::create($user));
//            dd($reminder);
//            // /lostpassword?id=1&code=fsVfHcQ0ohypu530qdGYgCajfaHOKuId
//        }

        $current = Carbon::now();
        $end_date = $current->subWeek(2);

        $orders = Order::all();
        $weekly_orders = Order::where('created_at', '>', $end_date)->get();

        $order_stat = [];
        for($date = $end_date; $date->diffInDays(); $date->addDay()) {
            $order_stat[$date->format('d.m')] = [];
        }

        $sales_stat = [];
        foreach ($weekly_orders as $order) {
            $order_stat[$order->created_at->format('d.m')]['quantity'][] = $order;

            if ($order->status_id == 6) {
                $sales_stat[] = $order->total_price;
                $order_stat[$order->created_at->format('d.m')]['sales'][] = $order->total_price;
            } else {
                $order_stat[$order->created_at->format('d.m')]['sales'][] = 0;
            }
        }

        foreach ($order_stat as $date => $date_orders) {
            if (isset($date_orders['quantity'])) {
                $order_stat[$date]['quantity'] = count($date_orders['quantity']);
            } else {
                $order_stat[$date]['quantity'] = 0;
            }

            if (isset($date_orders['sales'])) {
                $order_stat[$date]['sales'] = array_sum($date_orders['sales']);
            } else {
                $order_stat[$date]['sales'] = 0;
            }
        }

        $total_sales = $orders->where('status_id', 6)->pluck('total_price')->toArray();

        $stat = [
            'week_order' => $weekly_orders->count(),
            'all_orders' => $orders->count(),
            'new_orders' => Order::where('status_id', 1)->count(),
            'finished'   => Order::where('status_id', 6)->count(),
            'weekly_sales' => array_sum($sales_stat),
            'total_sales'  => array_sum($total_sales)
        ];

//        $users = $session->getUserActivity();
//
//        $online_users = [];
//
//        foreach ($users as $user) {
//            $url = 'http://ipinfo.io/' . $user->ip_address . '/json';
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//            $ipinfo = curl_exec($ch);
//
//            if (!is_null($user->user_id)) {
//                $user_info = Sentinel::findById($user->user_id);
//            } else {
//                $user_info = null;
//            }
//
//            if ($user->user_agent) {
//                $browser = $session->getBrowser($user->user_agent);
//            } else {
//                $browser = null;
//            }
//
//            $online_users[] = [
//                'ipinfo' => json_decode($ipinfo),
//                'userinfo' => $user_info,
//                'browserinfo' => $browser
//            ];
//
//        }

        return view('admin.dashboard', [
            'orders' => $order_stat,
            'stat'   => $stat
        ]);
    }
}

/*
"ip": "203.205.28.14",
  "hostname": "static.cmcti.vn",
  "city": "Ho Chi Minh City",
  "region": "Ho Chi Minh City",
  "country": "VN",
  "loc": "10.8142,106.6438",
  "org": "AS45903 CMC Telecom Infrastructure Company"
*/