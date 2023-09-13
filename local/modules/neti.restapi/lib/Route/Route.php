<?php

namespace Neti\RestApi\Route;

use Neti\RestApi\Route\Map\Decorator;

/**
 * @method RouteCollection get(string $url, array|string|\Closure $arguments)
 * @method RouteCollection put(string $url, array|string|\Closure $arguments)
 * @method RouteCollection post(string $url, array|string|\Closure $arguments)
 * @method RouteCollection head(string $url, array|string|\Closure $arguments)
 * @method RouteCollection delete(string $url, array|string|\Closure $arguments)
 * @method RouteCollection patch(string $url, array|string|\Closure $arguments)
 * @method RouteCollection options(string $url, array|string|\Closure $arguments)
 * @method group(array $args, \Closure $arguments)
 */

class Route extends Decorator
{
}