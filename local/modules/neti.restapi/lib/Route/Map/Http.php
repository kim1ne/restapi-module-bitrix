<?php

namespace Neti\RestApi\Route\Map;

class Http
{
    public static function requestUri(): string
    {
        return '/' . explode('?', $_REQUEST['URI'])[0];
    }
}