<?php

namespace Neti\RestApi\Route\Map;

class Matcher
{
    public static function regexp(string $string): string
    {
        return '~^' . $string . '$~';
    }
}