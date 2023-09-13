<?php

namespace Neti\RestApi\Container;

class ServiceNotFoundException extends \Exception
{
    public function __construct(string $serviceId)
    {
        $message = 'Service ' . $serviceId . ' is not found';
        parent::__construct($message, 500);
    }
}