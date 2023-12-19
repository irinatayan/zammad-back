<?php
declare(strict_types=1);

namespace Framework;

class App
{
    private Router $router;
    private Container $container;

    public function __construct(string $containerDefinitionsPath = null)
    {
        $this->router = new Router();
        $this->container = new Container();

        if ($containerDefinitionsPath) {
            $containerDefinitions = include $containerDefinitionsPath;
            $this->container->addDefinitions($containerDefinitions);
        }
    }

    public function run(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $this->router->dispatch($path, $method, $this->container);

    }

    public function get(string $path, array $controller): App
    {
        $this->router->add('GET', $path, $controller);
        return $this;
    }

    public function post(string $path, array $controller): App
    {
        $this->router->add('POST', $path, $controller);

        return $this;
    }

    public function delete(string $path, array $controller): App
    {
        $this->router->add('DELETE', $path, $controller);

        return $this;
    }

    public function addMiddleware(string $middleware): void
    {
        $this->router->addMiddleware($middleware);
    }

    public function add(string $middleware): App
    {
        $this->router->addRouteMiddleware($middleware);
        return $this;
    }

    public function setErrorHandler(array $controller): void
    {
        $this->router->setErrorHandler($controller);
    }

    public function setOptionsHandler(array $controller): void
    {
        $this->router->setOptionsHandler($controller);
    }
}