<?php

namespace Neti\RestApi\Bootstrap\Middleware;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;

interface Middleware
{
    public function handle(HttpRequest $request, callable $next): Json;
}