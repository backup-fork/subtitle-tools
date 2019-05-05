@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    @include('user.sub-idx-batch.partials.show-header')

    <div>
        Linked files
    </div>

    @if($subIdxBatch->files->isEmpty() && $subIdxBatch->unlinkedFiles->isEmpty())
        You haven't uploaded any files to this batch yet.
    @elseif($subIdxBatch->files->isEmpty())
        No files have been linked yet.
    @else
        The sub and idx files below have been linked and will be processed when you start the batch.

        @foreach($subIdxBatch->files as $batchFile)
        <div class="my-4">
            <div>Sub: {{ $batchFile->sub_original_name }}</div>
            <div class="text-xs">Idx: {{ $batchFile->idx_original_name }}</div>
            <form action="{{ route('user.subIdxBatch.unlink', $batchFile) }}" method="post">
                {{ csrf_field() }}
                <button>unlink</button>
            </form>
        </div>
        @endforeach


    @endif

@endsection
