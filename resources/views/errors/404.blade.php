@extends('layout.guest.template', [
    'title' => __('seo.title.404'),
    'description' => __('seo.description.404'),
    'keywords' => __('seo.keywords.404'),
])

@section('content')

    <h1>404 - Page not Found</h1>
    <p>
        <a href="{{ route('home') }}">Back to homepage</a>
    </p>

@endsection
