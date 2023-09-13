<?php

namespace Neti\RestApi\Exceptions;

class Excepter extends \Exception
{
    public function __construct($message = null, $code = 404)
    {
        $message = json_encode([
            'message' => $message,
            'status' => $code
        ]);
        parent::__construct(message: $message, code: $code);
    }
}