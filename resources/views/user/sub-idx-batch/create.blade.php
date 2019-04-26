@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')
    <h1 class="mb-4">Create a new Sub/Idx batch</h1>

    <form method="post" action="{{ route('user.subIdxBatch.store') }}">
        {{ csrf_field() }}

        <input type="number" name="max_files" class="field" value="{{ old('max_files', 100) }}" required>

        <button class="btn inline-block">Create</button>
    </form>
@endsection
