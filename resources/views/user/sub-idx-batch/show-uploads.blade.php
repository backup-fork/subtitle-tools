@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    @include('user.sub-idx-batch.partials.show-header')

    <div class="w-96">
        <form id="drop-container" class="relative p-4 mt-16 h-48 border-2 border-dashed" action="{{ route('user.subIdxBatch.upload', $subIdxBatch) }}" method="post" enctype="multipart/form-data">
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

            <input id="subtitles-input" accept=".sub,.idx" type="file" name="files[]" onchange="filesSelected(this)" multiple required>

            <input type="hidden" id="input-automatically-upload" name="automatically_upload" value="0">
        </form>

        <div class="flex justify-between items-center">
            <label class="cursor-pointer">
                <input type="checkbox" id="automatically-upload" {{ ($automaticallyUpload ?? false) ? 'checked' : '' }}>
                Automatically upload
            </label>

            <button form="drop-container" type="submit" class="tool-btn ml-auto">Upload</button>
        </div>
    </div>


    @if($linkedNames ?? false)
    <div class="border-l-4 border-green pl-4 py-2 mt-8">
        {{ count($linkedNames) }} {{ count($linkedNames) === 1 ? ' file has been added as a linked file.' : 'files have been added as linked files.' }}
    </div>
    @endif

    @if($unlinkedNames ?? false)
    <div class="border-l-4 border-yellow-dark pl-4 py-2 mt-8">
        {{ count($unlinkedNames) }} {{ count($unlinkedNames) === 1 ? ' file has been added as a unlinked file.' : 'files have been added as unlinked files.' }}
    </div>
    @endif

    @if($duplicateUnlinkedNames ?? false)
    <div class="border-l-4 border-red pl-4 mt-8">
        <strong>The following files were not added because they are already exist as an unlinked file in this batch</strong>

        <ul class="mt-2">
            @foreach($duplicateUnlinkedNames as $name)
            <li>{{ $name }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($duplicateLinkedNames ?? false)
    <div class="border-l-4 border-red pl-4 mt-8">
        <strong>The following files were not added because they are already exist as a linked file in this batch</strong>

        <ul class="mt-2">
            @foreach($duplicateLinkedNames as $name)
            <li>{{ $name }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($invalidNames ?? false)
    <div class="border-l-4 border-red pl-4 mt-8">
        <strong>Could not add the following files because they are not valid sub or idx files</strong>

        <ul class="mt-2">
            @foreach($invalidNames as $invalidName)
            <li>{{ $invalidName }}</li>
            @endforeach
        </ul>
    </div>
    @endif

@endsection

@push('footer')
    <script>
        function filesSelected(inputElement) {
            if (! document.getElementById('automatically-upload').checked) {
                return;
            }

            document.getElementById('input-automatically-upload').value = 1;

            document.getElementById('drop-container').submit();
        }
    </script>
@endpush
