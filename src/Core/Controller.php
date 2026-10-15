<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Render view with layout
     */
    protected function render(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($viewPath, $data, $layout);
    }

    /**
     * Send JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
