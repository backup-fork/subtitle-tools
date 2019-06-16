@extends('layout.guest.template', [
    'title' => __('seo.title.contact'),
    'description' => __('seo.description.contact'),
    'keywords' => __('seo.keywords.contact'),
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Contact</h1>
    <p class="max-w-sm">
        If you want to give feedback, or simply send me a message, you can do that using the form below.
        <br><br>
        If you would like a response, don't forget to include your email.
    </p>

    @if($sentMessage ?? false)
        <div class="w-full md:w-1/2 mt-4 p-2 rounded bg-green-lighter border-l-2 border-green">
            Thank you for your message!
        </div>
    @else
        @foreach ($errors->all() as $error)
            <div class="w-full md:w-1/2 mt-4 p-2 rounded bg-red-lighter border-l-2 border-red">
                {{ $error }}
            </div>
        @endforeach

        <form class="mt-4 w-full max-w-sm p-2" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <input class="field block w-full mb-2" type="text" name="email" value="{{ old('email') }}" placeholder="Email (optional)">

            <textarea class="field block w-full h-24" name="message" placeholder="Your message here..." required>{{ old('message') }}</textarea>

            <div class="flex items-center mt-2">
                <div id="hard-math" class="mr-4">3</div>

                <input type="text" class="field w-10" name="captcha" placeholder="?" required>

                <button class="tool-btn block my-0 ml-auto">Send message</button>
            </div>
        </form>
    @endif

@endsection

@push('footer')
    <script>
        setTimeout(function () {
            var el = document.getElementById('hard-math');

            el.innerText += ' + 2 =';
        }, 1000)
    </script>
@endpush
