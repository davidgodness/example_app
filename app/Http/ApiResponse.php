<?php

namespace App\Http;

class ApiResponse
{

    public static function success($data = []): array
    {
        return [
            'code' => 0,
            'message' => 'ok',
            'data' => $data
        ];
    }

    public static function error($code, $message, $data = [], $debug = []): array
    {
        $ret = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($debug)) {
            $ret['debug'] = $debug;
        }

        return $ret;
    }
}
