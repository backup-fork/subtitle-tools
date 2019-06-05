@extends('layout.guest.template', [
    'title' => 'Request password reset | Subtitle Tools',
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1 class="mb-6">Request password reset</h1>

    <form method="post">
        {{ csrf_field() }}

        <label class="block my-2 font-bold">
            Email
            <input class="block w-96 field mt-2" type="email" name="email" required autofocus>
        </label>

        <button type="submit" class="tool-btn">Request password reset</button>

    </form>

    @error('email')
    <div class="font-bold p-2 border-l-4 mb-4 border-red">
        {{ $message }}
    </div>
    @enderror

    <a href="{{ route('login') }}" class="text-black">Back to login</a>

@endsection
