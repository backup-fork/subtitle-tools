<div class="flex justify-between items-center p-4 border-b">

    <h2 class="m-0 text-xl font-normal text-center">Subtitle Tools</h2>


    <div class="flex">
        <div class="mr-6">
            My Account
        </div>

        <form method="post" action="{{ route('logout') }}">
            {{ csrf_field() }}

            <button type="submit">Log out</button>
        </form>
    </div>
</div>
