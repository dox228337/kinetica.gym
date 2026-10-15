<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => rtrim($path, '/') ?: '/',
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Parse path without query string
        $parsedUrl = parse_url($requestUri, PHP_URL_PATH);
        $path = rtrim($parsedUrl, '/') ?: '/';

        if ($path === '/index.php') {
            $path = '/';
        }

        // Normalize subdirectory if running inside a subfolder or alias
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '\\' && !empty($scriptName)) {
            $scriptName = str_replace('\\', '/', $scriptName);
            if (strpos($path, $scriptName) === 0) {
                $path = substr($path, strlen($scriptName));
                $path = rtrim($path, '/') ?: '/';
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $path) {
                $this->executeHandler($route['handler']);
                return;
            }
        }

        // Fallback for root path or not found: default to /en
        if ($path === '/' || $path === '') {
            header('Location: /en', true, 302);
            exit;
        }

        http_response_code(404);
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>404 Not Found - Kinetica</title></head><body style='background:#0a0b0e;color:#fff;font-family:sans-serif;text-align:center;padding:100px 20px;'><h1>404 - Page Not Found</h1><p><a href='/en' style='color:#d4ff00;'>Return to Kinetica Homepage</a></p></body></html>";
    }

    private function executeHandler($handler): void
    {
        if (is_callable($handler)) {
            call_user_func($handler);
            return;
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controllerName, $method] = explode('@', $handler);
            $fullClass = 'App\\Controllers\\' . $controllerName;

            if (class_exists($fullClass)) {
                $controller = new $fullClass();
                if (method_exists($controller, $method)) {
                    $controller->$method();
                    return;
                }
            }
        }

        http_response_code(500);
        echo "Error: Handler could not be executed.";
    }
}
