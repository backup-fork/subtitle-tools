<div class="flex justify-between items-center bg-white p-4 border-b">

    <div class="breadcrumbs-container">
        @yield('breadcrumbs')
    </div>

    <div class="flex">
        <div class="mr-6">
            My Account
        </div>

        <form class="" method="post" action="{{ route('logout') }}">
            {{ csrf_field() }}

            <button type="submit">Log out</button>
        </form>
    </div>
</div>
