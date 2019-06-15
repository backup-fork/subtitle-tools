@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    <div class="flex justify-between items-center">
        <h1>Sub/Idx Batches</h1>

        @if($user->batch_tokens_left > 0)
        <form method="post" action="{{ route('user.subIdxBatch.store') }}">
            {{ csrf_field() }}

            <button class="btn">Create new</button>
        </form>
        @elseif($user->batch_tokens_used > 0)
            <div class="btn bg-grey cursor-not-allowed" title="You can't create a batch because you have 0 batch tokens left">Create new</div>
        @endif
    </div>

    @forelse($subIdxBatches as $batch)
        <div class="flex max-w-sm p-2 mt-4 rounded border bg-white shadow">
            <div class="w-24">
                <a href="{{ route($batch->started_at ? 'user.subIdxBatch.show' : 'user.subIdxBatch.showUpload', $batch) }}">Batch {{ $batch->label }}</a>
            </div>
            <div>{{ $batch->started_at ? ($batch->finished_at ? 'Finished' : 'Processing') : 'Not started yet' }}</div>
        </div>
    @empty

        @if($user->batch_tokens_left === 0)
            <p class="max-w-md text-lg">
                This tool is a batch version of the free <a href="{{ route('subIdx') }}" target="_blank" class="underline">sub/idx to srt converter tool.</a>
                This tool makes it convenient to convert hundreds of sub/idx files at the same time.
                <br>
                <br>
                You need to batch tokens to use this tool. With one token you can convert one sub/idx file.
            </p>

            <a href="{{ route('user.account.buyBatchTokens.index') }}" class="btn text-lg inline-block mt-4">Buy batch tokens</a>
        @else
            <p>
                You haven't created any sub/idx batches yet.
            </p>
        @endif

    @endforelse

@endsection
