<?php

namespace Neti\RestApi\Middlewares;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Bootstrap\Middleware\Middleware;
use Neti\RestApi\Container\Container;
use Neti\RestApi\Exceptions\Excepter;
use Neti\RestApi\Services\JWTService;

class RefreshMiddleware implements Middleware
{
    const ARRAY_KEY_HEADER_AUTH = 'HTTP_X_AUTH_REFRESH_TOKEN';

    public function __construct(
        private readonly JWTService $jwtService
    ){}

    public function handle(HttpRequest $request, callable $next): Json
    {
        $token = $request->getServer()->getValues()[self::ARRAY_KEY_HEADER_AUTH] ?? '';

        $decode = $this->jwtService->checkRefreshToken($token);

        if ($decode === false) {
            $this->errorNotAuth();
        }

        return $next($request);
    }

    private function errorNotAuth()
    {
        throw new \Exception('refresh token is not valid', 401);
    }
}