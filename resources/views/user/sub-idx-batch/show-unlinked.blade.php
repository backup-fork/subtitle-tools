@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    @include('user.sub-idx-batch.partials.show-header')

    <div>
        Unlinked files
    </div>

    @if($subIdxBatch->files->isEmpty() && $subIdxBatch->unlinkedFiles->isEmpty())
        You haven't uploaded any files to this batch yet.
    @elseif($subIdxBatch->unlinkedFiles->isEmpty())
        All uploaded sub/idx files have been linked
    @else
        The sub and idx files listed below have not been linked yet.
    @endif

@endsection
