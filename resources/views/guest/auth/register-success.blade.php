@extends('layout.guest.template', [
    'title' => 'Account created | Subtitle Tools',
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1 class="mb-6">Account created</h1>

    <div class="max-w-sm">
        Your account has been created.
        Before you can login you must verify your email address.
        <br>
        <br>
        An email has been sent to you.
    </div>

@endsection
