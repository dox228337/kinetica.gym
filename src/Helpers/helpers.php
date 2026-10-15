<?php

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Generate a hidden CSRF token input field for forms
 */
if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = \App\Core\Session::csrfToken();
        return '<input type="hidden" name="_csrf_token" value="' . e($token) . '">';
    }
}

/**
 * Get the current CSRF token value
 */
if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \App\Core\Session::csrfToken();
    }
}
