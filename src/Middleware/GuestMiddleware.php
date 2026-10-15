<?php

namespace App\Middleware;

use App\Core\Auth;

class GuestMiddleware
{
    /**
     * Ensure current user is a guest (not logged in)
     */
    public static function handle(): bool
    {
        if (Auth::check()) {
            $redirect = Auth::isEmployee() ? '/crm' : '/dashboard';
            header('Location: ' . $redirect);
            exit;
        }
        return true;
    }
}
