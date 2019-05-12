@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    <h1>Batch</h1>

    @foreach($subIdxBatch->subIdxes as $subIdx)
        <div>{{ $subIdx->original_name }}</div>
    @endforeach

@endsection
