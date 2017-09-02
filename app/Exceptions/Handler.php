<?php

namespace App\Exceptions;

use Carbon\Carbon;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    public function report(Exception $exception)
    {
        if($exception instanceof PostTooLargeException) {
            file_put_contents(
                storage_path('/logs/post-size.log'),
                Carbon::now() . '|' . request()->path() . '|PostTooLargeException' . PHP_EOL,
                FILE_APPEND
            );
        }
        else {
            parent::report($exception);
        }
    }

    public function render($request, Exception $exception)
    {
        if($exception instanceof PostTooLargeException) {
            // Somehow, using back()->withErrors doesn't work on the live server,
            // so this hack is used instead
            return response()->json([
                __('validation.file_larger_than_max_post_size'),
            ])->setStatusCode(500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
