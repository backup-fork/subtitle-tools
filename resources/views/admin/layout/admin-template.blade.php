<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="canonical" href="{{ URL::current() }}" />

    <title>Admin | Subtitle Tools</title>

    @stack('head')

    <link rel="icon" type="image/png" href="/favicon.png" />

    <link rel="stylesheet" type="text/css" href="{{ mix('css/main.css') }}" />

</head>
<body class="bg-grey-lighter">

    @include('admin.layout.header')

    <div id="app">
        @yield('content')
    </div>

    @stack('footer')

</body>
</html>
