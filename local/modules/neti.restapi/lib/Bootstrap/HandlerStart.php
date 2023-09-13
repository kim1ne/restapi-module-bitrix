<?php

namespace Neti\RestApi\Bootstrap;

use Bitrix\Main\Engine\Response\Json;
use Bitrix\Main\HttpRequest;
use Neti\RestApi\Container\Container;
use Neti\RestApi\Container\ServiceNotRegister;
use Neti\RestApi\Exceptions\Excepter;
use Neti\RestApi\Route\RouteCollection;
use Neti\RestApi\Services\Helper;

class HandlerStart implements Handler
{
    private Container $container;
    /**
     * @throws \ReflectionException
     * @throws Excepter
     */
    public function handle(HttpRequest $request): Json
    {
        $this->container = Container::getInstance();
        $this->container->bind(HttpRequest::class, $request);

        $route = RouteCollection::getCurrent();
        $match = $route->match;

        if ($route->action instanceof \Closure) {
            $func = $route->action;
            $response = $func(...$this->exploreFunction($func), ...$match);
        } else {
            $controller = $this->container->get($route->controllerName);
            $paramsAction = $this->exploreAction($controller, $route->function);
            $function = $route->function;
            $response = $controller->$function(...$paramsAction, ...$match);
        }

        if (empty($response)) {
            throw new \Exception(
                $route->controllerName . '->' . $route->function  . ' response is empty', 500
            );
        }

        if ($response instanceof Json) {
            $response = $this->json(json_decode($response->getContent(), true));
        } elseif (is_array($response)) {
            $response = $this->json($response);
        } else {
            throw new \Exception('Type does not match format', 500);
        }

        return $response;
    }

    private function json(array $data): Json
    {
        $data = [
            'status' => 200,
            'data' => Helper::arrayToCamelCase($data)
        ];

        return new Json($data);
    }

    private function error(string|array $data, $statusCode)
    {
        if (is_array($data)) {
            $data = Helper::arrayToCamelCase($data);
        }

        return (new ExceptionResponse($data, $statusCode))->getResponse();
    }

    /**
     * @throws \ReflectionException
     * @throws ServiceNotRegister
     */
    private function exploreAction(object $class, string $action): array
    {
        $reflection = new \ReflectionMethod($class, $action);

        return $this->getService($reflection->getParameters());
    }

    private function exploreFunction(\Closure $closure)
    {
        $reflection = new \ReflectionFunction($closure);
        return $this->getService($reflection->getParameters());
    }

    private function getService(array $params): array
    {
        $dependencies = [];
        foreach ($params as $param) {
            $type = $param->getType();

            if (empty($type)) continue;

            $id = $type->getName();

            if ($this->container->isScalar($id)) continue;

            $dependencies[] = $this->container->get($id);
        }
        return $dependencies;
    }
}
