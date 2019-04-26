@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')
    <div class="max-w-lg flex justify-between items-center">
        <h1>Sub/Idx Batches</h1>

        <a href="{{ route('user.subIdxBatch.create') }}" class="btn">Create</a>
    </div>

    @forelse($subIdxBatches as $batch)
        <div>{{ $batch->id }}</div>
    @empty
        You do not have any sub/idx batches yet.
    @endforelse
@endsection
