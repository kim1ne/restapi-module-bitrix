<?php

namespace Neti\RestApi\Container;

class ServiceNotRegister extends \Exception
{
    public function __construct(string $serviceId)
    {
        $message = 'Service ' . $serviceId . ' is not registred';
        parent::__construct($message, 500);
    }
}