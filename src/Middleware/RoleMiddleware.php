<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Session;

class RoleMiddleware
{
    /**
     * Ensure current user has one of the allowed roles
     */
    public static function handle(array $allowedRoles): bool
    {
        if (!Auth::check()) {
            Session::setFlash('error', 'Authentication required.');
            header('Location: /login');
            exit;
        }

        $role = Auth::role();
        if (!in_array($role, $allowedRoles, true)) {
            Session::setFlash('error', 'Access denied. You do not have permission to access this area.');
            header('Location: /dashboard');
            exit;
        }

        return true;
    }
}
