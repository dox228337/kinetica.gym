<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

class AuthMiddleware
{
    /**
     * Ensure current user is authenticated
     */
    public static function handle(): bool
    {
        if (!Auth::check()) {
            Session::setFlash('error', 'Please log in to access this page.');
            header('Location: /login');
            exit;
        }
        return true;
    }
}
