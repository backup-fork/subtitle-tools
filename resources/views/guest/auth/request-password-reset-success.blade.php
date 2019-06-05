@extends('layout.guest.template', [
    'title' => 'Password reset requested | Subtitle Tools',
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1 class="mb-6">Password reset requested</h1>

    <p class="max-w-sm">
        An email has been sent to the given email address.
        You can use the link in the email to reset your password.
    </p>

@endsection
