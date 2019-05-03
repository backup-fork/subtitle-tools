<h1>Sub/Idx Batch</h1>

<div class="flex my-4">
    <a href="{{ route('user.subIdxBatch.showUpload', $subIdxBatch) }}" class="{{ Route::is('*.showUpload') ? 'font-bold' : '' }}">
        Upload files
    </a>

    <a href="{{ route('user.subIdxBatch.showUnlinked', $subIdxBatch) }}" class="mx-8 {{ Route::is('*.showUnlinked') ? 'font-bold' : '' }}">
        Unlinked files ({{ $subIdxBatch->unlinkedFiles->count() }})
    </a>

    <a href="{{ route('user.subIdxBatch.showLinked', $subIdxBatch) }}" class="{{ Route::is('*.showLinked') ? 'font-bold' : '' }}">
        Linked files ({{ $subIdxBatch->files->count() }})
    </a>
</div>
