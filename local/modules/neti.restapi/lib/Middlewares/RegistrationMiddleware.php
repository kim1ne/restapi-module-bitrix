<?php

namespace Neti\RestApi\Middlewares;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Bootstrap\Middleware\Middleware;
use Neti\RestApi\Exceptions\Excepter;

class RegistrationMiddleware implements Middleware
{
    public $error;

    public function handle(HttpRequest $request, callable $next): Json
    {
        if ((int)\Bitrix\Main\Engine\CurrentUser::get()->getId()) {
            throw new Excepter('User is authorize', 401);
        }

        return $next($request);
    }

    private function validate(HttpRequest $request)
    {
        if ($request->getJsonList()->get('login') === null) {
            throw new Excepter('`login` is require parameter', 400);
        }

        if ($request->getJsonList()->get('email') === null) {
            throw new Excepter('`email` is require parameter', 400);
        }

        if ($request->getJsonList()->get('password') === null) {
            throw new Excepter('`password` is require parameter', 400);
        }

        if ($request->getJsonList()->get('confirmPassword') === null) {
            throw new Excepter('`confirmPassword` is require parameter', 400);
        }

        if (!is_string($request->getJsonList()->get('login'))) {
            throw new Excepter('Parameter `login` must be is string', 400);
        }

        if (!is_string($request->getJsonList()->get('email'))) {
            throw new Excepter('Parameter `email` must be is string', 400);
        }

        if (!is_string($request->getJsonList()->get('password'))) {
            throw new Excepter('Parameter `password` must be is string', 400);
        }

        if (!is_string($request->getJsonList()->get('confirmPassword'))) {
            throw new Excepter('Parameter `confirmPassword` must be is string', 400);
        }

    }


}