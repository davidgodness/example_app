<?php

namespace App\Http;

use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiResponse
{

    public static function success($data = []): array
    {
        return [
            'message' => 'ok',
            'data' => $data
        ];
    }

    public static function error(\Throwable $e): array
    {
        $ret = [
            'data' => [],
        ];

        if (config('app.debug')) {
            $ret['message'] = $e->getMessage();
            $ret['debug'] = [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all(),
            ];
        } else {
            $ret['message'] = $e instanceof HttpExceptionInterface ? $e->getMessage() : 'Internal Server Error';
        }

        return $ret;
    }
}
