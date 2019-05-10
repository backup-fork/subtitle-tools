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

            @foreach($languages as $language => [$languageCode, $count])
                <label class="block">
                    <input name="languages[]" type="checkbox" value="{{ $languageCode }}">
                    {{ $language }} ({{ $count }}x)
                </label>
            @endforeach

            <button class="btn">Start batch</button>
        @endif

    </form>

@endsection
