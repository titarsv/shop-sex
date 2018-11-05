<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Settings;
use App\Models\Image;
use App\Models\UserData;
use App\Models\Order;
use App\Models\ProductsInOrder;
use App\Models\Wishlist;
use App\Models\Products;
use App\Models\Categories;
use App\Models\Cart;
use App\Models\ProductsCart;
use Breadcrumbs;
use Illuminate\Support\Facades\Mail;
use App\Models\Newpost;

use Cartalyst\Sentinel\Roles\EloquentRole as Roles;

class UserController extends Controller
{
    public $settings;

    protected $rules = [
        'email' => 'required|unique:users'
    ];
    protected $messages = [
        'email.required' => 'Поле должно быть заполнено!',
        'email.unique' => 'Поле должно быть уникальным!'
    ];

    /**
     * Список всех зарегистрированных и незарегистрированных пользователей
     * в панели администратора
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::join('role_users', function ($join) {
            $join->on('users.id', '=', 'role_users.user_id')
                ->whereIn('role_users.role_id', [3,4]);
        })->paginate(8);

        return view('admin.users.index', [
            'users' => $users,
            'title' => 'Список пользователей'
        ]);
    }

    /**
     * @return mixed
     */
    public function managers()
    {
        $users = User::join('role_users', function ($join) {
            $join->on('users.id', '=', 'role_users.user_id')
                ->whereIn('role_users.role_id', [2]);
        })->paginate(8);

        return view('admin.users.index', [
            'users' => $users,
            'title' => 'Список менеджеров'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = Sentinel::check();

        $user = User::find($user->id);

        $orders = Order::where('user_id',$user->id)->orderBy('created_at','desc')->get();

        $wish_list = Wishlist::where('user_id',$user->id)->get();

        $newpost = new Newpost();
        $regions = $newpost->getRegions();

        $address = $user->user_data->address();
        $cities = [];
        $departments = [];
        if(!empty($address)){
            if(!empty($address->npregion)){
                $region = $newpost->getRegionRef($address->npregion);
                $cities = $newpost->getCities($region->region_id);
            }
            if(!empty($address->npcity)){
                $city = $newpost->getCityRef($address->npregion);
                $departments = $newpost->getWarehouses($city->city_id);
            }
        }

        return view('users.index')->with('user', $user)
            ->with('orders', $orders)
            ->with('user_data', $user->user_data)
            ->with('wish_list', $wish_list)
            ->with('address', $address)
            ->with('cities', $cities)
            ->with('departments', $departments)
            ->with('regions', $regions);
    }
    public function history()
    {
        $user = Sentinel::check();
        $orders = Order::where('user_id',$user->id)->orderBy('created_at','desc')->get();

        return view('users.history')->with('user', $user)->with('orders', $orders);
    }
    public function wishList()
    {
        $user = Sentinel::check();
        $wish_list = Wishlist::where('user_id',$user->id)->get();
        return view('users.wishlist')->with('user', $user)->with('products', $wish_list);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $settings = Settings::find(1);

        $image_size = [
            'width' => $settings->category_image_width,
            'height' => $settings->category_image_height
        ];

        $user = User::find($id);

        return view('admin.users.edit')
            ->with('user', $user)
            ->with('image_size', $image_size);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = $this->rules;
        $rules['email'] = 'required|unique:users,email,'.$id.'';

        $messages = $this->messages;

        $validator = Validator::make($request->all(), $rules, $messages);
        $user = User::find($id);

        $image_id = $request->image_id ? $request->image_id : $user->user_data->image_id;
        $href = Image::find($image_id)->href;

        $request->merge(['href' => $href, 'user_id' => $id]);

        if($validator->fails()){
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $request_user = $request->only(['first_name', 'last_name', 'email']);
        $user->fill($request_user);
        $user->save();

        $request_user_data = $request->only(['user_id', 'phone', 'adress', 'company', 'other_data', 'image_id']);
        $user->user_data()->update($request_user_data);

        if(isset($user->roles->first()->slug) && $user->roles->first()->slug != $request->role){$role = Sentinel::findRoleBySlug($request->role);
           $old_role = Sentinel::findRoleBySlug($user->roles->first()->slug);
           $old_role->users()->detach($user);
           $role = Sentinel::findRoleBySlug($request->role);
           $role->users()->attach($user);
        }

        $user_role = Sentinel::findRoleBySlug('user');
        $unreg_user_role = Sentinel::findRoleBySlug('unregister_user');
        $unreg_users = $unreg_user_role->users()->with('roles')->get();
        $users = $user_role->users()->with('roles')->get();
        $users = $users->merge($unreg_users);

        return redirect('/admin/users/edit/'.$user->id)
            ->with('users', $users)
            ->with('message-success', 'Пользователь ' . $user->first_name . ' успешно обновлен.');
    }

    /**
     * Обновление адреса
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAddress(Request $request){
        $user = Sentinel::check();
        if ($user) {
            $user = User::find($user->id);

            $address = json_encode([
                'city' => $request->city,
                'post_code' => $request->post_code,
                'street' => $request->street,
                'house' => $request->house,
                'flat' => $request->flat,
                'npregion' => $request->npregion,
                'npcity' => $request->npcity,
                'npdepartment' => $request->npdepartment,
            ], JSON_UNESCAPED_UNICODE);

            $user->user_data()->update(['address' => $address]);

            return response()->json(['success' => true]);
        }else{
            return response()->json(['success' => false]);
        }
    }

    public function changeData()
    {
        $user = Sentinel::check();
        if ($user) {
            $user = User::find($user->id);
        }

        return view('users.change_data')
            ->with('user_data', $user->user_data)
            ->with('user', $user);
    }

    public function saveChangedData(Request $request)
    {
        $user = Sentinel::check();
        if ($user) {
            $user = User::find($user->id);
        }

        $rules = [
            'first_name' => 'required',
            'phone'     => 'required|regex:/^[0-9\-! ,\'\"\/+@\.:\(\)]+$/',
            'email'     => 'required|email|unique:users,email,'.$user->id
        ];

        $messages = [
            'first_name.required' => 'Не заполнены обязательные поля!',
            'phone.required'    => 'Не заполнены обязательные поля!',
            'phone.regex'       => 'Некорректный телефон!',
            'email.required'    => 'Не заполнены обязательные поля!',
            'email.email'       => 'Некорректный email-адрес!',
            'email.unique'      => 'Пользователь с таким email-ом уже зарегистрирован!'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator);
        }

        $user->first_name = htmlspecialchars($request->first_name);
        $user->last_name = htmlspecialchars($request->last_name);
        $user->email = htmlspecialchars($request->email);
        $user->user_data->phone = htmlspecialchars($request->phone);
        $user->user_data->adress = htmlspecialchars($request->adress);

        $user->push();

        return redirect('/user')
            ->with('status', 'Ваши личные данные успешно изменены!')
            ->with('process', 'change_data');
    }

    public function updatePassword(Request $request)
    {
        $user = Sentinel::check();
        if ($user) {
            $user = User::find($user->id);
        }

        $rules = [
            'password'  => 'min:4|confirmed',
            'password_confirmation' => 'min:4'
        ];

        $messages = [
            'password.min'      => 'Пароль должен быть не менее 4 символов!',
            'password.confirmed' => 'Введенные пароли не совпадают!'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator)
                ->with('process', 'update_password');
        }

        if($request->password) {
            $user->password = password_hash($request->password, PASSWORD_DEFAULT);
        }

        $user->push();

        return response()->json(['success' => true]);
    }

    public function updateSubscr(Request $request)
    {
        $user = Sentinel::check();
        if ($user) {
            $user = User::find($user->id);
        }

        $rules = [
            'subscr'  => 'required'
        ];

        $messages = [
            'subscr.required' => 'Не выбран тип подписки!'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator);
        }

        if($request->subscr) {
            $user->user_data->subscribe = $request->subscr;
        }

        $user->push();

        return response()->json(['success' => true]);
    }

    public function get_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function subscribe(Request $request, User $user, UserData $user_data)
    {
        $rules = [
            'email'     => 'required|email'
        ];

        $messages = [
            'email.required'    => 'Вы не указали email-адрес!',
            'email.email'       => 'Некорректный email-адрес!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }

        $user_exists = User::where('email', $request->email)->first();

        if ($user_exists){
            $subscribe = $user->where('id', $user_exists->id)->first();
            $subscribe->user_data->subscribe = 1;
            $subscribe->save();
        } else {
            $user = Sentinel::registerAndActivate(array(
                'email'    => $request->email,
                'password' => 'null',
                'permissions' => [
                    'unregistered' => true
            ]
            ));

            $role = Sentinel::findRoleBySlug('unregister_user');
            $role->users()->attach($user);

            $user_data->create([
                'user_id'   => $user->id,
                'image_id'  => 1,
                'subscribe' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        return response()->json(['success' => 'Вы успешно подписались на новости!']);
    }

    public function statistic($id)
    {
        $orders = Order::where('user_id', $id)->get();

        return view('admin.users.orders')->with('orders', $orders)->with('user', User::find($id));
    }
    public function reviews($id)
    {
        $reviews = Reviews::where('user_id', $id)->paginate(10);

        return view('admin.users.reviews')->with('reviews', $reviews)->with('user', User::find($id));
    }
    public function adminWishlist($id)
    {
        $wishlist = Wishlist::where('user_id', $id)->paginate(10);

        return view('admin.users.wishlist')->with('wishlist', $wishlist)->with('user', User::find($id));
    }

    /**
     * Обновление данных пользователя
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveUserData(Request $request){
        $user = Sentinel::check();
        if ($user) {
            $user = User::find($user->id);
        }

        $rules = [
            'fio' => 'required',
            'phone'     => 'required|regex:/^[0-9\-! ,\'\"\/+@\.:\(\)]+$/',
            'email'     => 'required|email|unique:users,email,'.$user->id
        ];

        $messages = [
            'fio.required' => 'Не заполнены обязательные поля!',
            'phone.required'    => 'Не заполнены обязательные поля!',
            'phone.regex'       => 'Некорректный телефон!',
            'email.required'    => 'Не заполнены обязательные поля!',
            'email.email'       => 'Некорректный email-адрес!'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator);
        }

        $name = explode(' ', $request->fio);

        $user->first_name = htmlspecialchars($name[0]);
        if(isset($name[1]))
            $user->last_name = htmlspecialchars($name[1]);
        $user->email = htmlspecialchars($request->email);
        $user->user_data->phone = htmlspecialchars($request->phone);
        $user->user_data->user_birth = htmlspecialchars($request->user_birth);

        $user->push();

        return response()->json(['success' => true]);
    }

    /**
     *  Заказ обратного звонка
     *
     * @param Request $request
     * @param Settings $settings
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(Request $request, Settings $settings){
        $rules = [
            'name' => 'required',
            'phone'     => 'required|regex:/^[0-9\-! ,\'\"\/+@\.:\(\)]+$/'
        ];

        $messages = [
            'name.required' => 'Не указано имя!',
            'phone.required'    => 'Некорректный номер телефона!'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 200);
        }

        $emails = $settings->get_setting('notify_emails');

        Mail::send('emails.callback', ['name' => $request->name, 'phone' => $request->phone], function($msg) use ($emails){
            $msg->from('admin@shop-sex.com.ua', 'Интернет-магазин shop-sex.com.ua');
            $msg->to($emails);
            $msg->subject('Перезвоните мне!');
        });

        return response()->json(['success' => 'Спасибо за Ваш интерес к нам! Наш менеджер вскоре свяжется с вами, ожидайте звонка.']);
    }

    public function sendMail(){
        $domain = $_SERVER['HTTP_HOST'];
        $_SESSION['http_host'] = $domain;

        $sendTo = 'titarsv@gmail.com';
        $from = "info@$domain";
        $title = '';

        $subject = "Заявка $domain " . $title;

        if(count($_FILES)){
            //print_r($_FILES);
            $files = array();
            foreach ($_FILES as $file) {
                if ($file["error"] == 0) {
                    $tmp_name = $file["tmp_name"];
                    // basename() может спасти от атак на файловую систему;
                    // может понадобиться дополнительная проверка/очистка имени файла
                    $name = basename($file["name"]);
                    move_uploaded_file($tmp_name, "upload/$name");
                    $files[] = array('path' => "upload/$name", 'name' => $tmp_name);
                }
            }
        }

        if (array_key_exists('data', $_POST)){
            //return print_r($_POST);die();

            $eol = PHP_EOL;

            $headers = "From: $from\nReply-To: $from\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html;charset=utf-8 \r\n";

            $msg = "";

            $msg .= "<html><body style='font-family:Arial,sans-serif;'>";
            $msg .= "<h2 style='color:#161616;font-weight:bold;font-size:30px;border-bottom:2px dotted #bd0707;'>Новая заявка на сайте $domain " . $title . "</h2>" . $eol;

            $data = json_decode($_POST['data']);
            $session_data = ['sourse' => 'Поисковая система', 'term' => 'Ключ', 'campaign' => 'Кампания'];

            foreach ($data as $key => $params) {
                if (!empty($params->title) && !empty($params->val)) {
                    $val = $this->prepare_data($params->val, $key);
                    $msg .= "<p><strong>$params->title:</strong> $val</p>" . $eol;
                    if (isset($session_data[$key]))
                        unset($session_data[$key]);
                }
            }

            foreach ($session_data as $key => $title) {
                if (array_key_exists($key, $_SESSION)) {
                    $val = $this->prepare_data($_SESSION[$key], $key);
                    $msg .= "<p><strong>$title:</strong> $val</p>" . $eol;
                }
            }

            $msg .= "</body></html>";

            if(!empty($files)){

                foreach($files as $file){
                    $path = $file['path'];
                }

            }

            $setting = new Settings();

            Mail::send('emails.sendmail', ['html' => $msg], function($msg) use ($setting){
                $msg->from('admin@shop-sex.com.ua', 'Интернет-магазин shop-sex.com.ua');
                $msg->to(get_object_vars($setting->get_setting('notify_emails')));
                $msg->subject('Перезвоните мне!');
            });

            header("HTTP/1.0 200 OK");
            echo '{"status":"success"}';

//            if(!empty($path)){
//                if ($this->send_mail($sendTo, $subject, $msg, $path)) {
//                    header("HTTP/1.0 200 OK");
//                    echo '{"status":"success"}';
//                } else {
//                    header("HTTP/1.0 404 Not Found");
//                    echo '{"status":"error"}';
//                }
//            }else{
//                if (mail($sendTo, $subject, $msg, $headers)) {
//                    header("HTTP/1.0 200 OK");
//                    echo '{"status":"success"}';
//                } else {
//                    header("HTTP/1.0 404 Not Found");
//                    echo '{"status":"error"}';
//                }
//            }
        } else {
            header("HTTP/1.0 404 Not Found");
            echo '{"status":"error"}';
        }

        if(!empty($files)){
            foreach($files as $file){
                unlink($file['path']);
            }
        }
    }

    public function prepare_data($data, $key){
        switch ($key) {
            case 'referer':
                return substr($data, 0, 30);
            case 'term':
                return urldecode($data);
            default:
                return $data;
        }
    }

    public function send_mail($to, $thm, $html, $path) {
        $fp = fopen($path,"r");
        if (!$fp) {
            print "Файл $path не может быть прочитан";
            exit();
        }

        $file = fread($fp, filesize($path));
        fclose($fp);

        $boundary = "--".md5(uniqid(time())); // генерируем разделитель
        $headers = "MIME-Version: 1.0\n";
        $headers .="Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
        $multipart = "--$boundary\n";

        $kod = 'utf-8';
        $multipart .= "Content-Type: text/html; charset=$kod\n";
        $multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";
        $multipart .= "$html\n\n";

        $message_part = "--$boundary\n";
        $message_part .= "Content-Type: application/octet-stream\n";
        $message_part .= "Content-Transfer-Encoding: base64\n";
        $message_part .= "Content-Disposition: attachment; filename = \"".$path."\"\n\n";
        $message_part .= chunk_split(base64_encode($file))."\n";
        $multipart .= $message_part."--$boundary--\n";

        if(mail($to, $thm, $multipart, $headers)) {
            return 1;
        }
    }
}
