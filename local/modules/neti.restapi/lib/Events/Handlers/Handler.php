<?php

namespace Neti\RestApi\Events\Handlers;

use Neti\RestApi\Tools\UrlWriter;

class Handler
{
    public static function ChangePathInterface($fields)
    {
        if ($fields[0] != '/') {
            $fields = '/' . $fields;
        }

        if (!str_ends_with($fields, '/')) {
            $fields = $fields . '/';
        }

        $writer = new UrlWriter();
        $writer->write($fields);
    }
}