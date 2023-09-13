<?php

namespace Neti\RestApi\Bootstrap;

class HelperMiddleware
{
    private string $error;

    public function getNamespaceForUserMiddlewareName()
    {
        $userMiddlewares = require_once $_SERVER['DOCUMENT_ROOT'] . '/local/modules/neti.restapi/config/middlewares.php';

    }
}