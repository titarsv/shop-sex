<div class="header" style="text-align: center;">
    <img src="{!! url('/images/logo.png') !!}" alt="logo" title="shop-sex.com.ua" width="228" height="60" />
</div>

<h1>{{ trans('app.hello') }}, <strong>{!! $user['last_name'] or '' !!} {!! $user['first_name'] !!}</strong>!</h1>
<p>{{ trans('app.welcome_to_the_online_store_shop-sexcomua') }}</p>
<p>{{ trans('app.to_enter') }} <a href="{!! url('/user') !!}">{{ trans('app.personal_area') }}</a> {{ trans('app.use_your_e-mail_and_password_specified_during_registration') }}</p>