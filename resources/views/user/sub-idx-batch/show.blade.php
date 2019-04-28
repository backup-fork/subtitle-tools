@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    <h1>Sub/Idx Batch</h1>

    <div>
        Step 1: Upload sub/idx files
    </div>

    <div class="w-96">
        <form id="drop-container" class="relative p-4 mt-16 h-48 border-2 border-dashed" action="{{ route('user.subIdxBatch.upload', $subIdxBatch) }}" method="post" enctype="multipart/form-data" >
            {{ csrf_field() }}

            <div class="hidden dropzone-instructions items-center justify-center flex-col">
                @include('helpers.svg.file', ['classes' => 'w-12'])

                <span class="text-xl mt-4 font-bold">
                    Drop sub/idx files here
                </span>
            </div>

            <div id="dropzone-error" class="hidden items-center justify-center flex-col">
                @include('helpers.svg.error-circle', ['classes' => 'w-12'])

                <span id="dropzone-error-text" class="text-xl mt-4 w-48 text-center font-bold">
                    Oops
                </span>
            </div>

            <input id="subtitles-input" accept=".sub,.idx" type="file" name="files[]" multiple required>

        </form>

        <button form="drop-container" type="submit" class="tool-btn ml-auto">Upload</button>
    </div>

@endsection
