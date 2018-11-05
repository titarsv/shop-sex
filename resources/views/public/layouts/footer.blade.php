<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-2 col-xs-3">
                @if(Request::path()!='/')
                    <a href="{{env('APP_URL')}}">
                        <img src="/images/logo.png" class="header__logo" alt="Главная">
                    </a>
                @else
                    <img src="/images/logo.png" class="header__logo" alt="Главная">
                @endif
            </div>
            <div class="col-sm-3 col-sm-offset-7 col-xs-5 col-xs-offset-4">
                <ul class="footer__contacts">
                    <li>050 971-25-69</li>
                    <li>shop_sex.com.ua</li>
                </ul>
            </div>
        </div>
    </div>
</footer>