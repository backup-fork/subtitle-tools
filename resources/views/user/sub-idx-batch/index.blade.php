@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    <div class="max-w-sm flex justify-between items-center">
        <h1>Sub/Idx Batches</h1>

        <a href="{{ route('user.subIdxBatch.create') }}" class="btn">Create</a>
    </div>

    @forelse($subIdxBatches as $batch)
        <div class="flex max-w-sm p-2 mt-4 rounded border bg-white shadow">
            <div class="w-24">
                <a href="{{ route($batch->started_at ? 'user.subIdxBatch.show' : 'user.subIdxBatch.showUpload', $batch) }}">Batch {{ $batch->label }}</a>
            </div>
            <div>{{ $batch->started_at ? ($batch->finished_at ? 'Finished' : 'Processing') : 'Not started yet' }}</div>
        </div>
    @empty
        You do not have any sub/idx batches yet.
    @endforelse

@endsection
