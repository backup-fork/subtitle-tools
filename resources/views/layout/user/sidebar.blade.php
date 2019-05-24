<div class="fixed flex flex-col h-full w-48 mr-16 pt-8 text-lg">

    <a href="{{ route('user.dashboard.index') }}" class="text-black pl-6 border-l-4 w-full py-4 {{ Route::is('user.dashboard*') ? 'border-red-light font-bold' : 'border-grey-lighter' }}">Dashboard</a>

    <a href="{{ route('user.subIdxBatch.index') }}" class="text-black pl-6 border-l-4 w-full py-4 {{ Route::is('user.subIdxBatch*') ? 'border-red-light font-bold' : 'border-grey-lighter' }}">Sub/Idx Batches</a>

</div>
