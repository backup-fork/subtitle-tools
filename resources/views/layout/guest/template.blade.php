@extends('layout.base-template', [
    'title' => $title ?? null,
    'description' => $description ?? null,
    'keywords' => $keywords ?? null,
    'bodyClasses' => 'font-open-sans relative overflow-hidden overflow-y-scroll',
])

@section('body')

    @include('layout.guest.header')

    @include('layout.guest.global-notification')

    <div id="app" class="container mx-auto px-4">
        @yield('content')
    </div>

    @include('layout.guest.footer')

@endsection
