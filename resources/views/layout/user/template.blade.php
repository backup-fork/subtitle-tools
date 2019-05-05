@extends('layout.base-template', [
    'title' => ($title ?? null).' | Subtitle Tools',
    'description' => $description ?? null,
    'keywords' => $keywords ?? null,
    'bodyClasses' => 'bg-grey-lighter h-full',
    'htmlClasses' => 'h-full rem-14',
])

@section('body')

<div id="app" class="flex flex-grow">

    <div class="relative w-64">
        @include('layout.user.sidebar')
    </div>

    <div class="flex-grow">
        @include('layout.user.header')

        <div class="p-6">
            @yield('content')
        </div>
    </div>

</div>

@endsection
