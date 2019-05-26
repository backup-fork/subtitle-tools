@extends('layout.user.template', [
    'title' => 'Sub/Idx Batch'
])

@section('content')

    @include('user.sub-idx-batch.partials.show-header')

    <form method="post" action="{{ route('user.subIdxBatch.link', $subIdxBatch) }}">
        {{ csrf_field() }}

        @if($subIdxBatch->files->isEmpty() && $subIdxBatch->unlinkedFiles->isEmpty())
            You haven't uploaded any files to this batch yet.
        @elseif($subIdxBatch->unlinkedFiles->isEmpty())
            All uploaded sub/idx files have been linked
        @else
            The sub and idx files listed below have not been linked yet.

            <h2 class="mb-2">Unlinked sub files</h2>
            @forelse($unlinkedSubFiles as $unlinkedSub)
                <div class="mb-2 pb-2 border-b">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="sub" value="{{ $unlinkedSub->id }}" class="mr-2" required>
                        {{ $unlinkedSub->original_name }}
                    </label>
                </div>
            @empty
                There are no unlinked sub files.
            @endforelse


            <h2 class="mt-8 mb-2">Unlinked idx files</h2>
            @forelse($unlinkedIdxFiles as $unlinkedIdx)
                <div class="mb-2 pb-2 border-b">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="idx" value="{{ $unlinkedIdx->id }}" class="mr-2" required>
                        {{ $unlinkedIdx->original_name }}
                    </label>
                </div>
            @empty
                There are no unlinked idx files.
            @endforelse


            @if($unlinkedSubFiles && $unlinkedIdxFiles)
            <button class="btn mt-8">Link selected files</button>
            @else
            <button class="btn bg-grey cursor-not-allowed mt-8" disabled title="You don't have both an unlinked sub file and an unlinked idx file.">Link selected files</button>
            @endif

        @endif

    </form>

    @error('alreadyLinked')
    <div class="border-l-4 border-red pl-4 mt-8 py-2">
        These two files can't be linked because the exact same files are already linked in this batch.
    </div>
    @enderror

@endsection
