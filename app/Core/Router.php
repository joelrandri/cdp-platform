<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function post(string $uri, callable $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function get(string $uri, callable $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function dispatch(string $method, string $uri): void
    {
        if (isset($this->routes[$method][$uri])) {
            $this->routes[$method][$uri]();
        } else {
            http_response_code(404);

            header('Content-Type: application/json; charset=utf-8');

            echo json_encode([
                'success' => false,
                'message' => 'Route not found'
            ]);
        }
    }
}