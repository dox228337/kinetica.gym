<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use Exception;

class Trainer extends Model
{
    /**
     * Get active club coaches and physiotherapists roster
     */
    public static function all(): array
    {
        try {
            $stmt = static::db()->query("
                SELECT e.*, p.title AS position_title
                FROM employees e
                LEFT JOIN positions p ON p.id = e.position_id
                WHERE e.status = 'active'
                ORDER BY e.id ASC
            ");
            if ($stmt) {
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($res)) {
                    return $res;
                }
            }
        } catch (Exception $e) {
            // Fallback
        }

        return [
            ['id' => 1, 'first_name' => 'Marek', 'last_name' => 'Kowalski', 'position_title' => 'Lead Biomechanics Coach', 'email' => 'trainer@kinetica.gym'],
            ['id' => 2, 'first_name' => 'Dr. Elena', 'last_name' => 'Rostova', 'position_title' => 'Heavy Iron & Biomechanics Lead', 'email' => 'elena@kinetica.gym'],
            ['id' => 3, 'first_name' => 'Kasia', 'last_name' => 'Dąbrowska', 'position_title' => 'Head of Physiotherapy & Mobility', 'email' => 'kasia@kinetica.gym'],
            ['id' => 4, 'first_name' => 'Filip', 'last_name' => 'Nowak', 'position_title' => 'Hyrox Endurance & Performance Coach', 'email' => 'filip@kinetica.gym']
        ];
    }
}
