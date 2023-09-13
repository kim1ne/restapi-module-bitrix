<?php

namespace Neti\RestApi\Bootstrap\Pipeline;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Bootstrap\Handler;
use Neti\RestApi\Bootstrap\Middleware\Middleware;
use Neti\RestApi\Bootstrap\Request;

class Pipeline
{
    private array $middlewares;
    private Handler $handler;

    public static ?self $pipe;

    public function __construct()
    {
        self::$pipe = $this;
    }

    /**
     * @param list<Middleware> $middlewares
     */
    public function setMiddleware(array $middlewares): void
    {
        $this->middlewares = $middlewares;
    }

    public function setHandler(Handler $handler): void
    {
        $this->handler = $handler;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function clear(): void
    {
        self::$pipe = null;
    }

    /**
     * @param Request $request
     */
    public function handle(HttpRequest $request): Json
    {
        /**
         * @var Middleware $middleware
         */
        $middleware = array_shift($this->middlewares);

        if ($middleware !== null) {
            return $middleware->handle($request, [$this, 'handle']);
        }

        $this->clear();

        return $this->handler->handle($request);
    }
}