@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    <h1 class="mb-4">Batch</h1>

    <div id="sub-idx-batch-result" data-batch-id="{{ $subIdxBatch->id }}"></div>

@endsection
