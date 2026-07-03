<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function __construct(private readonly Request $request)
    {
    }

    public function get(string $uri, array $action): void
    {
        $this->add('GET', $uri, $action);
    }

    public function post(string $uri, array $action): void
    {
        $this->add('POST', $uri, $action);
    }

    public function dispatch(): void
    {
        $method = $this->request->method();
        $path = $this->request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches) !== 1) {
                continue;
            }

            array_shift($matches);
            [$controller, $methodName] = $route['action'];
            $instance = new $controller($this->request);
            $instance->{$methodName}(...$matches);

            return;
        }

        http_response_code(404);
        echo View::render('errors/404', ['title' => 'Page not found'], Auth::check() ? 'app' : 'auth');
    }

    private function add(string $method, string $uri, array $action): void
    {
        $uri = '/' . trim($uri, '/');
        $uri = $uri === '//' ? '/' : $uri;
        $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $uri);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'pattern' => '#^' . $pattern . '$#',
            'action' => $action,
        ];
    }
}

