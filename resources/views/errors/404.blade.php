@extends('public.layouts.main')
@section('meta')
    <title>Ошибка 404. Страница не найдена</title>
@endsection
@section('content')
    <section class="section-1 err404" style="background: #000 center url(/images/main.jpg) no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-md-5 col-sm-7 col-xs-8">
                    <p class="main-title">Ошибка 404</p>
                    <p class="main-title">Кажется эта страница сейчас<br>у подруги ночует или её не существует</p>
                    <span>Мы уже вызвали полицию и она работает над этим вопросом. Приносим извинения за неудобства.</span>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <a href="/" class="banner-btn" onclick="window.history.back();">< Назад</a>
                </div>
            </div>
        </div>
    </section>
@endsection