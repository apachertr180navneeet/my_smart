<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ session()->has('dir') ? session()->get('dir') : 'ltr' }}">
<head>
    @include('landing-page.partials._head')
</head>
<body>
    <main>
        @yield('content')
    </main>
    @include('landing-page.partials._scripts')
</body>
</html>
