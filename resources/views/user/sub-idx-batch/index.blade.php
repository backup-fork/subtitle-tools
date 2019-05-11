@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')
    <div class="max-w-lg flex justify-between items-center">
        <h1>Sub/Idx Batches</h1>

        <a href="{{ route('user.subIdxBatch.create') }}" class="btn">Create</a>
    </div>

    @forelse($subIdxBatches as $batch)
        <div>
            <a href="{{ route($batch->started_at ? 'user.subIdxBatch.show' : 'user.subIdxBatch.showUpload', $batch) }}">{{ $batch->id }}</a>
        </div>
    @empty
        You do not have any sub/idx batches yet.
    @endforelse
@endsection
