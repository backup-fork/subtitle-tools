@extends('layout.user.template', [
    'title' => 'Support and feedback'
])

@section('content')

    <h1>Support and feedback</h1>

    If you have a question or want to give feedback, please fill in the form below.

    @if($messageSent ?? false)
        Thank you for your message. I'll try my best to reply as soon as possible.
    @else
        <form class="mt-4 w-full max-w-md p-2" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}

            <textarea class="field block w-full h-48" name="message" placeholder="Your message here..." required>{{ old('message') }}</textarea>

            <button class="tool-btn block mt-4 ml-auto">Send message</button>
        </form>
    @endif

@endsection
