<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '知乎') }}</title>
    <!-- Styles -->
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    {{--style--}}
    @yield('css')
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <my-nav></my-nav>
        @yield('content')
    </div>
    <!-- Ueditor Scripts -->
    @include('vendor.ueditor.assets')
    <script src="{{ asset('/js/app.js') }}"></script>
@yield('js')
</body>
</html>
