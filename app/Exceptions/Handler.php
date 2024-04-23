<?php

namespace App\Exceptions;

use App\Http\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e) {
            if (config('app.debug')) {

                $data = ApiResponse::error($e->getCode(), $e->getMessage(), [], [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all(),
                ]);
            } else {
                $data = ApiResponse::error($e->getCode(), $e->getMessage());
            }

            return new JsonResponse(
                $data,
                $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500,
                $e instanceof HttpExceptionInterface ? $e->getHeaders() : [],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );
        });
    }
}
