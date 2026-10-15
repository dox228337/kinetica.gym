<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Session;

class Booking extends Model
{
    /**
     * Get list of booked classes for a specific athlete
     */
    public static function forUser(int $userId): array
    {
        $all = Session::get('user_bookings') ?? [];
        return $all[$userId] ?? [];
    }

    /**
     * Create class reservation
     */
    public static function create(int $userId, string $classTitle): bool
    {
        $bookings = Session::get('user_bookings') ?? [];
        if (!isset($bookings[$userId])) {
            $bookings[$userId] = [];
        }
        if (!in_array($classTitle, $bookings[$userId], true)) {
            $bookings[$userId][] = $classTitle;
            Session::set('user_bookings', $bookings);
            return true;
        }
        return false;
    }
}
