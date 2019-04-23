@extends('layout.guest.template', [
    'title' => 'Login | Subtitle Tools',
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Login</h1>

    <form method="post" action="{{ route('login.post') }}">
        {{ csrf_field() }}

        <label class="block my-2 font-bold">
            Email
            <input class="block field" type="text" name="email" value="{{ old('email') }}" required autofocus>
        </label>

        <label class="block my-2 font-bold">
            Password
            <input class="block field" type="password" name="password" required>
        </label>

        <input class="hidden" type="checkbox" name="remember" checked>

        <button type="submit" class="tool-btn">Login</button>

    </form>

@endsection
