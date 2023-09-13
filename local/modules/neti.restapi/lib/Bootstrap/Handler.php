<?php

namespace Neti\RestApi\Bootstrap;

use Bitrix\Main\HttpRequest;

interface Handler
{
    public function handle(HttpRequest $request);
}