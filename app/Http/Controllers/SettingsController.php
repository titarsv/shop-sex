<?php

namespace App\Http\Controllers;

use App\Models\Newpost;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use App\Http\Requests;
use App\Models\Settings;
use Validator;
use Cache;

class SettingsController extends Controller
{
    private $user;
    private $settings;

    function __construct(Settings $settings)
    {
        $this->user = Sentinel::check();
        $this->settings = $settings;
    }

    public function index()
    {
        $settings = $this->settings->get_all();

        $image_sizes = config('image.sizes');

        return view('admin.settings')
            ->with('user', $this->user)
            ->with('settings', $settings)
            ->with('image_sizes', $image_sizes);
    }

    public function update(Request $request, Settings $settings)
    {
        $rules = [
//            'meta_title' => 'required|max:75',
//            'meta_description' => 'max:180',
//            'meta_keywords' => 'max:180',
            'notify_emails.*' => 'email|distinct|filled',
            'other_phones.*' => 'distinct|filled|regex:/^[0-9\-! ,\'\"\/+@\.:\(\)]+$/',
            'main_phone_1' => 'regex:/^[0-9\-! ,\'\"\/+@\.:\(\)]+$/',
            'main_phone_2' => 'regex:/^[0-9\-! ,\'\"\/+@\.:\(\)]+$/',
        ];

        $messages = [
            'meta_title.required' => 'Поле должно быть заполнено!',
            'meta_title.max' => 'Максимальная длина заголовка не может превышать 75 символов!',
            'meta_description.max' => 'Максимальная длина описания не может превышать 180 символов!',
            'meta_keywords.max' => 'Максимальная длина ключевых слов не может превышать 180 символов!',
            'notify_emails.*.email' => 'Введите корректный e-mail адрес!',
            'notify_emails.*.distinct' => 'Значения одинаковы!',
            'notify_emails.*.filled' => 'Поле должно быть заполнено!',
            'other_phones.*.distinct' => 'Значения одинаковы!',
            'other_phones.*.filled' => 'Поле должно быть заполнено!',
            'other_phones.*.regex' => 'Неверный формат телефона!',
            'main_phone_1.regex' => 'Неверный формат телефона!',
            'main_phone_2.regex' => 'Неверный формат телефона!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $settings->update_settings($request->except('_token'), true);

        return back()->with('message-success', 'Настройки успешно сохранены!');
    }

    public function extraIndex(Settings $setting)
    {
        $update_period = [
            [
                'period'    => 'Каждый день',
                'value'     => 86400
            ],
            [
                'period'    => 'Раз в неделю',
                'value'     => 604800
            ],
            [
                'period'    => 'Раз в месяц',
                'value'     => 2592000
            ],
            [
                'period'    => 'Раз в полгода',
                'value'     => 15552000
            ],
        ];

        $currencies = ['USD', 'EUR', 'RUB', 'UAH', 'BYN', 'KZT'];

        return view('admin.extra_settings', [
            'settings' => $setting->get_extra(),
            'update_period' => $update_period,
            'currencies' => $currencies
        ]);
    }

    public function seoSettings()
    {
        $settings = $this->settings->get_all();

        $image_sizes = config('image.sizes');

        return view('admin.seo_settings')
            ->with('user', $this->user)
            ->with('settings', $settings)
            ->with('image_sizes', $image_sizes);
    }

    public function seoUpdate(Request $request, Settings $setting)
    {
        $rules = [
            'meta_title' => 'required',
            'meta_title' => 'required',
            'meta_title' => 'required',
            'meta_title' => 'required',
        ];

        $messages = [
            'meta_title.required' => 'Поле должно быть заполнено!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }
    }

    public function newpostUpdate(Newpost $newpost)
    {
        $result = $newpost->updateAll();

        if ($result){
            $message_status = 'message-success';
            $message_text = 'Данные API Новой Почты успешно обновлены!';
        } else {
            $message_status = 'message-error';
            $message_text = 'При обновлении данных произошла ошибка! Подробности: /storage/logs/laravel.log';
        }
        return redirect('/admin/delivery-and-payment')
            ->with($message_status, $message_text);
    }

    public function extraUpdate(Request $request, Settings $settings)
    {
        $rules = [
            'newpost_api_key' => 'filled|max:32',
            'newpost_regions_update_period' => 'filled|not_in:0',
            'newpost_cities_update_period' => 'filled|not_in:0',
            'newpost_warehouses_update_period' => 'filled|not_in:0',
            'liqpay_api_public_key' => 'filled',
            'liqpay_api_private_key' => 'filled',
            'liqpay_api_currency' => 'filled|not_in:0'
        ];

        $messages = [
            'newpost_api_key.filled' => 'Поле должно быть заполнено!',
            'newpost_api_key.max' => 'Длина ключа должна быть не более 32 символов!',
            'newpost_regions_update_period.filled' => 'Выберите период!',
            'newpost_regions_update_period.not_in' => 'Выберите период!',
            'newpost_cities_update_period.filled' => 'Выберите период!',
            'newpost_cities_update_period.not_in' => 'Выберите период!',
            'newpost_warehouses_update_period.filled' => 'Выберите период!',
            'newpost_warehouses_update_period.not_in' => 'Выберите период!',
            'liqpay_api_public_key.filled' => 'Поле должно быть заполнено!',
            'liqpay_api_private_key.filled' => 'Поле должно быть заполнено!',
            'liqpay_api_currency.filled' => 'Выберите валюту платежей!',
            'liqpay_api_currency.not_in' => 'Выберите валюту платежей!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $settings->update_settings($request->except('_token'), false);
        Cache::flush();

        return back()->with('message-success', 'Настройки успешно сохранены!');
    }

}
