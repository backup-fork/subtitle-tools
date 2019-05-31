@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    @include('user.sub-idx-batch.partials.show-header')


    @if($subAlreadyExistsAsUnlinked ?? false)
        <div class="border-l-4 border-red pl-4 my-4">
            The .sub file was deleted because the exact same file is already added as an unlinked file.
        </div>
    @endif

    @if($idxAlreadyExistsAsUnlinked ?? false)
        <div class="border-l-4 border-red pl-4 my-4">
            The .idx file was deleted because the exact same file is already added as an unlinked file.
        </div>
    @endif


    @if($subIdxBatch->files->isEmpty() && $subIdxBatch->unlinkedFiles->isEmpty())
        You haven't uploaded any files to this batch yet.
    @elseif($subIdxBatch->files->isEmpty())
        No files have been linked yet.
    @else
        <div class="max-w-lg">
            The sub and idx files below have been linked and will be processed when you start the batch.
            You can select the languages you want to extract from the files below on the "start batch" page.
        </div>

        <div class="border-b my-4"></div>

        @foreach($subIdxBatch->files as $batchFile)
        <div class="flex p-1 mt-4 hover:bg-grey-lighter">
            <div class="flex-grow">
                <div class="mb-2">{{ $batchFile->sub_original_name }}</div>
                <div class="text-sm">{{ $batchFile->idx_original_name }}</div>
            </div>

            <form action="{{ route('user.subIdxBatch.unlink', $batchFile) }}" method="post">
                {{ csrf_field() }}
                <button class="mt-2 mr-2" title="Unlink">
                    <svg class="h-5 w-5">
                        <use xlink:href="#unlink"></use>
                    </svg>
                </button>
            </form>
        </div>
        @endforeach


    @endif

@endsection
