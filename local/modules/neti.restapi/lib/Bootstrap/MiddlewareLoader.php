<?php

namespace Neti\RestApi\Bootstrap;

class MiddlewareLoader
{
    public static function load()
    {
        $middlewares = require_once $_SERVER['DOCUMENT_ROOT'] . '/local/modules/neti.restapi/config/middlewares.php';

        $userMiddlewareFile = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/neti.restapi/middlewares.php';

        if (file_exists($userMiddlewareFile)) {
            $userMiddlewares = require_once $userMiddlewareFile;
            $middlewares = array_merge($middlewares, $userMiddlewares);
        }

        return $middlewares;
    }
}