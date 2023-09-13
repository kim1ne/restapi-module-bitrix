<?php

namespace Neti\RestApi\Route;

use Neti\RestApi\Route\Map\Http;
use Neti\RestApi\Route\Map\Matcher;
use Neti\RestApi\Route\Map\RouteValidator;

class RouteCollection
{
    private string $url;
    private array $middleware;
    public string|array $regexp = '(\w+)';
    public ?string $description = null;
    public ?string $name = null;

    public readonly string|array|\Closure $action;
    public readonly array $match;
    public readonly string $method;
    public readonly int $score;
    public readonly string $controllerName;
    public readonly string|\Closure $function;

    private static array $collection;
    private static ?self $current = null;

    public function __construct(
        string $httpMethod,
        string $url,
        array|string|\Closure $action
    )
    {
        $this->url = $url;
        $this->action = $action;
        $this->middleware = [];
        $this->method = strtoupper($httpMethod);
        self::$collection[$this->method][] = $this;
        $this->setTheScore();
        $this->action();
    }

    public function regexp(string|array $regexp): self
    {
        $this->regexp = $regexp;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAction(): array|\Closure|string
    {
        return $this->function;
    }

    public function getMiddlewares(): array
    {
        return $this->middleware;
    }

    public function action(): void
    {
        if ($this->action instanceof \Closure) return;

        if (is_array($this->action)) {
            $this->setControllerNameAndAction($this->action[0], $this->action[1] ?? '__invoke');
        } else {
            $this->setControllerNameAndAction($this->action);
        }
    }

    private function setControllerNameAndAction(string $controllerName, ?string $function = '__invoke'): void
    {
        $this->controllerName = $controllerName;
        $this->function = $function;
    }

    public function setTheScore(): void
    {
        $this->score = count(self::$collection[$this->method]);
    }

    public static function getCollection(): array
    {
        return self::$collection ?? [];
    }

    public static function withMethod(): array
    {
        return self::$collection[$_SERVER['REQUEST_METHOD']] ?? [];
    }

    public function middleware(string|array $middlewareName): self
    {
        if (is_string($middlewareName)) {
            $middlewareName = (array) $middlewareName;
        }

        $this->middleware = array_unique(array_merge($this->middleware, (array)$middlewareName));

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        $score = $this->score;
        $key = $score - 1;
        self::$collection[$this->method][$name] = self::$collection[$this->method][$key];
        unset(self::$collection[$this->method][$key]);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    public function group(array $data): self
    {
        foreach ($data as $group) {
            foreach ($group as $key => $value) {
                if (empty($key)) continue;
                switch ($key) {
                    case ('prefix'):

                        $this->url = $value . $this->url;
                        break;
                    case ('middleware'):
                        $this->middleware($value);
                        break;
                }
            }
        }

        return $this;
    }

    public static function getCurrent(): ?self
    {
        if (self::$current !== null) return self::$current;

        $currentRoute = null;

        $currentUri = Http::requestUri();

        foreach (RouteCollection::withMethod() as $route) {
            /**
             * @var RouteCollection $route
             */
//            $validateRoute = '';
//            if (is_string($route->regexp)) {
//                $validateRoute = RouteValidator::replaceRegexp($route->url, replacement: $route->regexp);
//            } elseif (is_array($route->regexp)) {
//                foreach ($route->regexp as $regex) {
//                    $url = (empty($validateRoute)) ? $route->url : $validateRoute;
//                    $validateRoute = RouteValidator::replaceRegexp($url, replacement: $regex, loop: false);
//                }
//            }

//            echo '<pre>'; print_r(RouteCollection::withMethod()); die;

            $validateRoute = $route->regexpValidate();

            $pregUrl = Matcher::regexp($validateRoute);

            preg_match($pregUrl, $currentUri, $matches);
            if (!empty($matches)) {
                unset($matches[0]);
                $route->match = array_values($matches);
                $currentRoute = $route;
                break;
            }
        }
        self::$current = $currentRoute;
        return self::$current;
    }

    private function regexpValidate(): string
    {
        $validateRoute = '';
        if (is_string($this->regexp)) {
            $validateRoute = RouteValidator::replaceRegexp($this->url, replacement: $this->regexp);
        } elseif (is_array($this->regexp)) {
            foreach ($this->regexp as $regex) {
                $url = (empty($validateRoute)) ? $this->url : $validateRoute;
                $validateRoute = RouteValidator::replaceRegexp($url, replacement: $regex, loop: false);
            }
        }
        return $validateRoute;
    }

    /**
     * @throws \Exception
     */
    public static function generateUrl(string $routeName, string|array $needles = null): string
    {
        $route = self::getByName($routeName);

        $url = $route->url;

        if ($needles !== null) {
            if (is_array($needles)) {
                foreach ($needles as $needle) {
                    $url = RouteValidator::replaceRegexp($url, '(', ')', $needle, false);
                }
            } else {
                $url = RouteValidator::replaceRegexp($url, '(', ')', $needles, false);
            }
        }

        return $url;
    }

    public static function getByName(string $routeName): self
    {
        $routes = self::$collection;
        $route = false;
        foreach ($routes as $httpRoutes) {
            if (array_key_exists($routeName, $httpRoutes)) {
                $route = $httpRoutes[$routeName];
                break;
            }
        }

        if ($route === false) throw new \Exception('Route name: ' . $routeName . ' is not find');

        return $route;
    }
}