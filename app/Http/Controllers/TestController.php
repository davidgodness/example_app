<?php

namespace App\Http\Controllers;

use App\Http\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TestController extends Controller
{
    public function printRequest(Request $request): array
    {
        return ApiResponse::success(['headers' => $request->headers->all(), 'body' => $request->getContent()]);
    }

    public function expectedError()
    {
        throw new BadRequestHttpException('invalid params');
    }

    /**
     * @throws Exception
     */
    public function unexpectedError()
    {
        throw new Exception();
    }

    public function checkStr(Request $request): array
    {
        $s = $request->query('s');

        if (strlen($s) == 0) {
            return ApiResponse::success(false);
        }

        if (strlen($s) < 1 || strlen($s) > 10000) {
            throw new BadRequestHttpException('1 <= s.length <= 10000');
        }

        if (preg_match('/[^(){}\[\]]/', $s)) {
            throw new BadRequestHttpException("'s' consists of parentheses only '()[]{}'");
        }

        $stack = [];
        $map = [
            ')' => '(',
            '}' => '{',
            ']' => '[',
        ];

        for ($i = 0; $i < strlen($s); $i++) {
            if (in_array($s[$i], array_keys($map))) {
                $pair = array_pop($stack);

                if (is_null($pair) || $pair !== $map[$s[$i]]) {
                    return ApiResponse::success(false);
                }
            }

            if (in_array($s[$i], array_values($map))) {
                $stack[] = $s[$i];
            }
        }

        if (empty($stack)) {
            return ApiResponse::success(true);
        } else {
            return ApiResponse::success(false);
        }
    }
}
