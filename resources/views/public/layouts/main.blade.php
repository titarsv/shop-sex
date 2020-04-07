<!DOCTYPE html>
<html lang="ru">
@include('public.layouts.header')

<body class="{{ Request::path()=='/' ? ' home' : '' }}">
@if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome-Lighthouse') === false && config('app.debug') === false)
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M2XBVPW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
@include('public.layouts.header-main', ['root_category' => isset($root_category) ? $root_category : false])
<main id="main-container">
    @yield('content')
</main>
@include('public.layouts.footer')
@include('public.layouts.footer-scripts')
</body>
</html>