<h1>Sub/Idx Batch {{ $subIdxBatch->label }}</h1>

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
