<?php

namespace App\Exceptions;

use App\Support\TextFile\Exceptions\TextEncodingException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        PostTooLargeException::class,
    ];

    public function render($request, Exception $exception)
    {
        if ($exception instanceof PostTooLargeException) {
            // Somehow, using back()->withErrors doesn't work on the live server,
            // so this hack is used instead
            return response()->json([
                __('validation.file_larger_than_max_post_size'),
            ])->setStatusCode(500);
        }

        if ($exception instanceof TextEncodingException) {
            return response()->view('errors.text-encoding-exception', [], 500);
        }

        return parent::render($request, $exception);
    }
}
