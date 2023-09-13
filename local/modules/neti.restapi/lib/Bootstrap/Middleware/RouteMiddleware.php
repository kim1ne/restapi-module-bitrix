<?php

namespace Neti\RestApi\Bootstrap\Middleware;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Exceptions\Excepter;
use Neti\RestApi\Route\RouteCollection;

class RouteMiddleware implements Middleware
{

    /**
     * @throws Excepter
     */
    public function handle(HttpRequest $request, callable $next): Json
    {
        $route = RouteCollection::getCurrent();

        if ($route === null) throw new \Exception('Page not found', 404);

        return $next($request);
    }
}