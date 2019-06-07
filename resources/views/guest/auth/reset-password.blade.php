@extends('layout.guest.template', [
    'title' => 'Reset password | Subtitle Tools',
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1 class="mb-6">Reset password</h1>

    <form method="post">
        {{ csrf_field() }}

        <label class="block my-2 font-bold">
            Email
            <input class="block w-96 field mt-2" type="email" name="email" value="{{ $email }}" required autofocus>
        </label>

        <label class="block my-2 font-bold">
            New password
            <input class="block w-96 field mt-2" type="password" name="password" required>
        </label>

        <label class="block my-2 font-bold">
            Repeat new password
            <input class="block w-96 field mt-2" type="password" name="password_confirmation" required>
        </label>

        <button type="submit" class="tool-btn">Reset password</button>

    </form>

    @error('email')
    <div class="font-bold p-2 border-l-4 mb-4 border-red">
        {{ $message }}
    </div>
    @enderror


@endsection
