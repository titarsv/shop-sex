<header class="header">
	<div class="container">
		<div class="row">
			<div class="header-main">
				@if(Request::path()!='/')
				<a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}">
					<div class="logo">
						<img src="/images/new-logo.svg" alt="{{ trans('app.home') }}">
					</div>
				</a>
				@else
				<div class="logo">
					<img src="/images/new-logo.svg" alt="{{ trans('app.home') }}">
				</div>
				@endif
				<div class="header-controls">
					<div class="header-top">
						<a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/checkout" class="cart-link hidden-cart">
							<div class="header__cart">
								<p class="header__cart-title">{{ trans('app.basket') }}</p>
								@if(isset($cart) && $cart->total_quantity)
								<p class="header__cart-sum">{{ number_format($cart->total_price, 2, '.', ' ') }}{{ trans('app.hryvnias') }}</p>
								<p class="header__cart-guant">{{ $cart->total_quantity }}</p>
								@endif
							</div>
						</a>
						<div class="header-spacer"></div>
						<div class="search-btn">
							<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
								<rect width="30" height="30" rx="15" fill="#FF0019"/>
								<path d="M8 13.4444C8 14.1594 8.14082 14.8674 8.41443 15.5279C8.68804 16.1885 9.08908 16.7887 9.59464 17.2942C10.1002 17.7998 10.7004 18.2008 11.3609 18.4745C12.0215 18.7481 12.7295 18.8889 13.4444 18.8889C14.1594 18.8889 14.8674 18.7481 15.5279 18.4745C16.1885 18.2008 16.7887 17.7998 17.2942 17.2942C17.7998 16.7887 18.2008 16.1885 18.4745 15.5279C18.7481 14.8674 18.8889 14.1594 18.8889 13.4444C18.8889 12.7295 18.7481 12.0215 18.4745 11.3609C18.2008 10.7004 17.7998 10.1002 17.2942 9.59464C16.7887 9.08908 16.1885 8.68804 15.5279 8.41443C14.8674 8.14082 14.1594 8 13.4444 8C12.7295 8 12.0215 8.14082 11.3609 8.41443C10.7004 8.68804 10.1002 9.08908 9.59464 9.59464C9.08908 10.1002 8.68804 10.7004 8.41443 11.3609C8.14082 12.0215 8 12.7295 8 13.4444Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M22.0002 22.0002L17.3335 17.3335" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</div>
						<div class="mob-btn popup-btn" data-mfp-src="#phones-popup">
							<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect width="30" height="30" rx="15" fill="#FF0019"/>
								<g clip-path="url(#clip0_2020_131)">
									<path d="M21.4168 14.657C21.2621 14.657 21.1138 14.5955 21.0044 14.4861C20.895 14.3767 20.8335 14.2283 20.8335 14.0736C20.8323 12.8363 20.3402 11.6501 19.4653 10.7752C18.5904 9.90025 17.4041 9.40819 16.1668 9.40695C16.0121 9.40695 15.8638 9.34549 15.7544 9.2361C15.645 9.1267 15.5835 8.97833 15.5835 8.82362C15.5835 8.66891 15.645 8.52053 15.7544 8.41114C15.8638 8.30174 16.0121 8.24028 16.1668 8.24028C17.7134 8.24198 19.1962 8.85711 20.2898 9.9507C21.3833 11.0443 21.9985 12.527 22.0002 14.0736C22.0002 14.2283 21.9387 14.3767 21.8293 14.4861C21.7199 14.5955 21.5715 14.657 21.4168 14.657ZM19.6668 14.0736C19.6668 13.1454 19.2981 12.2551 18.6417 11.5987C17.9853 10.9424 17.0951 10.5736 16.1668 10.5736C16.0121 10.5736 15.8638 10.6351 15.7544 10.7445C15.645 10.8539 15.5835 11.0022 15.5835 11.157C15.5835 11.3117 15.645 11.46 15.7544 11.5694C15.8638 11.6788 16.0121 11.7403 16.1668 11.7403C16.7857 11.7403 17.3792 11.9861 17.8168 12.4237C18.2543 12.8613 18.5002 13.4548 18.5002 14.0736C18.5002 14.2283 18.5616 14.3767 18.671 14.4861C18.7804 14.5955 18.9288 14.657 19.0835 14.657C19.2382 14.657 19.3866 14.5955 19.496 14.4861C19.6054 14.3767 19.6668 14.2283 19.6668 14.0736ZM20.9403 21.1693L21.4711 20.5574C21.809 20.2184 21.9987 19.7593 21.9987 19.2807C21.9987 18.8022 21.809 18.3431 21.4711 18.0041C21.453 17.986 20.0495 16.9063 20.0495 16.9063C19.7127 16.5856 19.2652 16.4071 18.8002 16.4078C18.3351 16.4084 17.8882 16.5882 17.5523 16.9098L16.4404 17.8466C15.5329 17.471 14.7084 16.9198 14.0145 16.2247C13.3206 15.5295 12.7708 14.7042 12.3968 13.796L13.3301 12.6876C13.6519 12.3517 13.8319 11.9047 13.8327 11.4395C13.8334 10.9743 13.6549 10.5267 13.3342 10.1898C13.3342 10.1898 12.2533 8.78803 12.2352 8.76995C11.9024 8.43497 11.4511 8.24438 10.9789 8.23936C10.5067 8.23434 10.0515 8.41529 9.71167 8.74312L9.04084 9.32645C5.07767 13.9243 13.6118 22.3925 18.3613 22.2403C18.8409 22.2431 19.3162 22.1497 19.7592 21.9658C20.2021 21.7818 20.6037 21.511 20.9403 21.1693Z" fill="white"/>
								</g>
							</svg>
						</div>
						<div class="header-anon">
							<div>
								<span>{{ trans('app.guaranteed_anonymity') }}</span>
							</div>
							<p class="header-lang">
								@if(App::getLocale() == 'ua')
								<a href="{{ Request::getRequestUri() == '/ua' ? '/' : substr(Request::getRequestUri(), 3) }}">Рус</a> /
								<span>Укр</span>{{-- / --}}
								{{-- <a href="{{ Request::getRequestUri() == '/ua' ? '/en' : '/en'.substr(Request::getRequestUri(), 3) }}">En</a>--}}
								{{--@elseif(App::getLocale() == 'en')--}}
								{{--<a href="{{ Request::getRequestUri() == '/ua' ? '/' : substr(Request::getRequestUri(), 3) }}">Рус</a> /--}}
								{{--<a href="{{ Request::getRequestUri() == '/en' ? '/ua' : '/ua'.substr(Request::getRequestUri(), 3) }}">Укр</a> /--}}
								{{--<span>En</span>--}}
								@elseif(App::getLocale() == 'ru')
								<span>Рус</span> /
								<a href="/ua{{ Request::getRequestUri() }}">Укр</a>{{-- / --}}
								{{--<a href="/en{{ Request::getRequestUri() }}">En</a>--}}
								@endif
							</p>
							<div class="header-adult">
								<span>18+</span>
								<p>{{ trans('app.the_site_contains_content_for_persons_over_the_age_of_18_18+') }}</p>
							</div>
						</div>

						<ul class="header__contacts">
							<li>
								<img src="/images/new-phone.svg" class="header-icon" alt="phone">
								<a href="tel:0507000197" class="phone-number">050 700-01-97</a>
							</li>
							<li>
								<img src="/images/new-phone.svg" class="header-icon" alt="phone">
								<a href="tel:0958860978" class="phone-number">095 886-09-78</a>
							</li>
							<li>
								<a href="https://www.instagram.com/shop_sex.com.ua/" target="_blank">
									<img src="/images/new-insta.svg" class="header-icon" alt="inst">
								</a>
							</li>
							<li>
								<a href="https://www.youtube.com/channel/UC1aB159PGPqfliNrTqQPdKQ" target="_blank">
									<img src="/images/new-youtube.svg" class="header-icon" alt="youtube">
								</a>
							</li>
						</ul>
						<div class="hmb-menu">
							<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
								<g clip-path="url(#clip0_2022_202)">
									<path d="M0 5C0 2.23858 2.23858 0 5 0H25C27.7614 0 30 2.23858 30 5V25C30 27.7614 27.7614 30 25 30H5C2.23858 30 0 27.7614 0 25V5Z" fill="#FF0019"/>
									<path d="M7 9H23" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M7 15H23" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M7 21H23" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
							</svg>
							<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
								<g clip-path="url(#clip0_2024_517)">
									<path d="M0 5C0 2.23858 2.23858 0 5 0H25C27.7614 0 30 2.23858 30 5V25C30 27.7614 27.7614 30 25 30H5C2.23858 30 0 27.7614 0 25V5Z" fill="#FF0019"/>
									<path d="M9 9.08008L21 21.0801M9 21.0801L21 9.08008" stroke="white" stroke-width="2" stroke-linecap="round"/>
								</g>
							</svg>
						</div>

						@if(Request::path()!='/')
							<a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}">
								<div class="logo">
									<img src="/images/new-logo.svg" alt="{{ trans('app.home') }}">
								</div>
							</a>
						@else
							<div class="logo">
								<img src="/images/new-logo.svg" alt="{{ trans('app.home') }}">
							</div>
						@endif


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
		</div>
	</div>
	<nav class="header-nav">
		<div class="container">
			<div class="row">
				<div class="header-nav__top">
					@if(Request::path()!='/')
					<a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}">
						<div class="logo-mob">
							<img src="/images/new-logo.svg" alt="{{ trans('app.home') }}">
						</div>
					</a>
					@else
					<div class="logo-mob">
						<img src="/images/new-logo.svg" alt="{{ trans('app.home') }}">
					</div>
					@endif
					{{--<div class="header-anon">
						<span>{{ trans('app.guaranteed_anonymity') }}</span>
					</div>--}}
					<div class="header-adult">
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
							<!-- <img src="/images/icons/catalog.png" alt="">
							<picture>
								<source data-src="/images/icons/catalog.webp" srcset="/images/pixel.webp" type="image/webp">
								<source data-src="/images/icons/catalog.png" srcset="/images/pixel.png" type="image/png">
								<img src="/images/pixel.jpg" alt="cart-main">
							</picture> -->
							{{ trans('app.catalog') }}
						</a>
					</li>
					@if($isset_new)
					<li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/novinki">{{ trans('app.new_items') }}</a></li>
					@endif
					@if($isset_actions)
					<li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/catalog/aktsii">{{ trans('app.promotions') }}</a></li>
					@endif
					<li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/o-magazine">{{ trans('app.about_store') }}</a></li>
					{{-- <li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/voprosy-i-otvety">{{ trans('app.questions_and_answers') }}</a></li>--}}
					<li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/kak-kupit">{{ trans('app.how_to_buy') }}</a></li>
					<li><a href="{{env('APP_URL')}}{{ App::getLocale() == 'ru' ? '' : '/'.App::getLocale() }}/page/kontakty">{{ trans('app.contacts') }}</a></li>
					<li><a href="" class="popup-btn" data-mfp-src="#question-popup">{{ trans('app.ask_a_question') }}</a></li>
				</ul>
				<div class="header-nav-bottom">
					<p class="header-lang">
						@if(App::getLocale() == 'ua')
							<a href="{{ Request::getRequestUri() == '/ua' ? '/' : substr(Request::getRequestUri(), 3) }}">Рус</a> /
							<span>Укр</span>{{-- / --}}
							{{-- <a href="{{ Request::getRequestUri() == '/ua' ? '/en' : '/en'.substr(Request::getRequestUri(), 3) }}">En</a>--}}
							{{--@elseif(App::getLocale() == 'en')--}}
							{{--<a href="{{ Request::getRequestUri() == '/ua' ? '/' : substr(Request::getRequestUri(), 3) }}">Рус</a> /--}}
							{{--<a href="{{ Request::getRequestUri() == '/en' ? '/ua' : '/ua'.substr(Request::getRequestUri(), 3) }}">Укр</a> /--}}
							{{--<span>En</span>--}}
						@elseif(App::getLocale() == 'ru')
							<span>Рус</span> /
							<a href="/ua{{ Request::getRequestUri() }}">Укр</a>{{-- / --}}
							{{--<a href="/en{{ Request::getRequestUri() }}">En</a>--}}
						@endif
					</p>
					<div class="header-nav__insta">
						<a href="https://www.instagram.com/shop_sex.com.ua/" target="_blank">
							<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect width="30" height="30" rx="15" fill="#FF0019"/>
								<path d="M18 9H12C10.344 9 9 10.344 9 12V18C9 19.656 10.344 21 12 21H18C19.656 21 21 19.656 21 18V12C21 10.344 19.656 9 18 9ZM15 18C13.344 18 12 16.656 12 15C12 13.344 13.344 12 15 12C16.656 12 18 13.344 18 15C18 16.656 16.656 18 15 18ZM18.21 12.372C17.88 12.372 17.61 12.102 17.61 11.772C17.61 11.442 17.88 11.172 18.21 11.172C18.54 11.172 18.81 11.442 18.81 11.772C18.81 12.102 18.54 12.372 18.21 12.372Z" fill="white"/>
								<path d="M15.001 17C16.1055 17 17.001 16.1046 17.001 15C17.001 13.8954 16.1055 13 15.001 13C13.8964 13 13.001 13.8954 13.001 15C13.001 16.1046 13.8964 17 15.001 17Z" fill="white"/>
							</svg>
						</a>
						<a href="https://www.youtube.com/channel/UC1aB159PGPqfliNrTqQPdKQ" target="_blank">
							<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect width="30" height="30" rx="15" fill="#FF0019"/>
								<path d="M18.8182 9C19.236 9 19.6498 9.08622 20.0358 9.25373C20.4218 9.42125 20.7726 9.66678 21.0681 9.97631C21.3635 10.2858 21.5979 10.6533 21.7578 11.0577C21.9177 11.4621 22 11.8956 22 12.3333V17.6667C22 18.1044 21.9177 18.5379 21.7578 18.9423C21.5979 19.3467 21.3635 19.7142 21.0681 20.0237C20.7726 20.3332 20.4218 20.5787 20.0358 20.7463C19.6498 20.9138 19.236 21 18.8182 21H11.1818C10.764 21 10.3502 20.9138 9.96419 20.7463C9.57815 20.5787 9.22739 20.3332 8.93193 20.0237C8.33523 19.3986 8 18.5507 8 17.6667V12.3333C8 11.4493 8.33523 10.6014 8.93193 9.97631C9.52864 9.35119 10.3379 9 11.1818 9H18.8182ZM13.0909 13V17C13.091 17.1179 13.1209 17.2337 13.1777 17.3355C13.2344 17.4373 13.3159 17.5216 13.4138 17.5796C13.5117 17.6377 13.6226 17.6675 13.7351 17.6661C13.8477 17.6646 13.9578 17.6319 14.0544 17.5713L17.2362 15.5713C17.3303 15.5121 17.4081 15.4283 17.4622 15.3282C17.5162 15.2281 17.5446 15.115 17.5446 15C17.5446 14.885 17.5162 14.7719 17.4622 14.6718C17.4081 14.5717 17.3303 14.4879 17.2362 14.4287L14.0544 12.4287C13.9578 12.3681 13.8477 12.3354 13.7351 12.3339C13.6226 12.3325 13.5117 12.3623 13.4138 12.4204C13.3159 12.4784 13.2344 12.5627 13.1777 12.6645C13.1209 12.7663 13.091 12.8821 13.0909 13Z" fill="white"/>
							</svg>
						</a>
					</div>
				</div>
			</div>
		</div>
	</nav>
	<div class="hide">
		<div id="phones-popup" class="phones-popup">
			<div class="phones-popup__header">
				<img src="/images/phone.svg" class="phone-icon" alt="phone">
				<span>
					{{ trans('app.call') }}
				</span>
			</div>
			<ul>
				<li>
					<img src="/images/phone.svg" class="phone-icon" alt="phone">
					<a href="tel:0507000197">050 700-01-97</a>
				</li>
				<li>
					<img src="/images/phone.svg" class="phone-icon" alt="phone">
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
							<form action="/sendmail" class="question-popup__form ajax_form" data-error-title="{{ trans('app.send_error') }}" data-error-message="{{ trans('app.try_to_send_a_question_after_a_while') }}" data-success-title="{{ trans('app.thanks_for_the_question') }}" data-success-message="{{ trans('app.our_manager_will_contact_you_soon') }}">
								<textarea name="request" placeholder="{{ trans('app.write_your_question') }}" data-validate-required="{{ trans('app.obligatory_field') }}" data-title="Вопрос"></textarea>
								<input type="tel" name="phone" placeholder="{{ trans('app.phone_number') }}" data-title="{{ trans('app.phone') }}" data-validate-required="{{ trans('app.obligatory_field') }}" data-validate-uaphone="Неправильный номер">
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
