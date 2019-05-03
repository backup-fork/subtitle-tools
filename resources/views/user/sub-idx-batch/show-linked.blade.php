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
    @endif

@endsection
