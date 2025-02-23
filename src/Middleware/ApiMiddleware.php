<?php

namespace App\Middleware;

use App\Libraries\BaseMiddleware;

class ApiMiddleware extends BaseMiddleware
{
    public function handle()
    {
        // Check if it is a cross-origin preflight request
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }
}
