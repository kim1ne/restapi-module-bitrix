<?php

namespace Neti\RestApi\Middlewares;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Bootstrap\Middleware\Middleware;
use Neti\RestApi\Container\Container;
use Neti\RestApi\Exceptions\Excepter;
use Neti\RestApi\Services\JWTService;

class UserMiddleware implements Middleware
{

    public function handle(HttpRequest $request, callable $next): Json
    {
        return $next($request);
    }
}