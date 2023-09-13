<?php

namespace Neti\RestApi\Container;

final class Container
{
    public static ?self $instance = null;

    const SKALAR_VALUES = [
        'int', 'string', 'array', 'object', 'float', 'bool'
    ];

    private array $bindings = [];
    private array $cachedDependencies = [];

    public function __construct()
    {
        self::$instance = $this;
    }

    public static function getInstance(): self
    {
        $self = require_once $_SERVER['DOCUMENT_ROOT'] . '/local/modules/neti.restapi/config/container.php';

        if ($self !== true) {
            return $self;
        }

        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function bind(string $id, $binding): void
    {
        $this->bindings[$id] = $binding;
    }

    public function isScalar(string $type): bool
    {
        return in_array($type, self::SKALAR_VALUES);
    }

    /**
     * @throws \ReflectionException
     * @throws ServiceNotRegister
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id) && class_exists($id)) {
            return $this->make($id);
        }

        if (isset($this->cachedDependencies[$id])) {
            return $this->cachedDependencies[$id];
        }

        $binding = $this->bindings[$id];

        if ($binding instanceof \Closure) {
            return $binding($this);
        }

        if (is_string($binding) && class_exists($binding)) {
            return $this->make($binding);
        }

        return $binding;
    }

    /**
     * @throws \ReflectionException
     * @throws ServiceNotRegister
     */
    private function make(string $className)
    {
        $reflection = new \ReflectionClass($className);

        if ($reflection->isInterface()) {
            if (!$this->has($className)) throw new ServiceNotRegister($className);
        }

        $constructor = $reflection->getConstructor();

        if (empty($constructor)) {
            return new $className();
        }

        $parameters = $constructor->getParameters();

        if (empty($parameters)) {
            return new $className();
        }

        $constructor = [];

        foreach ($parameters as $param) {
            $nameType = $param->getType()->getName();

            if ($this->has($nameType)) {
                $param = $this->get($nameType);
            } else {
                $param = $this->make($nameType);
            }

            $constructor[] = $param;
        }

        $this->cachedDependencies[$className] = new $className(...$constructor);

        return $this->cachedDependencies[$className];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->cachedDependencies) || array_key_exists($id, $this->bindings);
    }
}