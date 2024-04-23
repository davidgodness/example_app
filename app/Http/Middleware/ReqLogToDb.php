<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ReqLogToDb
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $tableName = 'req_logs_' . now()->toDateTime()->format('Y_m_d');

        $results = DB::select("show tables like '$tableName'");

        if (empty($results)) {
            DB::statement("CREATE TABLE `$tableName` (
	            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            `method` VARCHAR(8) NOT NULL,
                `path` VARCHAR(256) NOT NULL,
                `req_data` LONGTEXT NOT NULL,
                `response_content` LONGTEXT NOT NULL,
                `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
        }

        DB::table($tableName)->insert([
            'method' => $request->method(),
            'path' => $request->path(),
            'req_data' => $request->getContent(),
            'response_content' => $response->getContent(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $response;
    }
}
