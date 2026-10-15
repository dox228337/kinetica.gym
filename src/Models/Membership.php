<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use Exception;

class Membership extends Model
{
    /**
     * Get all membership plans ordered by display priority
     */
    public static function allPlans(): array
    {
        try {
            $stmt = static::db()->query("SELECT * FROM membership_plans ORDER BY sort_order ASC");
            if ($stmt) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            // Fallback
        }
        return [];
    }

    /**
     * Find a membership plan by its unique code
     */
    public static function findByCode(string $code): ?array
    {
        try {
            $stmt = static::db()->prepare("SELECT * FROM membership_plans WHERE code = :code LIMIT 1");
            $stmt->execute(['code' => $code]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            return null;
        }
    }
}
