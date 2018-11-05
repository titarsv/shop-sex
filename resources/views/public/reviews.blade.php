@extends('public.layouts.main')
@section('meta')
    <title>Отзывы</title>
    <meta name="description" content="Отзывы">
    <meta name="keywords" content="Отзывы">
@endsection

@section('breadcrumbs')
    {!! Breadcrumbs::render('search') !!}
@endsection

@section('content')
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-4 hidden-xs">
                    <div class="aside-filter-menu-item">
                        <div class="aside-filter-menu-item-title aside-block">
                            <a href="{{env('APP_URL')}}/page/contact"><p>Контакты</p></a>
                        </div>
                    </div>
                    <div class="aside-filter-menu-item">
                        <div class="aside-filter-menu-item-title aside-block">
                            <a href="javascript:void(0);" class="active-aside-link"><p>Отзывы</p></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-8 col-xs-12 review-scroll-container jScrollPane">
                    <div class="col-sm-12 margin">
                        <h1 class="title">Отзывы</h1>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        @foreach($reviews as $review)
                            <div class="review-product-item path-underline">
                                <a href="{{env('APP_URL')}}/product/{{ $review->product->url_alias }}">
                                    <div class="cart-img-wrp history-prod-img">
                                        <img src="{{  $review->product->image->url() }}" alt="{{ $review->product->name }}">
                                    </div>
                                </a>
                                <div class="review-product-wrp">
                                    <div class="review-container">
                                        <div class="cart-prod-description">
                                            <a href="{{env('APP_URL')}}/product/{{ $review->product->url_alias }}"><h5 class="default-link-hover review-prod-title">{{ $review->product->name }}</h5></a>
                                        </div>
                                        <div class="review stars">
                                            @for($i=0;$i<5;$i++)
                                                @if($i < $review->grade)
                                                    <i class="stars">&#xE802</i>
                                                @else
                                                    <i class="stars">&#xE80B</i>
                                                @endif
                                            @endfor
                                        </div>
                                        <p class="review-text">
                                            {{ $review->review }}
                                        </p>
                                        <span class="review-info">
                                            @if(!empty($review->user))
                                                {{ $review->user->first_name }}
                                            @else
                                                {{ $review->author }}
                                            @endif
                                            - {!! $review->created_at !!}
                                        </span>
                                    </div>
                                    @if(!empty($review->answer))
                                    <div class="answer-container">
                                        <p class="review-text">
                                            {!! $review->answer !!}
                                        </p>
                                        <span class="review-info">shop-sex.com.ua - {!! $review->updated_at !!}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-3 col-sm-12 col-xs-12 cart-receipt-wrp">
                    <form action="" class="response-form ajax_form"
                          data-error-title="Ошибка отправки!"
                          data-error-message="Попробуйте отправить заявку через некоторое время."
                          data-success-title="Спасибо за заявку!"
                          data-success-message="Наш менеджер свяжется с вами в ближайшее время.">
                        <div class="row">
                            <div class="col-md-12 path-underline">
                                <p class="response-form-title margin">Ваш отзыв о магазине</p>
                                <div class="response-form-stars margin">
                                    <input type="radio" name="stars" value="1" id="1-star" class="radio" data-title="Оценка">
                                    <label for="1-star" class="radio-stars"></label>

                                    <input type="radio" name="stars" value="2" id="2-star" class="radio" data-title="Оценка">
                                    <label for="2-star" class="radio-stars"></label>

                                    <input type="radio" name="stars" value="3" id="3-star" class="radio" data-title="Оценка">
                                    <label for="3-star" class="radio-stars"></label>

                                    <input type="radio" name="stars" value="4" id="4-star" class="radio" data-title="Оценка">
                                    <label for="4-star" class="radio-stars"></label>

                                    <input type="radio" name="stars" value="5" id="5-star" class="radio" data-title="Оценка">
                                    <label for="5-star" class="radio-stars"></label>
                                </div>
                                <textarea type="text" class="response-form-text margin" placeholder="Вам понравилось обслуживание? Сообщите нам, что вы думаете"></textarea>
                            </div>
                            <div class="col-md-12 margin">
                                <div class="profile-data-item response-input-wrp">
                                    <label class="data-name">Имя</label>
                                    <input type="text" name="username" class="response-input" data-title="Имя">
                                </div>
                                <div class="profile-data-item response-input-wrp">
                                    <label class="data-name">Почта</label>
                                    <input type="text" name="email" class="response-input" data-title="Почта" data-validate-required="Обязательное поле" data-validate-email="Неправильный email">
                                </div>
                                <div class="profile-data-item response-input-wrp">
                                    <label class="data-name">Телефон</label>
                                    <input type="text" name="phone" class="response-input response-phone-input" placeholder="380__ _ _ __" data-title="Телефон" data-validate-required="Обязательное поле" data-validate-uaphone="Неправильный номер">
                                </div>
                            </div>
                            <div class="col-md-12 no-padding">
                                <button type="submit" class="process" style="color: #F5F5F5; font-family: 'HelveticaNeue'; font-size: 20px; font-weight: bold; line-height: 25px; text-align: center; width: 100%; border: none;  outline: none;">Отправить отзыв</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection