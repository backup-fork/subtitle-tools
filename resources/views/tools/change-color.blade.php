@extends('layout.guest.template', [
    'title' => __('seo.title.changeColor'),
    'description' => __('seo.description.changeColor'),
    'keywords' => __('seo.keywords.changeColor'),
])

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Subtitle color changer</h1>
    <p>
        Change the color of the text in subtitle files.
    </p>


    @component('components.tool-form')

        @slot('title') Select files to color @endslot

        @slot('formats') Supported subtitle formats: srt, webvtt, ass, ssa @endslot

        @slot('buttonText') Change color @endslot

        @slot('extraAfter')
            <label class="block">
                <div class="font-bold mb-2">Color</div>
                <input type="color" name="newColor" value="{{ old('newColor', '#ef5753') }}" class="cursor-pointer" required />
            </label>
        @endslot

    @endcomponent

    <div class="max-w-md">
        <h2>Changing the color of text</h2>
        <p>
            This tools colors all the text in a subtitle file.
            Adding color to subtitles can improve readability and make the text more noticeable.
            <br>
            <br>
            If you want to remove colors from an srt file, use the <a class="underline" href="{{ route('cleanSrt') }}">srt cleaner tool</a> instead.
        </p>

        <h2>Subtitle color support</h2>
        <p>
            Not all video players and televisions support colored subtitles.
            If you have used this tool to apply a color, but the subtitles are still white, then unfortunately there isn't much you can do.
            A lot of devices simply don't support styling in subtitle files.
        </p>
    </div>

@endsection
