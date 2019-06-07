@extends('layout.guest.template', [
    'title' => 'Login | Subtitle Tools',
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1 class="mb-6">Login</h1>

    <form method="post" action="{{ route('login.post') }}">
        {{ csrf_field() }}
        <input type="hidden" value="1" name="remember">

        <label class="block my-2 font-bold">
            Email
            <input class="block w-64 field mt-2" type="text" name="email" value="{{ old('email') }}" required autofocus>
        </label>

        <label class="block my-2 font-bold">
            Password
            <input class="block w-64 field mt-2" type="password" name="password" required>
        </label>

        <button type="submit" class="tool-btn">Login</button>

    </form>

    @error('email')
        <div class="font-bold p-2 border-l-4 border-red">
            {{ $message }}
        </div>
    @enderror


    <div class="mt-6">
        <a href="{{ route('register.index') }}" class="text-black">Create an account</a>

        <br>
        <br>

        <a href="{{ route('requestPasswordReset.index') }}" class="text-black">Password reset</a>
    </div>

@endsection
