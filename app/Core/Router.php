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

    public function getDynamic(string $pattern, callable $action): void
    {
        $this->routes['GET'][$pattern] = $action;
    }

    public function dispatch(string $method, string $uri): void
    {
        $action = $this->routes[$method][$uri] ?? null;

        if ($action !== null) {
            $action();
            return;
        }

        foreach ($this->routes[$method] ?? [] as $pattern => $callback) {
            $regex = preg_replace(
                '#\{id\}#',
                '([0-9]+)',
                $pattern
            );

            if (preg_match('#^' . $regex . '$#', $uri, $matches)) {
                $callback((int) $matches[1]);
                return;
            }
        }

        http_response_code(404);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'message' => 'Route not found'
        ]);
    }
}