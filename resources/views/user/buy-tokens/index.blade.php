@extends('layout.user.template', [
    'title' => 'Buy batch tokens',
])

@section('content')

    <h1 class="mb-4">Buy batch tokens</h1>

    <p class="text-xl">
        You currently have <strong>{{ $user->batch_tokens_left }}</strong> batch tokens.
    </p>

    <p class="mt-8 max-w-md">
        Batch tokens are used to convert sub/idx subtitles using the sub/idx batch tool.
        With one token you can convert one sub/idx file.
        Tokens never expire.
        <br>
        <br>
        You can only buy tokens in packs of 100. If you only want to convert a few sub/idx files, you can use the <a href="{{ route('subIdx') }}" class="underline">free sub/idx tool</a> instead.
    </p>

    <form action="" class="mt-8">
        <div class="flex items-center text-xl">
            Buy <input name="amount_of_tokens" type="number" min="100" value="100" step="100" onchange="updateTotalPrice(this)" class="field mx-4 w-24 border rounded"> tokens for

            <span class="ml-2 font-bold" id="total-price">$7.99</span>
        </div>

        <button class="btn mt-4">Buy tokens</button>
    </form>

@endsection


@push('footer')
    <script>
        function updateTotalPrice(inputEl)
        {
            const mod = inputEl.value % 100;

            if (mod !== 0) {
                const newValue = inputEl.value - mod;

                inputEl.value = newValue === 0 ? 100 : newValue;

                return updateTotalPrice(inputEl);
            }

            const total = (inputEl.value / 100) * 7.99;

            document.getElementById('total-price').innerText = `$${total.toFixed(2)}`;
        }
    </script>
@endpush
