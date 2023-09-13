<?php

namespace Neti\RestApi\Route;

class RouteLoader
{
    public static function load(): void
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/local/modules/neti.restapi/config/routes.php';

        $userRoutesFile = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/neti.restapi/routes.php';
        if (file_exists($userRoutesFile)) {
            require_once $userRoutesFile;
        }
    }
}