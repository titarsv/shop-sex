<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-sm-10">
                <div class="row header__enter">
                    <p class="col-sm-offset-3 col-sm-3 header__enter-garant">Гарантия анонимности</p>
                    <ul class="header__enter-list">
                        <li class="col-sm-2 header__enter-forum"><a href="{{env('APP_URL')}}/page/forum">Форум</a></li>
                        {{--<li class="col-sm-2 header__enter-login"><a href="{{env('APP_URL')}}/login">Войти</a></li>--}}
                    </ul>
                    <ul class="header__contacts hedr col-sm-3">
                        <li><img src="/images/icons/qa.png" alt=""><a href="" class="popup-btn"  data-mfp-src="#question-popup">Задать вопрос</a></li>
                    </ul>
                </div>
                <div class="row header__logo-search-wrp">
                    <div class="col-sm-2 col-xs-5">
                        @if(Request::path()!='/')
                            <a href="{{env('APP_URL')}}">
                                <img src="/images/logo.png" class="header__logo" alt="Главная">
                            </a>
                        @else
                            <img src="/images/logo.png" class="header__logo" alt="Главная">
                        @endif
                    </div>
                    <div class="col-sm-4 hidden-xs">
                        {!! Form::open(['route' => 'search', 'class' => 'header__search-wrp', 'method' => 'post']) !!}
                            {!! Form::input('search', 'text', null, ['class' => 'header__search'] ) !!}
                            <input type="submit" value="" class="search-hidden">
                        {!! Form::close()!!}
                    </div>
                    <div class="col-sm-6 hidden-xs">
                        <ul class="header__contacts">
                            <li><img src="/images/icons/phone.png" alt=""> <a href="tel:0509712569">050 971-25-69</a></li>
                            <li><img src="/images/icons/phone.png" alt=""> <a href="tel:0958860978">095 886-09-78</a></li>
                            <li><img src="/images/icons/inst.png" alt=""> shop_sex.com.ua</li>
                        </ul>
                    </div>
                    <div class="visible-xs-block col-xs-5">
                        <ul class="header__contacts">
                            <li><a href="tel:0509712569">050 971-25-69</a></li>
                            <li><a href="tel:0958860978">095 886-09-78</a></li>
                        </ul>
                    </div>
                    <div class="visible-xs-block col-xs-2">
                        <div class="hmb-menu"></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 hidden-xs">
                <a href="{{env('APP_URL')}}/checkout" class="cart-link">
                    <div class="header__cart">
                    <img src="/images/icons/cart-main.png" class="header__cart-img" alt="">
                        <p class="header__cart-title">Корзина</p>
                        @if(isset($cart) && $cart->total_quantity)
                            <p class="header__cart-sum">{{ number_format($cart->total_price, 2, '.', ' ') }}грн</p>
                            <p class="header__cart-guant">{{ $cart->total_quantity }}</p>
                        @endif
                    </div>
                </a>
            </div>
            <div class="col-sm-12 hidden-xs">
                <nav>
                    <ul class="navigation">
                        <li class="navigation__catalog"><a href="{{env('APP_URL')}}/catalog"><img src="/images/icons/catalog.png" alt=""> Каталог товаров</a></li>
                        @if($isset_new)
                        <li><a href="{{env('APP_URL')}}/catalog/novinki">Новинки</a></li>
                        @endif
                        @if($isset_actions)
                        <li><a href="{{env('APP_URL')}}/catalog/aktsii">Акции</a></li>
                        @endif
                        <li><a href="{{env('APP_URL')}}/page/o-magazine">О магазине</a></li>
                        <li><a href="{{env('APP_URL')}}/page/voprosy-i-otvety">Вопросы и ответы</a></li>
                        <li><a href="{{env('APP_URL')}}/page/kak-kupit">Как купить</a></li>
                        <li><a href="{{env('APP_URL')}}/page/kontakty">Контакты</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <ul class="mob-navigation">
        <li class="mob-navigation__catalog"><a href="{{env('APP_URL')}}/catalog">Каталог товаров</a></li>
        @if($isset_new)
        <li><a href="{{env('APP_URL')}}/catalog/novinki">Новинки</a></li>
        @endif
        @if($isset_actions)
        <li><a href="{{env('APP_URL')}}/catalog/aktsii">Акции</a></li>
        @endif
        <li><a href="{{env('APP_URL')}}/page/oplata-i-dostavka">О магазине</a></li>
        <li><a href="{{env('APP_URL')}}/page/voprosy-i-otvety">Вопросы и ответы</a></li>
        <li><a href="{{env('APP_URL')}}/page/kak-kupit">Как купить</a></li>
        <li><a href="{{env('APP_URL')}}/page/kontakty">Контакты</a></li>
    </ul>

    <div class="hide">
        <div id="question-popup" class="view-popup">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 col-sm-12 col-sm-offset-0 col-xs-12">
                        <div class="question-popup__container">
                            <p class="question-popup__container-title">Задать вопрос</p>
                            <form action="/sendmail" class="question-popup__form ajax_form"
                                  data-error-title="Ошибка отправки!"
                                  data-error-message="Попробуйте отправить вопрос через некоторое время."
                                  data-success-title="Спасибо за вопрос!"
                                  data-success-message="Наш менеджер свяжется с вами в ближайшее время.">
                                <textarea name="request" placeholder="Напишите свой вопрос" data-validate-required="Обязательное поле" data-title="Вопрос"></textarea>
                                <input type="tel" name="phone" placeholder="Номер телефона" data-title="Телефон" data-validate-required="Обязательное поле" data-validate-uaphone="Неправильный номер">
                                <button type="submit">Отправить</button>
                            </form>
                            <button title="Close (Esc)" type="button" class="mfp-close">×</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>