@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    @include('user.sub-idx-batch.partials.show-header')


    @if($subIdxBatch->files->count() <= $user->batch_tokens_left)
        <form method="post" action="{{ route('user.subIdxBatch.start', $subIdxBatch) }}" class="max-w-md">
            {{ csrf_field() }}

            @if($subIdxBatch->files->isEmpty())
                You haven't added any files to this batch yet.
            @else
                Select languages

                @foreach($languages as $language => [$languageCode, $count])
                    <label class="block">
                        <input name="languages[]" type="checkbox" value="{{ $languageCode }}">
                        {{ $language }} ({{ $count }}x)
                    </label>
                @endforeach

                <button class="btn">Start batch</button>
            @endif

        </form>
    @else
        <div class="max-w-sm">
            You have added {{ $subIdxBatch->files->count() }} sub/idx files to this batch, but you only have {{ $user->batch_tokens_left }} batch tokens left.
            <br>
            <br>
            Before you can start this batch, you have to either remove some linked sub/idx files, or <a href="{{ route('user.account.buyBatchTokens.index') }}" class="underline">buy more batch tokens</a>.
        </div>
    @endif

@endsection
