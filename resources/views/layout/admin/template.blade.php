@extends('layout.base-template', [
    'title' => $title ?? null,
    'description' => $description ?? null,
    'keywords' => $keywords ?? null,
    'bodyClasses' => 'bg-grey-lighter h-full',
    'htmlClasses' => 'h-full',
])

@section('body')

<div id="app" class="flex h-full">

    <div class="relative w-48 mr-6 border-r bg-white">
        @include('layout.admin.sidebar')
    </div>

    <div class="flex-grow mt-6 mr-6">
        @yield('content')
    </div>

</div>

@endsection
