<?php

namespace Neti\RestApi\Route\Map;

use Neti\RestApi\Route\RouteCollection;

class Decorator
{
    private array $args = [];

    public function __call(string $method, array $action): RouteCollection
    {
        $routeCollection = new RouteCollection($method, ...$action);
        return $routeCollection->group($this->args);
    }

    public function group(array $args, \Closure $func): void
    {
        $this->setGroup($args);
        $func($this);

        $this->removeGroup();
    }

    private function setGroup(array $args): void
    {
        Group::set($args);
        $this->args = Group::get();
    }

    private function removeGroup(): void
    {
        $this->args = Group::remove();
    }
}