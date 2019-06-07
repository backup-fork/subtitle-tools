@extends('layout.guest.template', [
    'title' => 'Create account | Subtitle Tools',
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1 class="mb-6">Create an account</h1>

    <form method="post">
        {{ csrf_field() }}

        <label class="block my-2 font-bold">
            Email
            <input class="block w-96 field mt-2" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </label>

        <label class="block my-2 font-bold">
            Password
            <input class="block w-96 field mt-2" type="password" name="password" required>
        </label>

        <label class="block my-2 font-bold">
            Repeat password
            <input class="block w-96 field mt-2" type="password" name="password_confirmation" required>
        </label>

        <button type="submit" class="tool-btn">Create account</button>

    </form>

    @error('password')
    <div class="font-bold p-2 border-l-4 mb-4 border-red">
        {{ $message }}
    </div>
    @enderror

    @error('email')
    <div class="font-bold p-2 border-l-4 mb-4 border-red">
        {{ $message }}
    </div>
    @enderror


@endsection
