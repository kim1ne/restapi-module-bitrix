<?php

namespace Neti\RestApi\Bootstrap\Middleware;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Bootstrap\MiddlewareLoader;
use Neti\RestApi\Bootstrap\Pipeline\Pipeline;
use Neti\RestApi\Container\Container;
use Neti\RestApi\Exceptions\Excepter;
use Neti\RestApi\Route\RouteCollection;

class PipelineRouteMiddleware implements Middleware
{

    /**
     * @throws Excepter
     */
    public function handle(HttpRequest $request, callable $next): Json
    {
        $route = RouteCollection::getCurrent();

        if (empty($pipeMiddlewares = $route->getMiddlewares())) {
            return $next($request);
        }

        $userMiddlewares = MiddlewareLoader::load();

        $middlewares = [];

        $container = Container::getInstance();

        foreach ($pipeMiddlewares as $middleware) {
            if (!array_key_exists($middleware, $userMiddlewares)) {
                throw new Excepter('Middleware "' . $middleware .'" is not exists', 500);
            }
            if (!class_exists($userMiddlewares[$middleware])) {
                throw new Excepter('Middleware "' . $middleware . '" - "' . $userMiddlewares[$middleware] .'" is not exists', 500);
            }

            $middleware = $userMiddlewares[$middleware];

            $middlewares[] = $container->get($middleware);
        }

        $pipe = Pipeline::$pipe;

        $pipe->setMiddleware($middlewares);

        return $next($request);
    }
}