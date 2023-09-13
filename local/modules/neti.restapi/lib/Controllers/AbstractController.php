<?php

namespace Neti\RestApi\Controllers;

abstract class AbstractController
{
    public function __construct(
        protected \Bitrix\Main\HttpRequest $request
    ){}
}
