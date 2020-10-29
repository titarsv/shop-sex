<div class="header" style="text-align: center;">
    <img src="{!! url('/images/logo.png') !!}" alt="logo" title="shop-sex.com.ua" width="228" height="60" />

    <p style="font-size: 20px;">Новое сообщение на сайте shop-sex.com.ua!</p>

    @if(!empty($$phone))
    <p style="font-size: 20px;">{{ trans('app.phone') }}:<b>{{ $phone }}</b></p>
    @endif
    @if(!empty($email))
        <p style="font-size: 20px;">Email:<b>{{ $email }}</b></p>
    @endif

    <p>Хочу получать информацию о скидках и специальных предложениях.</p>
</div>