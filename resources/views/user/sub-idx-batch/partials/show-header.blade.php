<div class="flex justify-between">
    <h1>Sub/Idx Batch {{ $subIdxBatch->label }}</h1>

    @if(Route::is('*.showUpload'))
        <form method="post" action="{{ route('user.subIdxBatch.delete', $subIdxBatch) }}">
            {{ csrf_field() }}
            {{ method_field('delete') }}

            <button class="btn bg-red hover:bg-red-dark" onclick="return confirm('Are you sure you want to delete this batch?')">Delete batch</button>
        </form>
    @endif
</div>

<div class="flex my-4">
    <a href="{{ route('user.subIdxBatch.showUpload', $subIdxBatch) }}" class="{{ Route::is('*.showUpload') ? 'font-bold' : '' }}">
        Upload files
    </a>

    <a href="{{ route('user.subIdxBatch.showUnlinked', $subIdxBatch) }}" class="ml-8 {{ Route::is('*.showUnlinked') ? 'font-bold' : '' }}">
        Unlinked files ({{ $subIdxBatch->unlinkedFiles->count() }})
    </a>

    <a href="{{ route('user.subIdxBatch.showLinked', $subIdxBatch) }}" class="ml-8 {{ Route::is('*.showLinked') ? 'font-bold' : '' }}">
        Linked files ({{ $subIdxBatch->files->count() }})
    </a>

    <a href="{{ route('user.subIdxBatch.showStart', $subIdxBatch) }}" class="ml-8 {{ Route::is('*.showStart') ? 'font-bold' : '' }}">
        Start batch
    </a>
</div>
