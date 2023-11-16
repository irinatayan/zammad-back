<?php

declare(strict_types=1);

namespace App;

class Router
{
    private const GET = 'GET';
    private const POST = 'POST';
    private const OPTIONS = 'OPTIONS';
    private const PATCH = 'PATCH';

    private array $routes = [];

    /**
     * @param string $route
     * @param callable|string $callback
     */
    public function get(string $route, callable|string $callback): void
    {
        $this->routes[] = [
            'path' => $route,
            'callback' => $callback,
            'method' => self::GET,
        ];
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param string $route
     * @param callable|string $callback
     */
    public function post(string $route, callable|string $callback): void
    {
        $this->routes[] = [
            'path' => $route,
            'callback' => $callback,
            'method' => self::POST,
        ];
    }

    /**
     * @param string $route
     * @param callable|string $callback
     */
    public function patch(string $route, callable|string $callback): void
    {
        $this->routes[] = [
            'path' => $route,
            'callback' => $callback,
            'method' => self::PATCH,
        ];
    }

    /**
     * @param string $route
     * @param callable|string $callback
     */
    public function options(string $route, callable|string $callback): void
    {
        $this->routes[] = [
            'path' => $route,
            'callback' => $callback,
            'method' => self::OPTIONS,
        ];
    }

    public function run(array $server): void
    {
        $match = null;

        $requestUri = parse_url($server['REQUEST_URI']);
        $requestPath = $requestUri['path'];
        $method = $server['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['path'] === $requestPath && $route['method'] === $method) {
                $match = $route['callback'];
                break;
            }
        }

        $requestArray = array_merge($_GET, $_POST);
        $requestArray['cookie'] = $_COOKIE;

        $request = new Request($requestArray);
        $response = new Response();

        if (!$match) {
            $match = function (Request $request, Response $response) {
                $response->setBody('404 Not found!');
                (new Response())->error(message: "404 Not found!", statusCode: 404)->send();
            };
        }

        if (is_string($match) && class_exists($match)) {
            $match = new $match();
        }
        call_user_func_array($match, [$request, $response]);

    }
}