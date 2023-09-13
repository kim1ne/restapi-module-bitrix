<?php

return [
    'auth' => \Neti\RestApi\Middlewares\AuthMiddleware::class,
    'registration' => \Neti\RestApi\Middlewares\RegistrationMiddleware::class,
    'refresh' => \Neti\RestApi\Middlewares\RefreshMiddleware::class,
];
