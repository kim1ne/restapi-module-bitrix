<?php

use Neti\RestApi\Route\Route;

$route = new Route();

$route->get('/user/{user}', \Neti\RestApi\Controllers\IndexController::class)
    ->middleware('user')
    ->regexp('(\d+)');