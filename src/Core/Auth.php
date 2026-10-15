<?php

namespace App\Core;

class Auth
{
    private const SESSION_USER_KEY = 'kinetica_auth_user';

    /**
     * Check if user is logged in
     */
    public static function check(): bool
    {
        return Session::has(self::SESSION_USER_KEY);
    }

    /**
     * Get authenticated user array
     */
    public static function user(): ?array
    {
        return Session::get(self::SESSION_USER_KEY);
    }

    /**
     * Get user ID
     */
    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int)($user['id'] ?? 0) : null;
    }

    /**
     * Get user role ('lead', 'client', 'employee', 'admin')
     */
    public static function role(): string
    {
        $user = self::user();
        return $user['role'] ?? 'guest';
    }

    /**
     * Check if user is gym staff / trainer
     */
    public static function isEmployee(): bool
    {
        $role = self::role();
        return in_array($role, ['employee', 'trainer', 'admin'], true);
    }

    /**
     * Check if user is a client or lead
     */
    public static function isClient(): bool
    {
        $role = self::role();
        return in_array($role, ['client', 'lead'], true);
    }

    /**
     * Log in a user by storing their data in session
     */
    public static function login(array $user): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        Session::set(self::SESSION_USER_KEY, [
            'id'                  => (int)($user['id'] ?? 1),
            'full_name'           => $user['full_name'] ?? 'Athlete',
            'email'               => $user['email'] ?? '',
            'phone'               => $user['phone'] ?? '',
            'role'                => $user['role'] ?? 'client',
            'lead_status'         => $user['lead_status'] ?? 'converted',
            'fitness_goal'        => $user['fitness_goal'] ?? 'General Fitness',
            'preferred_language'  => $user['preferred_language'] ?? 'en',
            'free_courses_unlocked' => (bool)($user['free_courses_unlocked'] ?? true),
            'nfc_token'           => 'NFC-' . strtoupper(substr(md5($user['email'] ?? 'pass'), 0, 8)),
        ]);
    }

    /**
     * Log out current user
     */
    public static function logout(): void
    {
        Session::remove(self::SESSION_USER_KEY);
        Session::destroy();
    }
}
