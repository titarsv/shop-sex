<!DOCTYPE html>
<html lang="ru">
@include('public.layouts.header')

<body class="{{ Request::path()=='/' ? ' home' : '' }}">
@include('public.layouts.header-main', ['root_category' => isset($root_category) ? $root_category : false])
<main id="main-container">
    @yield('content')
</main>
@include('public.layouts.footer')
@include('public.layouts.footer-scripts')
</body>
</html>