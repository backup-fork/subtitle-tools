@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    @include('user.sub-idx-batch.partials.show-header')

    <div>
        Start batch
    </div>

    <form method="post" action="{{ route('user.subIdxBatch.start', $subIdxBatch) }}" class="max-w-md">
        {{ csrf_field() }}

        @if($subIdxBatch->files->isEmpty())
            You haven't added any files to this batch yet.
        @else
            Select languages
        @endif

    </form>

@endsection
