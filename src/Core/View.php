<?php

namespace App\Core;

class View
{
    /**
     * Render a view file with optional layout
     *
     * @param string $viewPath e.g. 'landing/index'
     * @param array $data Variables to pass to the view
     * @param string|null $layout e.g. 'layouts/main' or null for no layout
     */
    public static function render(string $viewPath, array $data = [], ?string $layout = 'layouts/main'): void
    {
        extract($data);

        $viewsBaseDir = __DIR__ . '/../../views/';
        $viewFile = $viewsBaseDir . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }

        // Capture view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = $viewsBaseDir . $layout . '.php';
            if (file_exists($layoutFile)) {
                ob_start();
                require $layoutFile;
                echo ob_get_clean();
                return;
            }
        }

        echo $content;
    }
}
