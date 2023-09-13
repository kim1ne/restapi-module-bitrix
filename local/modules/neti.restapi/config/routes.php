<?php

use Neti\RestApi\Route\Route;

$route = new Route();

$route->group(['prefix' => '/user'], function (Route $route) {

    $route->post('/registration', [\Neti\RestApi\Controllers\UserController::class, 'registration'])
        ->middleware('registration')
        ->description('Присылается login, email, password и confirmPassword, при успешной регистрации создаётся пользователь и выдаётся access-токен и refresh-токен');

    $route->post('/auth', [\Neti\RestApi\Controllers\UserController::class, 'auth'])
        ->name('user.auth')
        ->description('Присылается login и password, при успешной авторизации выдаётся access-токен и refresh-токен');

    $route->post('/refresh/token', [\Neti\RestApi\Controllers\UserController::class, 'refresh'])
        ->middleware(['auth', 'refresh'])
        ->description('Для выдачи нового access-токена присылается refresh-токен');

});

$route->get('/', \Neti\RestApi\Controllers\IndexController::class)
    ->name('index');

$route->get('/{user}/comments/{comment}', \Neti\RestApi\Controllers\IndexController::class)
    ->name('comment')
    ->regexp(['(\w+)', '(\d+)']);