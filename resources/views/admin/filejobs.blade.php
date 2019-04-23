@extends('layout.admin.template')

@section('content')

    <div class="max-w-5xl text-sm pl-4 mb-16">
        <div class="flex justify-between items-center mb-4">
            <h1>File Jobs</h1>

            <form method="get" id="filters">
                <select name="encoding" class="field" onchange="document.getElementById('filters').submit()">
                    <option value="">(all encodings)</option>
                    @foreach($encodings as $encoding)
                        <option value="{{ $encoding }}" {{ request()->get('encoding') === $encoding ? 'selected' : '' }}>{{ $encoding }}</option>
                    @endforeach
                </select>

                <select name="type" class="field" onchange="document.getElementById('filters').submit()">
                    <option value="">(all types)</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request()->get('type') === $type ? 'selected' : '' }}>{{ class_basename($type) }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="flex px-2 font-bold">
            <div class="w-3/12">Original Name</div>
            <div class="w-2/12">Error Message</div>
            <div class="w-1/12">Encoding</div>
            <div class="w-1/12">Language</div>
            <div class="w-1/12">Type</div>
            <div class="w-2/12">Files</div>
            <div class="w-2/12">Finished at</div>
        </div>

        @foreach($fileJobs as $fileJob)
            @php
                $meta = optional($fileJob->inputStoredFile->meta);
            @endphp

            <div class="flex px-2 py-1 border-t hover:bg-grey-light">
                <div class="w-3/12">
                    <input type="text" class="w-11/12 bg-grey-lighter" value="{{ $fileJob->original_name }}" readonly>
                </div>
                <div class="w-2/12">
                    {{ __($fileJob->error_message) }}
                </div>
                <div class="w-1/12">
                    {{ $meta->encoding }}
                </div>
                <div class="w-1/12">
                    {{ $meta->language ? __('languages.subIdx.'.$meta->language) : '' }}
                </div>
                <div class="w-1/12">
                    {{ class_basename($meta->identified_as) }}
                </div>
                <div class="w-2/12">
                    <div class="inline-flex">
                        <a target="_blank" href="{{ route('admin.storedFiles.show', $fileJob->input_stored_file_id) }}">{{ $fileJob->input_stored_file_id }}</a>

                        <form target="_blank" method="post" action="{{ route('admin.storedFiles.download') }}" class="ml-2">
                            {{ csrf_field() }}

                            <input type="hidden" name="id" value="{{ $fileJob->input_stored_file_id }}" />
                            <button type="submit">⬇</button>
                        </form>
                    </div>

                    <span class="mx-2">🡆</span>
                    @if($fileJob->output_stored_file_id)
                        <div class="inline-flex">
                            <a target="_blank" href="{{ route('admin.storedFiles.show', $fileJob->output_stored_file_id) }}">{{ $fileJob->output_stored_file_id }}</a>

                            <form target="_blank" method="post" action="{{ route('admin.storedFiles.download') }}" class="ml-2">
                                {{ csrf_field() }}

                                <input type="hidden" name="id" value="{{ $fileJob->output_stored_file_id }}" />
                                <button type="submit">⬇</button>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="w-2/12">
                    {{ $fileJob->finished_at }}
                </div>
            </div>
        @endforeach
    </div>

@endsection
