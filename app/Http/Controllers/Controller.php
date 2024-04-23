<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function success($data = []): array
    {
        return ['error_code' => 0, 'error_message' => 'success', 'data' => $data];
    }

    public function failure(int $error_code = 0, string $error_message = 'error', $data = []): array
    {
        return ['error_code' => $error_code, 'error_message' => $error_message, 'data' => $data];
    }
}
