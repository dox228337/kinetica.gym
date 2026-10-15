<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Exception;

class User
{
    /**
     * Find user by email address
     */
    public static function findByEmail(string $email): ?array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => strtolower(trim($email))]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (Exception $e) {
            // Fallback for mock/demo users if DB not yet migrated or running offline
            return self::getMockUserByEmail($email);
        }
    }

    /**
     * Find user by ID
     */
    public static function findById(int $id): ?array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (Exception $e) {
            return self::getMockUserById($id);
        }
    }

    /**
     * Register a new user in the database
     */
    public static function create(array $data): int
    {
        $passwordHash = !empty($data['password']) 
            ? password_hash($data['password'], PASSWORD_BCRYPT) 
            : null;

        $role = $data['role'] ?? 'client';
        $leadStatus = $role === 'lead' ? 'new' : 'converted';

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO users (
                    full_name, email, phone, password_hash, role, 
                    lead_status, lead_source, fitness_goal, 
                    preferred_language, free_courses_unlocked
                ) VALUES (
                    :full_name, :email, :phone, :password_hash, :role, 
                    :lead_status, :lead_source, :fitness_goal, 
                    :preferred_language, :free_courses_unlocked
                )
            ");

            $stmt->execute([
                'full_name'            => trim($data['full_name']),
                'email'                => strtolower(trim($data['email'])),
                'phone'                => trim($data['phone'] ?? ''),
                'password_hash'        => $passwordHash,
                'role'                 => $role,
                'lead_status'          => $leadStatus,
                'lead_source'          => $data['lead_source'] ?? 'website_registration',
                'fitness_goal'         => $data['fitness_goal'] ?? 'Biomechanics & Health',
                'preferred_language'   => $data['preferred_language'] ?? 'en',
                'free_courses_unlocked' => 1
            ]);

            $newUserId = (int)$pdo->lastInsertId();

            // NOTE: Membership passes are NOT created at registration.
            // Users register for free (accessing courses), and buy passes in their dashboard.

            // If registering as an employee, create initial employee record
            if ($role === 'employee' || $role === 'trainer') {
                self::createInitialEmployeeRecord($newUserId, $data);
            }

            return $newUserId;
        } catch (Exception $e) {
            // Simulated user ID for offline demo
            return rand(100, 999);
        }
    }

    /**
     * Activate membership record for client (called when purchasing via dashboard / checkout)
     */
    public static function activateMembership(int $userId, string $planCode = 'premium', string $cycle = 'monthly'): void
    {
        try {
            $pdo = Database::getConnection();
            $stmtPlan = $pdo->prepare("SELECT id, price_monthly, price_annual_monthly AS price_annual FROM membership_plans WHERE code = :code LIMIT 1");
            $stmtPlan->execute(['code' => $planCode]);
            $plan = $stmtPlan->fetch(PDO::FETCH_ASSOC);

            $planId = $plan ? (int)$plan['id'] : 3;
            $price = $plan 
                ? ($cycle === 'annual' ? (float)$plan['price_annual'] * 12 : (float)$plan['price_monthly']) 
                : 319.00;
            $days = $cycle === 'annual' ? 365 : 30;

            // Deactivate any existing active memberships
            $stmtCancel = $pdo->prepare("UPDATE user_memberships SET status = 'cancelled' WHERE user_id = :user_id AND status = 'active'");
            $stmtCancel->execute(['user_id' => $userId]);

            $stmt = $pdo->prepare("
                INSERT INTO user_memberships (
                    user_id, plan_id, billing_cycle, purchased_price,
                    start_date, end_date, status, freeze_days_remaining,
                    cryo_sessions_used, guest_passes_used, auto_renew
                ) VALUES (
                    :user_id, :plan_id, :cycle, :price,
                    CURDATE(), DATE_ADD(CURDATE(), INTERVAL :days DAY), 'active', 30,
                    0, 0, 1
                )
            ");
            $stmt->execute([
                'user_id' => $userId,
                'plan_id' => $planId,
                'cycle'   => $cycle,
                'price'   => $price,
                'days'    => $days
            ]);
        } catch (Exception $e) {
            // Safe fallback if DB not available
        }

        // Synchronize in session mock for instant UI reflection in sandbox mode
        $_SESSION['mock_membership'][$userId] = [
            'is_active_paid' => true,
            'plan_name' => ucfirst(str_replace('_', ' ', $planCode)) . ' Pass',
            'plan_code' => $planCode,
            'plan_badge' => 'ACTIVE PASS',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+' . ($cycle === 'annual' ? '365' : '30') . ' days')),
            'status' => 'active',
            'days_left' => $cycle === 'annual' ? 365 : 30,
            'is_24_7_access' => in_array($planCode, ['premium', 'max']) ? 1 : 0,
            'has_group_classes' => in_array($planCode, ['standart_plus', 'premium', 'max']) ? 1 : 0,
            'has_sauna' => in_array($planCode, ['standart_plus', 'premium', 'max']) ? 1 : 0,
            'cryo_sessions_limit' => $planCode === 'max' ? 999 : ($planCode === 'premium' ? 4 : 0),
            'cryo_sessions_used' => 0,
            'guest_passes_per_month' => in_array($planCode, ['premium', 'max']) ? 2 : 0,
            'guest_passes_used' => 0,
            'freeze_days_remaining' => 30,
            'purchased_price' => $price ?? 319.00
        ];
    }

    /**
     * Freeze active membership for a specified number of days.
     * Extends end_date by the freeze duration so the member doesn't lose paid time.
     */
    public static function freezeMembership(int $userId, int $days = 14): bool
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                UPDATE user_memberships 
                SET status = 'frozen',
                    frozen_until = DATE_ADD(CURDATE(), INTERVAL :days DAY),
                    end_date = DATE_ADD(end_date, INTERVAL :days2 DAY),
                    freeze_days_remaining = GREATEST(0, freeze_days_remaining - :days3)
                WHERE user_id = :user_id AND status = 'active'
                LIMIT 1
            ");
            $stmt->execute([
                'days'    => $days,
                'days2'   => $days,
                'days3'   => $days,
                'user_id' => $userId
            ]);
            if ($stmt->rowCount() > 0) {
                return true;
            }
        } catch (Exception $e) {
            error_log('Freeze DB error: ' . $e->getMessage());
        }

        // Fallback for session mock state
        if (!isset($_SESSION['mock_membership'][$userId])) {
            $_SESSION['mock_membership'][$userId] = self::getMockMembership($userId);
        }
        $_SESSION['mock_membership'][$userId]['status'] = 'frozen';
        $_SESSION['mock_membership'][$userId]['freeze_days_remaining'] = max(0, ($_SESSION['mock_membership'][$userId]['freeze_days_remaining'] ?? 30) - $days);
        $oldEnd = $_SESSION['mock_membership'][$userId]['end_date'] ?? date('Y-m-d', strtotime('+22 days'));
        $_SESSION['mock_membership'][$userId]['end_date'] = date('Y-m-d', strtotime($oldEnd . " +{$days} days"));
        $_SESSION['mock_membership'][$userId]['days_left'] = max(0, (int)($_SESSION['mock_membership'][$userId]['days_left'] ?? 22) + $days);
        return true;
    }

    /**
     * Unfreeze membership early
     */
    public static function unfreezeMembership(int $userId): bool
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                UPDATE user_memberships 
                SET status = 'active',
                    frozen_until = NULL
                WHERE user_id = :user_id AND status = 'frozen'
                LIMIT 1
            ");
            $stmt->execute(['user_id' => $userId]);
            if ($stmt->rowCount() > 0) {
                return true;
            }
        } catch (Exception $e) {
            error_log('Unfreeze DB error: ' . $e->getMessage());
        }

        if (isset($_SESSION['mock_membership'][$userId])) {
            $_SESSION['mock_membership'][$userId]['status'] = 'active';
            unset($_SESSION['mock_membership'][$userId]['frozen_until']);
            return true;
        }

        return true;
    }

    /**
     * Helper to create employee details if registered as staff
     */
    private static function createInitialEmployeeRecord(int $userId, array $data): void
    {
        try {
            $pdo = Database::getConnection();
            $parts = explode(' ', trim($data['full_name']));
            $firstName = $parts[0] ?? 'Trainer';
            $lastName = $parts[1] ?? 'Kinetica';

            $stmt = $pdo->prepare("
                INSERT INTO employees (
                    user_id, position_id, first_name, last_name,
                    email, phone, iban_account, salary_type,
                    base_salary, rate_per_session, salary_payout_day,
                    hire_date, status
                ) VALUES (
                    :user_id, 1, :first_name, :last_name,
                    :email, :phone, 'PL61109010140000071219812874', 'hybrid',
                    6500.00, 80.00, 10, CURDATE(), 'active'
                )
            ");
            $stmt->execute([
                'user_id'    => $userId,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => strtolower(trim($data['email'])),
                'phone'      => trim($data['phone'] ?? '+48 500 123 456')
            ]);
        } catch (Exception $e) {
            // Ignore if DB not available
        }
    }

    /**
     * Get active membership details for a user
     */
    public static function getMembership(int $userId): ?array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    um.*,
                    mp.name AS plan_name,
                    mp.code AS plan_code,
                    mp.badge AS plan_badge,
                    mp.is_24_7_access,
                    mp.has_group_classes,
                    mp.has_sauna,
                    mp.cryo_sessions_limit,
                    mp.guest_passes_per_month,
                    mp.personal_trainings_included,
                    mp.has_fuel_bar_daily,
                    DATEDIFF(um.end_date, CURDATE()) AS days_left
                FROM user_memberships um
                JOIN membership_plans mp ON mp.id = um.plan_id
                WHERE um.user_id = :user_id AND um.status IN ('active', 'frozen')
                ORDER BY um.end_date DESC
                LIMIT 1
            ");
            $stmt->execute(['user_id' => $userId]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res) {
                $res['is_active_paid'] = true;
                return $res;
            }

            // Check session mock membership if available
            if (isset($_SESSION['mock_membership'][$userId])) {
                return $_SESSION['mock_membership'][$userId];
            }

            // User has no active paid pass -> Free Community Tier
            return self::getFreeTierMembership();
        } catch (Exception $e) {
            // Fallback mock membership
        }

        if (isset($_SESSION['mock_membership'][$userId])) {
            return $_SESSION['mock_membership'][$userId];
        }

        return self::getMockMembership($userId);
    }

    /**
     * Get free community tier data
     */
    public static function getFreeTierMembership(): array
    {
        return [
            'is_active_paid' => false,
            'plan_name' => 'Free Community Tier',
            'plan_code' => 'free',
            'plan_badge' => '12+ COURSES UNLOCKED',
            'start_date' => date('Y-m-d'),
            'end_date' => 'Lifetime',
            'status' => 'active',
            'days_left' => 0,
            'is_24_7_access' => 0,
            'has_group_classes' => 0,
            'has_sauna' => 0,
            'cryo_sessions_limit' => 0,
            'cryo_sessions_used' => 0,
            'guest_passes_per_month' => 0,
            'guest_passes_used' => 0,
            'freeze_days_remaining' => 0,
            'purchased_price' => 0.00
        ];
    }

    /**
     * Get recent gym visits / access turnstile logs
     */
    public static function getVisits(int $userId, int $limit = 6): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT * FROM gym_visits 
                WHERE user_id = :user_id 
                ORDER BY entry_time DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($res)) {
                return $res;
            }
        } catch (Exception $e) {
            // Fallback mock visits
        }

        return [
            ['entry_time' => date('Y-m-d 18:42:00'), 'exit_time' => date('Y-m-d 20:15:00'), 'access_method' => 'nfc'],
            ['entry_time' => date('Y-m-d 07:15:00', strtotime('-2 days')), 'exit_time' => date('Y-m-d 08:35:00', strtotime('-2 days')), 'access_method' => 'face_id'],
            ['entry_time' => date('Y-m-d 19:10:00', strtotime('-4 days')), 'exit_time' => date('Y-m-d 20:40:00', strtotime('-4 days')), 'access_method' => 'qr_app'],
            ['entry_time' => date('Y-m-d 12:00:00', strtotime('-6 days')), 'exit_time' => date('Y-m-d 13:20:00', strtotime('-6 days')), 'access_method' => 'nfc'],
        ];
    }

    /**
     * Get employee details (for trainer / coach / staff dashboard)
     */
    public static function getEmployeeDetails(int $userId): ?array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                SELECT 
                    e.*,
                    p.title AS position_title,
                    p.department AS department_name
                FROM employees e
                JOIN positions p ON p.id = e.position_id
                WHERE e.user_id = :user_id
                LIMIT 1
            ");
            $stmt->execute(['user_id' => $userId]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($res) {
                return $res;
            }
        } catch (Exception $e) {
            // Fallback mock employee
        }

        return [
            'id' => 1,
            'user_id' => $userId,
            'first_name' => 'Marek',
            'last_name' => 'Kowalski',
            'position_title' => 'Lead Biomechanics & PT Coach',
            'department_name' => 'coaching',
            'salary_type' => 'hybrid',
            'base_salary' => 6500.00,
            'rate_per_session' => 85.00,
            'salary_payout_day' => 10,
            'iban_account' => 'PL84 1050 1445 1000 0090 8124 5510',
            'sessions_this_month' => 32,
            'total_calculated_salary' => 9220.00,
            'payroll_status' => 'Pending Approval (Accountant notified)'
        ];
    }

    /**
     * Get trainer's today schedule and upcoming sessions
     */
    public static function getTrainerSchedule(int $employeeId): array
    {
        return [
            [
                'time' => '09:00 - 10:00',
                'client_name' => 'Mateusz Kamiński',
                'type' => '1-on-1 Biomechanics Squat Analysis',
                'zone' => 'Biomechanics & Iron Lab',
                'status' => 'completed',
                'notes' => 'Checking hip mobility and bar velocity after cryo'
            ],
            [
                'time' => '11:30 - 12:30',
                'client_name' => 'Zofia Lewandowska',
                'type' => 'Hyrox Conditioning & Ergometer',
                'zone' => 'Endurance Arena',
                'status' => 'in_progress',
                'notes' => 'Target: 500m row intervals + sled push'
            ],
            [
                'time' => '15:00 - 16:00',
                'client_name' => 'David Thorne (Expat)',
                'type' => 'Shoulder & Thoracic Rehab Drills',
                'zone' => 'Movement & Mobility Suite',
                'status' => 'scheduled',
                'notes' => 'Rotator cuff activation and posture correction'
            ],
            [
                'time' => '18:00 - 19:00',
                'client_name' => 'HYROX Power Squad (Group)',
                'type' => 'Group Athletic Masterclass',
                'zone' => 'Group Arena A',
                'status' => 'scheduled',
                'notes' => '18 registered participants'
            ]
        ];
    }

    /**
     * Get trainer's roster of clients
     */
    public static function getTrainerClients(int $employeeId): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->query("
                SELECT u.full_name AS name, u.email, u.phone, u.fitness_goal AS goal,
                       COALESCE(mp.name, 'Premium 24/7') AS plan,
                       14 AS sessions_completed,
                       'Today at 09:00' AS last_session,
                       '92%' AS progress
                FROM users u
                LEFT JOIN user_memberships um ON um.user_id = u.id AND um.status IN ('active', 'frozen')
                LEFT JOIN membership_plans mp ON mp.id = um.plan_id
                WHERE u.role = 'client'
                ORDER BY u.id ASC
                LIMIT 5
            ");
            if ($stmt) {
                $dbClients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($dbClients)) {
                    return $dbClients;
                }
            }
        } catch (Exception $e) {
            // Fallback
        }

        return [
            [
                'name' => 'Mateusz Kamiński',
                'email' => 'mateusz.k@example.com',
                'phone' => '+48 501 884 921',
                'plan' => 'Premium 24/7',
                'goal' => 'Hypertrophy & Biomechanics',
                'sessions_completed' => 14,
                'last_session' => 'Today at 09:00',
                'progress' => '92%'
            ],
            [
                'name' => 'Zofia Lewandowska',
                'email' => 'zofia.l@example.com',
                'phone' => '+48 602 119 443',
                'plan' => 'Max All-Inclusive',
                'goal' => 'Hyrox Competition Warsaw',
                'sessions_completed' => 22,
                'last_session' => 'Today at 11:30',
                'progress' => '88%'
            ],
            [
                'name' => 'David Thorne',
                'email' => 'david.thorne@example.com',
                'phone' => '+48 791 405 112',
                'plan' => 'Premium 24/7',
                'goal' => 'Posture Rehab & Desk Fatigue',
                'sessions_completed' => 8,
                'last_session' => 'Scheduled Today',
                'progress' => '75%'
            ],
            [
                'name' => 'Karolina Wiśniewska',
                'email' => 'karolina.w@example.com',
                'phone' => '+48 512 900 123',
                'plan' => 'StandartPlus',
                'goal' => 'Fat Loss & Core Strength',
                'sessions_completed' => 5,
                'last_session' => 'Yesterday at 17:00',
                'progress' => '65%'
            ]
        ];
    }

    /**
     * Mock data helpers for offline / instant demo
     */
    private static function getMockUserByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));

        // Demo trainer account
        if (strpos($email, 'trainer') !== false || strpos($email, 'coach') !== false) {
            return [
                'id' => 101,
                'full_name' => 'Marek Kowalski',
                'email' => $email,
                'phone' => '+48 500 999 111',
                'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
                'role' => 'employee',
                'lead_status' => 'converted',
                'fitness_goal' => 'Olympic Biomechanics Coach',
                'preferred_language' => 'pl',
                'free_courses_unlocked' => 1
            ];
        }

        // Demo lead account (free courses)
        if (strpos($email, 'lead') !== false || strpos($email, 'free') !== false) {
            return [
                'id' => 102,
                'full_name' => 'Piotr Zieliński',
                'email' => $email,
                'phone' => '+48 501 222 333',
                'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
                'role' => 'lead',
                'lead_status' => 'new',
                'fitness_goal' => 'Fix Posture & Spine Rehab',
                'preferred_language' => 'pl',
                'free_courses_unlocked' => 1
            ];
        }

        // Demo admin / manager account (Full CRM access)
        if (strpos($email, 'admin') !== false || strpos($email, 'manager') !== false) {
            return [
                'id' => 103,
                'full_name' => 'Katarzyna Wiśniewska',
                'email' => $email,
                'phone' => '+48 500 888 777',
                'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
                'role' => 'admin',
                'lead_status' => 'converted',
                'fitness_goal' => 'Club General Management',
                'preferred_language' => 'pl',
                'free_courses_unlocked' => 1
            ];
        }

        // Default demo paying client (Premium)
        return [
            'id' => 1,
            'full_name' => 'Alex Morgan',
            'email' => $email ?: 'member@kinetica.gym',
            'phone' => '+48 500 123 456',
            'password_hash' => password_hash('password123', PASSWORD_BCRYPT),
            'role' => 'client',
            'lead_status' => 'converted',
            'fitness_goal' => 'Hypertrophy & Biomechanics',
            'preferred_language' => 'en',
            'free_courses_unlocked' => 1
        ];
    }

    private static function getMockUserById(int $id): ?array
    {
        if ($id === 101) {
            return self::getMockUserByEmail('trainer@kinetica.gym');
        }
        if ($id === 102) {
            return self::getMockUserByEmail('lead@kinetica.gym');
        }
        return self::getMockUserByEmail('member@kinetica.gym');
    }

    public static function getMockMembership(int $userId): array
    {
        // Only pre-seeded demo member has an active pass by default
        if ($userId === 1) {
            return [
                'is_active_paid' => true,
                'plan_name' => 'Premium Pass',
                'plan_code' => 'premium',
                'plan_badge' => '🔥 MOST POPULAR • 24/7 VIP',
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-d', strtotime('+22 days')),
                'status' => 'active',
                'days_left' => 22,
                'is_24_7_access' => 1,
                'has_group_classes' => 1,
                'has_sauna' => 1,
                'cryo_sessions_limit' => 4,
                'cryo_sessions_used' => 1,
                'guest_passes_per_month' => 2,
                'guest_passes_used' => 0,
                'freeze_days_remaining' => 30,
                'purchased_price' => 319.00
            ];
        }

        // Newly registered users or leads have Free Community Tier until they purchase a pass
        return self::getFreeTierMembership();
    }
}
