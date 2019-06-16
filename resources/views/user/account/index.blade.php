@extends('layout.user.template', [
    'title' => 'My Account',
])

@section('content')

    <h1>My Account</h1>

    <h2 class="mt-8 mb-2">Batch Tokens</h2>
    <div>
        You have <strong>{{ $user->batch_tokens_left }}</strong> batch tokens left.
        <br>
        <br>
        <a href="{{ route('user.account.buyBatchTokens.index') }}" class="underline">Click here</a> to buy more.
    </div>

@endsection
