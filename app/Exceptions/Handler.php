<?php

namespace App\Exceptions;

use App\Http\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
            $code = $e->getCode();
            $message = $e->getMessage();
            $exception = get_class($e);
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']));

            $tableName = 'error_logs_' . now()->toDateTime()->format('Y_m_d');

            $results = DB::select("show tables like '$tableName'");

            if (empty($results)) {
                DB::statement("CREATE TABLE `$tableName` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    `code` INT(10) NOT NULL DEFAULT 0,
                    `message` VARCHAR(255) NOT NULL DEFAULT '',
                    `exception` VARCHAR(255),
                    `file` VARCHAR(255),
                    `line` INT(11),
                    `trace` LONGTEXT,
                    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )");
            }

            DB::table($tableName)->insert([
                'code' => $code,
                'message' => $message,
                'exception' => $exception,
                'file' => $file,
                'line' => $line,
                'trace' => $trace->toJson(),
            ]);

            if (config('app.debug')) {
                $data = ApiResponse::error($code, $message, [], compact('exception', 'file', 'line', 'trace'));
            } else {
                $data = ApiResponse::error($code, $message);
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
