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
        throw new BadRequestHttpException('invalid params', null, 1);
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

        if (is_null($s)) {
            return ApiResponse::error(1, "no query string 's'");
        }

        if (strlen($s) == 0) {
            throw new BadRequestHttpException("'s' has no value", null, 2);
        }

        if (strlen($s) < 1 || strlen($s) > 10000) {
            throw new BadRequestHttpException('1 <= s.length <= 10000', null, 3);
        }

        if (preg_match('/[^(){}\[\]]/', $s)) {
            throw new BadRequestHttpException("'s' consists of parentheses only '()[]{}'",
                null, 4);
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
                    return ApiResponse::error(5, 'not valid', false);
                }
            }

            if (in_array($s[$i], array_values($map))) {
                $stack[] = $s[$i];
            }
        }

        if (empty($stack)) {
            return ApiResponse::success(true);
        } else {
            return ApiResponse::error(5, 'not valid', false);
        }
    }
}
