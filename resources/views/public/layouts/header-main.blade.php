<header class="header">
    <div class="container">
        <div class="row">
            <div class="header-main">
                @if(Request::path()!='/')
                    <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}">
                        <picture class="logo">
                            <source srcset="/images/logo.webp" type="image/webp">
                            <source srcset="/images/logo.png" type="image/png">
                            <img src="/images/logo.png" alt="{{ trans('app.home') }}">
                        </picture>
                    </a>
                @else
                    <picture class="logo">
                        <source srcset="/images/logo.webp" type="image/webp">
                        <source srcset="/images/logo.png" type="image/png">
                        <img src="/images/logo.png" alt="{{ trans('app.home') }}">
                    </picture>
                @endif
                <div class="header-controls">
                    <div class="header-top">
                        <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/checkout" class="cart-link hidden-cart">
                            <div class="header__cart">
                                {{--<img src="/images/icons/cart-main.png" class="header__cart-img" alt="">--}}
                                <picture class="header__cart-img">
                                    <source data-src="/images/icons/cart-main.webp" srcset="/images/pixel.webp" type="image/webp">
                                    <source data-src="/images/icons/cart-main.png" srcset="/images/pixel.png" type="image/png">
                                    <img src="/images/pixel.jpg" alt="cart-main" style="width: 100%;">
                                </picture>
                                <picture class="header__cart-mob">
                                    <source data-src="/images/icons/cart-mob.webp" srcset="/images/pixel.webp" type="image/webp">
                                    <source data-src="/images/icons/cart-mob.png" srcset="/images/pixel.png" type="image/png">
                                    <img src="/images/pixel.jpg" alt="cart-main" style="width: 100%;">
                                </picture>
                                <p class="header__cart-title">{{ trans('app.basket') }}</p>
                                @if(isset($cart) && $cart->total_quantity)
                                    <p class="header__cart-sum">{{ number_format($cart->total_price, 2, '.', ' ') }}{{ trans('app.hryvnias') }}</p>
                                    <p class="header__cart-guant">{{ $cart->total_quantity }}</p>
                                @endif
                            </div>
                        </a>
                        <div class="header-spacer"></div>
                        <div class="search-btn"></div>
                        <div class="mob-btn popup-btn" data-mfp-src="#phones-popup">
                            <picture>
                                <source data-src="/images/icons/phone.webp" srcset="/images/pixel.webp" type="image/webp">
                                <source data-src="/images/icons/phone.png" srcset="/images/pixel.png" type="image/png">
                                <img src="/images/pixel.jpg" alt="phone">
                            </picture>
                        </div>
                        <div class="header-anon">
                            <div>
                                <picture>
                                    <source srcset="/images/icons/anon.webp" type="image/webp">
                                    <source srcset="/images/icons/anon.png" type="image/png">
                                    <img src="/images/icons/anon.png" alt="">
                                </picture>
                                <span>{{ trans('app.guaranteed_anonymity') }}</span>
                            </div>
                            <p class="header-lang">
                            @if(App::getLocale() == 'ua')
                                <a href="{{ Request::getRequestUri() == '/ua' ? '/' : substr(Request::getRequestUri(), 3) }}">Рус</a> /
                                <span>Укр</span> /
                                <a href="{{ Request::getRequestUri() == '/ua' ? '/en' : '/en'.substr(Request::getRequestUri(), 3) }}">En</a>
                            @elseif(App::getLocale() == 'en')
                                <a href="{{ Request::getRequestUri() == '/ua' ? '/' : substr(Request::getRequestUri(), 3) }}">Рус</a> /
                                <a href="{{ Request::getRequestUri() == '/en' ? '/ua' : '/ua'.substr(Request::getRequestUri(), 3) }}">Укр</a> /
                                <span>En</span>
                            @elseif(App::getLocale() == 'ru')
                                <span>Рус</span> /
                                <a href="/ua{{ Request::getRequestUri() }}">Укр</a> /
                                <a href="/en{{ Request::getRequestUri() }}">En</a>
                            @endif
                            </p>
                            <div class="header-adult">
                                <picture>
                                    <source srcset="/images/icons/pepper.webp" type="image/webp">
                                    <source srcset="/images/icons/pepper.png" type="image/png">
                                    <img src="/images/icons/pepper.png" alt="">
                                </picture>
                                <span>18+</span>
                                <p>{{ trans('app.the_site_contains_content_for_persons_over_the_age_of_18_18+') }}</p>
                            </div>
                        </div>

                        <ul class="header__contacts">
                            <li>
                                <picture>
                                    <source data-src="/images/icons/phone.webp" srcset="/images/pixel.webp" type="image/webp">
                                    <source data-src="/images/icons/phone.png" srcset="/images/pixel.png" type="image/png">
                                    <img src="/images/pixel.jpg" alt="phone">
                                </picture>
                                <a href="tel:0507000197">050 700-01-97</a>
                            </li>
                            <li>
                                <picture>
                                    <source data-src="/images/icons/phone.webp" srcset="/images/pixel.webp" type="image/webp">
                                    <source data-src="/images/icons/phone.png" srcset="/images/pixel.png" type="image/png">
                                    <img src="/images/pixel.jpg" alt="phone">
                                </picture>
                                <a href="tel:0958860978">095 886-09-78</a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/shop_sex.com.ua/" target="_blank">
                                    <picture>
                                        <source data-src="/images/icons/inst.webp" srcset="/images/pixel.webp" type="image/webp">
                                        <source data-src="/images/icons/inst.png" srcset="/images/pixel.png" type="image/png">
                                        <img src="/images/pixel.jpg" alt="inst">
                                    </picture>
                                    shop_sex.com.ua
                                </a>
                            </li>
                        </ul>
                        <div class="hmb-menu"></div>
                    </div>
                    <div class="header-bot">
                        {!! Form::open(['route' => 'search', 'class' => 'header__search-wrp', 'method' => 'get']) !!}
                        {!! Form::input('search', 'text', null, ['class' => 'header__search', 'data-autocomplete' => 'input-search'] ) !!}
                        <input type="submit" value="" class="search-hidden" data-autocomplete="input-search">
                        <div class="search-results" data-output="search-results" style="display: none">

                        </div>
                        {!! Form::close()!!}
                        <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/checkout" class="cart-link">
                            <div class="header__cart">
                                {{--<img src="/images/icons/cart-main.png" class="header__cart-img" alt="">--}}
                                <picture class="header__cart-img">
                                    <source data-src="/images/icons/cart-main.webp" srcset="/images/pixel.webp" type="image/webp">
                                    <source data-src="/images/icons/cart-main.png" srcset="/images/pixel.png" type="image/png">
                                    <img src="/images/pixel.jpg" alt="cart-main" style="width: 100%;">
                                </picture>
                                <p class="header__cart-title">{{ trans('app.basket') }}</p>
                                @if(isset($cart) && $cart->total_quantity)
                                    <p class="header__cart-sum">{{ number_format($cart->total_price, 2, '.', ' ') }}{{ trans('app.hryvnias') }}</p>
                                    <p class="header__cart-guant">{{ $cart->total_quantity }}</p>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <nav class="header-nav">
                <div class="header-nav__top">
                    @if(Request::path()!='/')
                        <a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}">
                            <picture class="logo-mob">
                                <source srcset="/images/logo-mob.webp" type="image/webp">
                                <source srcset="/images/logo-mob.png" type="image/png">
                                <img src="/images/logo-mob.png" alt="{{ trans('app.home') }}">
                            </picture>
                        </a>
                    @else
                        <picture class="logo-mob">
                            <source srcset="/images/logo-mob.webp" type="image/webp">
                            <source srcset="/images/logo-mob.png" type="image/png">
                            <img src="/images/logo-mob.png" alt="{{ trans('app.home') }}">
                        </picture>
                    @endif
                    <div class="header-anon">
                        <picture>
                            <source srcset="/images/icons/anon.webp" type="image/webp">
                            <source srcset="/images/icons/anon.png" type="image/png">
                            <img src="/images/icons/anon.png" alt="">
                        </picture>
                        <span>{{ trans('app.guaranteed_anonymity') }}</span>
                    </div>
                    <div class="header-adult">
                        <picture>
                            <source srcset="/images/icons/pepper.webp" type="image/webp">
                            <source srcset="/images/icons/pepper.png" type="image/png">
                            <img src="/images/icons/pepper.png" alt="">
                        </picture>
                        <span>{{ trans('app.the_site_contains_content_for_persons_over_the_age_of_18_18+') }}</span>
                    </div>
                </div>
                <ul class="header-nav__phones">
                    <li>
                        <a href="tel:0507000197">050 700-01-97</a>
                    </li>
                    <li>
                        <a href="tel:0958860978">095 886-09-78</a>
                    </li>
                </ul>
                <ul class="navigation">
                    <li class="navigation__catalog"><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog">
                            {{--<img src="/images/icons/catalog.png" alt="">--}}
                            <picture>
                                <source data-src="/images/icons/catalog.webp" srcset="/images/pixel.webp" type="image/webp">
                                <source data-src="/images/icons/catalog.png" srcset="/images/pixel.png" type="image/png">
                                <img src="/images/pixel.jpg" alt="cart-main">
                            </picture>
                            {{ trans('app.catalog') }}</a>
                    </li>
                    @if($isset_new)
                        <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/novinki">{{ trans('app.new_items') }}</a></li>
                    @endif
                    @if($isset_actions)
                        <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/aktsii">{{ trans('app.promotions') }}</a></li>
                    @endif
                    <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/o-magazine">{{ trans('app.about_store') }}</a></li>
                    <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/voprosy-i-otvety">{{ trans('app.questions_and_answers') }}</a></li>
                    <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/kak-kupit">{{ trans('app.how_to_buy') }}</a></li>
                    <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/kontakty">{{ trans('app.contacts') }}</a></li>
                    <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/forum">{{ trans('app.forum') }}</a></li>
                    <li><a href="" class="popup-btn" data-mfp-src="#question-popup">{{ trans('app.ask_a_question') }}</a></li>
                </ul>

                <p class="header-lang">
                    <span>Рус</span> / <a href="">Укр</a> / <a href="">En</a>
                </p>
                <div class="header-nav__insta">
                    <a href="https://www.instagram.com/shop_sex.com.ua/" target="_blank">
                        <picture>
                            <source data-src="/images/icons/inst.webp" srcset="/images/pixel.webp" type="image/webp">
                            <source data-src="/images/icons/inst.png" srcset="/images/pixel.png" type="image/png">
                            <img src="/images/pixel.jpg" alt="inst">
                        </picture>
                        shop_sex.com.ua
                    </a>
                </div>
            </nav>
        </div>
    </div>
    <div class="hide">
        <div id="phones-popup" class="phones-popup">
            <div class="phones-popup__header">
                <picture>
                    <source data-src="/images/icons/phone.webp" srcset="/images/pixel.webp" type="image/webp">
                    <source data-src="/images/icons/phone.png" srcset="/images/pixel.png" type="image/png">
                    <img src="/images/pixel.jpg" alt="phone">
                </picture>
                <span>
                    {{ trans('app.call') }}
                </span>
            </div>
            <ul>
                <li>
                    <picture>
                        <source data-src="/images/icons/phone.webp" srcset="/images/pixel.webp" type="image/webp">
                        <source data-src="/images/icons/phone.png" srcset="/images/pixel.png" type="image/png">
                        <img src="/images/pixel.jpg" alt="phone">
                    </picture>
                    <a href="tel:0507000197">050 700-01-97</a>
                </li>
                <li>
                    <picture>
                        <source data-src="/images/icons/phone.webp" srcset="/images/pixel.webp" type="image/webp">
                        <source data-src="/images/icons/phone.png" srcset="/images/pixel.png" type="image/png">
                        <img src="/images/pixel.jpg" alt="phone">
                    </picture>
                    <a href="tel:0958860978">095 886-09-78</a>
                </li>
            </ul>
        </div>
        <div id="question-popup" class="view-popup">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 col-sm-12 col-sm-offset-0 col-xs-12">
                        <div class="question-popup__container">
                            <p class="question-popup__container-title">{{ trans('app.ask_a_question') }}</p>
                            <form action="/sendmail" class="question-popup__form ajax_form"
                                  data-error-title="{{ trans('app.send_error') }}"
                                  data-error-message="{{ trans('app.try_to_send_a_question_after_a_while') }}"
                                  data-success-title="{{ trans('app.thanks_for_the_question') }}"
                                  data-success-message="{{ trans('app.our_manager_will_contact_you_soon') }}">
                                <textarea name="request" placeholder="Напишите свой вопрос" data-validate-required="{{ trans('app.obligatory_field') }}" data-title="Вопрос"></textarea>
                                <input type="tel" name="phone" placeholder="Номер телефона" data-title="{{ trans('app.phone') }}" data-validate-required="{{ trans('app.obligatory_field') }}" data-validate-uaphone="Неправильный номер">
                                <button type="submit">{{ trans('app.send_message') }}</button>
                            </form>
                            <button title="Close (Esc)" type="button" class="mfp-close">×</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
