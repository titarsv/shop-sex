@extends('public.layouts.main')
@section('meta')
    <title>Ошибка 404. Страница не найдена</title>
@endsection
@section('content')
    <nav class="breadrumbs">
        <div class="container">
            <ul class="breadrumbs-list">
                <li class="breadrumbs-item">
                    <a href="/">Главная</a><i>→</i>
                </li>
                <li class="breadrumbs-item">
                    Страница не найдена
                </li>
            </ul>
        </div>
    </nav>
    <main id="error-page">
        <div class="container">
            <h1><span>404 Error</span></h1>

            <div class="error-404">
                <p>Страница не найдена</p>
                <img src="/images/404.png" alt="404"/>
            </div>
        </div>
    </main>
@endsection