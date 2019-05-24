@extends('layout.base-template', [
    'title' => ($title ?? null).' | Subtitle Tools',
    'description' => $description ?? null,
    'keywords' => $keywords ?? null,
    'bodyClasses' => 'bg-grey-lighter h-full',
    'htmlClasses' => 'h-full rem-14',
])

@section('body')

<div class="mx-8">

    @include('layout.user.header')

    <div class="flex">

        <div class="w-64">
            @include('layout.user.sidebar')
        </div>

        <div class="flex-grow">
            <div class="mt-8 p-4 max-w-xl rounded shadow bg-white">
                @yield('content')
            </div>
        </div>

    </div>

</div>

@endsection
