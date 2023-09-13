<?php

namespace Neti\RestApi\Bootstrap;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Bootstrap\Pipeline\Pipeline;

class Application
{
    public static string $time;

    /**
     * @param Pipeline $pipeline
     */
    public function __construct(
        private readonly Pipeline $pipeline
    )
    {
        self::$time = microtime(true);
    }

    public function handle(HttpRequest $request): Json
    {
        try {
            return $this->pipeline->handle($request);
        } catch (\Exception $exception) {
            return (new ExceptionResponse($exception->getMessage(), $exception->getCode()))->getResponse();
        }
    }
}