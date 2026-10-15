<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Session;
use PDO;
use Exception;

class Crm
{
    /**
     * Get Executive Overview Metrics & Analytics
     */
    public static function getExecutiveStats(): array
    {
        $mrr = 86420.00;
        $activeMembers = 342;
        $leadsCount = 58;
        $conversionRate = 28.4;
        $visitsToday = 148;
        $occupancyCurrent = 42;
        $occupancyMax = 120;

        try {
            $pdo = Database::getConnection();
            
            // Total active paying members
            $stmt = $pdo->query("SELECT COUNT(*) FROM user_memberships WHERE status = 'active' AND end_date >= CURDATE()");
            if ($stmt) {
                $count = (int)$stmt->fetchColumn();
                if ($count > 0) $activeMembers = $count;
            }

            // Calculate Monthly Recurring Revenue
            $stmtMrr = $pdo->query("SELECT SUM(purchased_price) FROM user_memberships WHERE status = 'active' AND end_date >= CURDATE()");
            if ($stmtMrr) {
                $val = (float)$stmtMrr->fetchColumn();
                if ($val > 0) $mrr = $val;
            }

            // Leads count
            $stmtLeads = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'lead' OR lead_status != 'converted'");
            if ($stmtLeads) {
                $leads = (int)$stmtLeads->fetchColumn();
                if ($leads > 0) $leadsCount = $leads;
            }

            // Visits today
            $stmtVisits = $pdo->query("SELECT COUNT(*) FROM gym_visits WHERE DATE(entry_time) = CURDATE()");
            if ($stmtVisits) {
                $v = (int)$stmtVisits->fetchColumn();
                if ($v > 0) $visitsToday = $v;
            }

            // Real-time occupancy from active gym visits (entered within 2 hours without recorded exit)
            $stmtOcc = $pdo->query("SELECT COUNT(*) FROM gym_visits WHERE (exit_time IS NULL OR exit_time > NOW()) AND entry_time >= DATE_SUB(NOW(), INTERVAL 2 HOUR)");
            if ($stmtOcc) {
                $occ = (int)$stmtOcc->fetchColumn();
                if ($occ > 0) $occupancyCurrent = $occ;
            }
        } catch (Exception $e) {
            // Safe fallback to simulated stats
        }

        // Incorporate recent simulated turnstile entries from session
        $simulatedLogs = Session::get('crm_mock_turnstiles') ?: [];
        if (!empty($simulatedLogs)) {
            $occupancyCurrent = min($occupancyMax, max(12, $occupancyCurrent + count($simulatedLogs)));
        }

        return [
            'mrr' => $mrr,
            'active_members' => $activeMembers,
            'leads_count' => $leadsCount,
            'conversion_rate' => $conversionRate,
            'visits_today' => $visitsToday,
            'occupancy_current' => $occupancyCurrent,
            'occupancy_max' => $occupancyMax,
            'occupancy_percentage' => round(($occupancyCurrent / $occupancyMax) * 100),
            'currency' => 'PLN',
            'next_payout_date' => date('Y-m-10', strtotime('+1 month')),
            'retention_alerts_count' => 6
        ];
    }

    /**
     * Get Leads organized for Sales Kanban Board
     */
    public static function getLeads(): array
    {
        // Try DB first
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->query("
                SELECT u.*, 
                    DATEDIFF(CURDATE(), u.created_at) AS days_ago
                FROM users u 
                WHERE u.role = 'lead' OR u.lead_status != 'converted'
                ORDER BY u.created_at DESC
            ");
            $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            if (!empty($rows)) {
                return self::groupLeadsByStatus($rows);
            }
        } catch (Exception $e) {
            // Fallback to mock session data
        }

        $sessionLeads = Session::get('crm_mock_leads');
        if (!$sessionLeads) {
            $sessionLeads = self::getDefaultMockLeads();
            Session::set('crm_mock_leads', $sessionLeads);
        }

        return self::groupLeadsByStatus($sessionLeads);
    }

    /**
     * Helper to group leads into Kanban stages
     */
    private static function groupLeadsByStatus(array $leads): array
    {
        $kanban = [
            'new' => [],
            'contacted' => [],
            'trial_scheduled' => [],
            'converted' => [],
            'unqualified' => []
        ];

        foreach ($leads as $lead) {
            $status = $lead['lead_status'] ?? 'new';
            if (!isset($kanban[$status])) {
                $status = 'new';
            }
            // Calculate lead score based on activities
            $lead['score'] = self::calculateLeadScore($lead);
            $kanban[$status][] = $lead;
        }

        return $kanban;
    }

    /**
     * Calculate dynamic Lead Score (0 - 100)
     */
    private static function calculateLeadScore(array $lead): int
    {
        $score = 25; // Base registration score
        if (!empty($lead['free_courses_unlocked'])) $score += 20;
        if (!empty($lead['phone'])) $score += 15;
        if (($lead['lead_source'] ?? '') === 'website_order') $score += 25;
        if (($lead['lead_status'] ?? '') === 'trial_scheduled') $score += 20;
        if (!empty($lead['manager_notes'])) $score += 10;
        return min(98, $score);
    }

    /**
     * Update Lead Status in Kanban pipeline
     */
    public static function updateLeadStatus(int $leadId, string $newStatus, ?string $notes = null, ?string $followUpDate = null): bool
    {
        try {
            $pdo = Database::getConnection();
            $sql = "UPDATE users SET lead_status = :status";
            $params = ['status' => $newStatus, 'id' => $leadId];

            if ($notes !== null) {
                $sql .= ", manager_notes = CONCAT(COALESCE(manager_notes, ''), '\n[" . date('d.m.Y H:i') . "] ', :notes)";
                $params['notes'] = trim($notes);
            }
            if ($followUpDate !== null) {
                $sql .= ", next_follow_up_date = :f_date";
                $params['f_date'] = $followUpDate;
            }
            $sql .= ", last_contacted_at = NOW() WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } catch (Exception $e) {
            // Update session mock
        }

        $sessionLeads = Session::get('crm_mock_leads') ?: self::getDefaultMockLeads();
        foreach ($sessionLeads as &$lead) {
            if ((int)$lead['id'] === $leadId) {
                $lead['lead_status'] = $newStatus;
                $lead['last_contacted_at'] = date('Y-m-d H:i');
                if ($notes) {
                    $lead['manager_notes'] = ($lead['manager_notes'] ?? '') . "\n[" . date('d.m.Y H:i') . "] " . $notes;
                }
                if ($followUpDate) {
                    $lead['next_follow_up_date'] = $followUpDate;
                }
                break;
            }
        }
        Session::set('crm_mock_leads', $sessionLeads);
        return true;
    }

    /**
     * Create new lead manually via CRM Reception / Phone
     */
    public static function createLead(array $data): int
    {
        $id = rand(200, 999);
        $newLead = [
            'id' => $id,
            'full_name' => trim($data['full_name'] ?? 'New Prospect'),
            'email' => strtolower(trim($data['email'] ?? 'lead' . $id . '@example.com')),
            'phone' => trim($data['phone'] ?? '+48 500 000 000'),
            'role' => 'lead',
            'lead_status' => $data['lead_status'] ?? 'new',
            'lead_source' => $data['lead_source'] ?? 'reception_walk_in',
            'fitness_goal' => $data['fitness_goal'] ?? 'Biomechanics & Health',
            'preferred_language' => $data['preferred_language'] ?? 'pl',
            'free_courses_unlocked' => 1,
            'manager_notes' => trim($data['notes'] ?? 'Created manually by sales staff.'),
            'created_at' => date('Y-m-d H:i:s'),
            'days_ago' => 0
        ];

        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO users (
                    full_name, email, phone, role, lead_status, lead_source, 
                    fitness_goal, preferred_language, free_courses_unlocked, manager_notes
                ) VALUES (
                    :name, :email, :phone, 'lead', :status, :source,
                    :goal, :lang, 1, :notes
                )
            ");
            $stmt->execute([
                'name' => $newLead['full_name'],
                'email' => $newLead['email'],
                'phone' => $newLead['phone'],
                'status' => $newLead['lead_status'],
                'source' => $newLead['lead_source'],
                'goal' => $newLead['fitness_goal'],
                'lang' => $newLead['preferred_language'],
                'notes' => $newLead['manager_notes']
            ]);
            $id = (int)$pdo->lastInsertId();
            $newLead['id'] = $id;
        } catch (Exception $e) {
            // Safe fallback
        }

        $sessionLeads = Session::get('crm_mock_leads') ?: self::getDefaultMockLeads();
        array_unshift($sessionLeads, $newLead);
        Session::set('crm_mock_leads', $sessionLeads);
        return $id;
    }

    /**
     * Get All Athletes / Paying Clients with 360° Profile & Retention telemetry
     */
    public static function getAthletes(string $search = '', string $filterStatus = 'all'): array
    {
        $athletes = Session::get('crm_mock_athletes');
        if (!$athletes) {
            $athletes = self::getDefaultMockAthletes();
            Session::set('crm_mock_athletes', $athletes);
        }

        // Apply filters
        $result = [];
        $search = strtolower(trim($search));

        foreach ($athletes as $a) {
            if ($filterStatus !== 'all' && ($a['membership_status'] ?? '') !== $filterStatus) {
                continue;
            }
            if ($search !== '') {
                $nameMatch = strpos(strtolower($a['full_name']), $search) !== false;
                $emailMatch = strpos(strtolower($a['email']), $search) !== false;
                $phoneMatch = strpos(strtolower($a['phone']), $search) !== false;
                if (!$nameMatch && !$emailMatch && !$phoneMatch) {
                    continue;
                }
            }
            $result[] = $a;
        }

        return $result;
    }

    /**
     * Perform Athlete Membership Action (Freeze, Extend, Grant Cryo Session)
     */
    public static function athleteAction(int $athleteId, string $action, array $params = []): bool
    {
        $athletes = Session::get('crm_mock_athletes') ?: self::getDefaultMockAthletes();
        foreach ($athletes as &$a) {
            if ((int)$a['id'] === $athleteId) {
                if ($action === 'freeze') {
                    $a['membership_status'] = 'frozen';
                    $a['freeze_days_remaining'] = max(0, ($a['freeze_days_remaining'] ?? 30) - 14);
                } elseif ($action === 'unfreeze') {
                    $a['membership_status'] = 'active';
                } elseif ($action === 'extend_30_days') {
                    $a['days_left'] = ($a['days_left'] ?? 0) + 30;
                    $a['membership_status'] = 'active';
                } elseif ($action === 'grant_cryo') {
                    $a['cryo_sessions_limit'] = ($a['cryo_sessions_limit'] ?? 0) + 1;
                }
                break;
            }
        }
        Session::set('crm_mock_athletes', $athletes);
        return true;
    }

    /**
     * Get Access Control & Live Warsaw Turnstile Scans Feed
     */
    public static function getTurnstileLogs(int $limit = 15): array
    {
        $logs = Session::get('crm_turnstile_logs');
        if (!$logs) {
            $logs = self::getDefaultTurnstileLogs();
            Session::set('crm_turnstile_logs', $logs);
        }
        return array_slice($logs, 0, $limit);
    }

    /**
     * Simulate an entry/exit scan at Warsaw Turnstile
     */
    public static function simulateScan(string $athleteName, string $method = 'nfc', string $gate = 'Warsaw Main Gate - Turnstile 01'): array
    {
        $newLog = [
            'id' => rand(1000, 9999),
            'athlete_name' => $athleteName,
            'gate' => $gate,
            'method' => strtoupper($method),
            'direction' => rand(0, 10) > 2 ? 'ENTRY' : 'EXIT',
            'time' => date('H:i:s'),
            'date' => date('d.m.Y'),
            'status' => 'APPROVED',
            'status_badge' => 'granted'
        ];

        $logs = Session::get('crm_turnstile_logs') ?: self::getDefaultTurnstileLogs();
        array_unshift($logs, $newLog);
        Session::set('crm_turnstile_logs', array_slice($logs, 0, 50));
        return $newLog;
    }

    /**
     * Get Coaching & Training Sessions Schedule
     */
    public static function getSchedule(string $date = ''): array
    {
        $schedule = Session::get('crm_mock_schedule');
        if (!$schedule) {
            $schedule = self::getDefaultSchedule();
            Session::set('crm_mock_schedule', $schedule);
        }
        return $schedule;
    }

    /**
     * Book athlete into a schedule slot
     */
    public static function bookSession(int $sessionId, string $athleteName): bool
    {
        $schedule = Session::get('crm_mock_schedule') ?: self::getDefaultSchedule();
        foreach ($schedule as &$item) {
            if ((int)$item['id'] === $sessionId) {
                $item['booked_count'] = min($item['capacity'], $item['booked_count'] + 1);
                $item['attendees'][] = $athleteName;
                break;
            }
        }
        Session::set('crm_mock_schedule', $schedule);
        return true;
    }

    /**
     * Get Coach Payroll & Accounting Automation Records
     */
    public static function getPayrollData(): array
    {
        $payroll = Session::get('crm_mock_payroll');
        if (!$payroll) {
            $payroll = self::getDefaultPayrollRecords();
            Session::set('crm_mock_payroll', $payroll);
        }
        return $payroll;
    }

    /**
     * Approve payroll statement for disbursement
     */
    public static function approvePayroll(int $employeeId): bool
    {
        $payroll = Session::get('crm_mock_payroll') ?: self::getDefaultPayrollRecords();
        foreach ($payroll as &$p) {
            if ((int)$p['employee_id'] === $employeeId) {
                $p['status'] = 'approved';
                $p['approved_at'] = date('Y-m-d H:i');
                break;
            }
        }
        Session::set('crm_mock_payroll', $payroll);
        return true;
    }

    /**
     * Mark payroll as paid / exported to accountant
     */
    public static function markPayrollPaid(int $employeeId): bool
    {
        $payroll = Session::get('crm_mock_payroll') ?: self::getDefaultPayrollRecords();
        foreach ($payroll as &$p) {
            if ((int)$p['employee_id'] === $employeeId) {
                $p['status'] = 'paid';
                $p['paid_at'] = date('Y-m-d H:i');
                break;
            }
        }
        Session::set('crm_mock_payroll', $payroll);
        return true;
    }

    // -------------------------------------------------------------------------
    // DEFAULT MOCK GENERATORS FOR INSTANT PLUG-AND-PLAY EXECUTION
    // -------------------------------------------------------------------------

    private static function getDefaultMockLeads(): array
    {
        return [
            [
                'id' => 102,
                'full_name' => 'Piotr Zieliński',
                'email' => 'piotr.zielinski@gmail.com',
                'phone' => '+48 501 222 333',
                'lead_status' => 'new',
                'lead_source' => 'free_courses',
                'fitness_goal' => 'Desk Posture & Spine Rehab',
                'preferred_language' => 'pl',
                'free_courses_unlocked' => 1,
                'manager_notes' => 'Downloaded Bulletproof Squat & Posture courses. Interested in physio assessment.',
                'next_follow_up_date' => date('Y-m-d', strtotime('+1 day')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'days_ago' => 0
            ],
            [
                'id' => 104,
                'full_name' => 'Kacper Adamski',
                'email' => 'kacper.adamski@wp.pl',
                'phone' => '+48 602 888 192',
                'lead_status' => 'new',
                'lead_source' => 'instagram_ad',
                'fitness_goal' => 'Hyrox Endurance & Strength',
                'preferred_language' => 'pl',
                'free_courses_unlocked' => 1,
                'manager_notes' => 'Clicked ad for Warsaw Hyrox preparation training.',
                'next_follow_up_date' => date('Y-m-d', strtotime('+2 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 hours')),
                'days_ago' => 0
            ],
            [
                'id' => 105,
                'full_name' => 'Marta Lewandowska',
                'email' => 'marta.lewandowska@outlook.com',
                'phone' => '+48 512 400 300',
                'lead_status' => 'contacted',
                'lead_source' => 'free_courses',
                'fitness_goal' => 'Sub-Zero Cryo & Recovery',
                'preferred_language' => 'pl',
                'free_courses_unlocked' => 1,
                'manager_notes' => '[11.10 14:20] Called via phone. Sent WhatsApp brochure about Premium tier with 4 cryo sessions.',
                'next_follow_up_date' => date('Y-m-d', strtotime('+1 day')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'days_ago' => 1
            ],
            [
                'id' => 106,
                'full_name' => 'Thomas Miller',
                'email' => 't.miller@expatair.com',
                'phone' => '+48 799 123 456',
                'lead_status' => 'trial_scheduled',
                'lead_source' => 'reception_walk_in',
                'fitness_goal' => 'Biomechanics & Hypertrophy',
                'preferred_language' => 'en',
                'free_courses_unlocked' => 1,
                'manager_notes' => 'Expat living in Złota 44 tower. Trial biomechanics screening booked with Coach Marek for tomorrow at 18:00.',
                'next_follow_up_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'days_ago' => 2
            ],
            [
                'id' => 107,
                'full_name' => 'Zofia Kaczmarek',
                'email' => 'zofia.kaczmarek@onet.pl',
                'phone' => '+48 503 999 888',
                'lead_status' => 'converted',
                'lead_source' => 'free_courses',
                'fitness_goal' => 'Spine Decompression',
                'preferred_language' => 'pl',
                'free_courses_unlocked' => 1,
                'manager_notes' => 'Purchased StandartPlus Pass via dashboard after watching physio webinar.',
                'next_follow_up_date' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
                'days_ago' => 4
            ],
            [
                'id' => 108,
                'full_name' => 'Jakub Nowicki',
                'email' => 'j.nowicki@gmail.com',
                'phone' => '+48 691 444 222',
                'lead_status' => 'unqualified',
                'lead_source' => 'free_courses',
                'fitness_goal' => 'Powerlifting',
                'preferred_language' => 'pl',
                'free_courses_unlocked' => 1,
                'manager_notes' => 'Lives in Kraków, downloaded video courses only, not moving to Warsaw.',
                'next_follow_up_date' => null,
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
                'days_ago' => 6
            ]
        ];
    }

    private static function getDefaultMockAthletes(): array
    {
        return [
            [
                'id' => 1,
                'full_name' => 'Alex Morgan',
                'email' => 'member@kinetica.gym',
                'phone' => '+48 500 123 456',
                'plan_name' => 'Premium Pass',
                'plan_code' => 'premium',
                'membership_status' => 'active',
                'days_left' => 22,
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-d', strtotime('+22 days')),
                'cryo_sessions_limit' => 4,
                'cryo_sessions_used' => 1,
                'guest_passes_remaining' => 2,
                'freeze_days_remaining' => 30,
                'price_monthly' => 319.00,
                'nfc_tag' => 'NFC-8A2F910C',
                'last_visit' => date('d.m.Y H:i', strtotime('-18 hours')),
                'days_since_last_visit' => 1,
                'churn_alert' => false
            ],
            [
                'id' => 201,
                'full_name' => 'Michał Wiśniewski',
                'email' => 'm.wisniewski@techcorp.pl',
                'phone' => '+48 600 777 999',
                'plan_name' => 'Max Pass (All-Inclusive)',
                'plan_code' => 'max',
                'membership_status' => 'active',
                'days_left' => 28,
                'start_date' => date('Y-m-05'),
                'end_date' => date('Y-m-d', strtotime('+28 days')),
                'cryo_sessions_limit' => 999,
                'cryo_sessions_used' => 8,
                'guest_passes_remaining' => 4,
                'freeze_days_remaining' => 30,
                'price_monthly' => 499.00,
                'nfc_tag' => 'NFC-4B71EE02',
                'last_visit' => date('d.m.Y H:i', strtotime('-2 hours')),
                'days_since_last_visit' => 0,
                'churn_alert' => false
            ],
            [
                'id' => 202,
                'full_name' => 'Karolina Wójcik',
                'email' => 'karolina.w@lawfirm.pl',
                'phone' => '+48 505 444 333',
                'plan_name' => 'StandartPlus Pass',
                'plan_code' => 'standart_plus',
                'membership_status' => 'active',
                'days_left' => 14,
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-d', strtotime('+14 days')),
                'cryo_sessions_limit' => 0,
                'cryo_sessions_used' => 0,
                'guest_passes_remaining' => 0,
                'freeze_days_remaining' => 22,
                'price_monthly' => 239.00,
                'nfc_tag' => 'NFC-99C128FF',
                'last_visit' => date('d.m.Y', strtotime('-12 days')),
                'days_since_last_visit' => 12,
                'churn_alert' => true // Retention alert: 12 days absent!
            ],
            [
                'id' => 203,
                'full_name' => 'Bartosz Kamiński',
                'email' => 'bartosz.k@fintech.io',
                'phone' => '+48 518 901 234',
                'plan_name' => 'Standart Pass',
                'plan_code' => 'standart',
                'membership_status' => 'frozen',
                'days_left' => 19,
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-d', strtotime('+33 days')),
                'cryo_sessions_limit' => 0,
                'cryo_sessions_used' => 0,
                'guest_passes_remaining' => 0,
                'freeze_days_remaining' => 16,
                'price_monthly' => 179.00,
                'nfc_tag' => 'NFC-55AA3209',
                'last_visit' => date('d.m.Y', strtotime('-16 days')),
                'days_since_last_visit' => 16,
                'churn_alert' => false
            ],
            [
                'id' => 204,
                'full_name' => 'Anna Dąbrowska',
                'email' => 'anna.dabrowska@gmail.com',
                'phone' => '+48 601 321 654',
                'plan_name' => 'Premium Pass',
                'plan_code' => 'premium',
                'membership_status' => 'expired',
                'days_left' => 0,
                'start_date' => date('Y-m-d', strtotime('-35 days')),
                'end_date' => date('Y-m-d', strtotime('-5 days')),
                'cryo_sessions_limit' => 4,
                'cryo_sessions_used' => 4,
                'guest_passes_remaining' => 0,
                'freeze_days_remaining' => 30,
                'price_monthly' => 319.00,
                'nfc_tag' => 'NFC-11335577',
                'last_visit' => date('d.m.Y', strtotime('-6 days')),
                'days_since_last_visit' => 6,
                'churn_alert' => true // Expired renewal target
            ],
            [
                'id' => 205,
                'full_name' => 'David Smith',
                'email' => 'd.smith@embassy.gov',
                'phone' => '+48 790 888 777',
                'plan_name' => 'Max Pass (All-Inclusive)',
                'plan_code' => 'max',
                'membership_status' => 'active',
                'days_left' => 26,
                'start_date' => date('Y-m-03'),
                'end_date' => date('Y-m-d', strtotime('+26 days')),
                'cryo_sessions_limit' => 999,
                'cryo_sessions_used' => 5,
                'guest_passes_remaining' => 3,
                'freeze_days_remaining' => 30,
                'price_monthly' => 499.00,
                'nfc_tag' => 'NFC-77BB99AA',
                'last_visit' => date('d.m.Y H:i', strtotime('-1 day')),
                'days_since_last_visit' => 1,
                'churn_alert' => false
            ]
        ];
    }

    private static function getDefaultTurnstileLogs(): array
    {
        return [
            ['id' => 1, 'athlete_name' => 'Michał Wiśniewski', 'gate' => 'Warsaw Main Gate - Turnstile 01', 'method' => 'BIOMETRIC FACE-ID', 'direction' => 'ENTRY', 'time' => '17:42:15', 'date' => date('d.m.Y'), 'status' => 'APPROVED', 'status_badge' => 'granted'],
            ['id' => 2, 'athlete_name' => 'Alex Morgan', 'gate' => 'Warsaw Main Gate - Turnstile 02', 'method' => 'APPLE WALLET NFC', 'direction' => 'ENTRY', 'time' => '17:15:32', 'date' => date('d.m.Y'), 'status' => 'APPROVED', 'status_badge' => 'granted'],
            ['id' => 3, 'athlete_name' => 'David Smith', 'gate' => 'Sub-Zero Cryo Chamber Gate', 'method' => 'RFID BRACELET', 'direction' => 'ENTRY', 'time' => '16:55:04', 'date' => date('d.m.Y'), 'status' => 'APPROVED', 'status_badge' => 'granted'],
            ['id' => 4, 'athlete_name' => 'Anna Dąbrowska', 'gate' => 'Warsaw Main Gate - Turnstile 01', 'method' => 'MOBILE QR APP', 'direction' => 'ENTRY', 'time' => '16:20:10', 'date' => date('d.m.Y'), 'status' => 'DENIED (PASS EXPIRED)', 'status_badge' => 'denied'],
            ['id' => 5, 'athlete_name' => 'Janusz Kowalczyk', 'gate' => 'Finnish Sauna & Spa Zone', 'method' => 'APPLE WALLET NFC', 'direction' => 'EXIT', 'time' => '15:48:21', 'date' => date('d.m.Y'), 'status' => 'APPROVED', 'status_badge' => 'granted'],
            ['id' => 6, 'athlete_name' => 'Karolina Wójcik', 'gate' => 'Warsaw Main Gate - Turnstile 02', 'method' => 'BIOMETRIC FACE-ID', 'direction' => 'EXIT', 'time' => '14:30:19', 'date' => date('d.m.Y'), 'status' => 'APPROVED', 'status_badge' => 'granted'],
            ['id' => 7, 'athlete_name' => 'Piotr Krawczyk', 'gate' => 'Warsaw Main Gate - Turnstile 01', 'method' => 'NFC PASS', 'direction' => 'ENTRY', 'time' => '13:10:45', 'date' => date('d.m.Y'), 'status' => 'APPROVED', 'status_badge' => 'granted'],
            ['id' => 8, 'athlete_name' => 'Elena Rostova (Coach)', 'gate' => 'Staff Security Gate', 'method' => 'STAFF BIOMETRIC', 'direction' => 'ENTRY', 'time' => '12:00:11', 'date' => date('d.m.Y'), 'status' => 'APPROVED', 'status_badge' => 'granted'],
        ];
    }

    private static function getDefaultSchedule(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Hyrox Engine & High-Output Interval',
                'coach_name' => 'Coach Marek Kowalski',
                'zone' => 'Functional Turf & Sled Track',
                'time_start' => '17:00',
                'time_end' => '18:15',
                'capacity' => 16,
                'booked_count' => 14,
                'type' => 'group_class',
                'attendees' => ['Michał Wiśniewski', 'Alex Morgan', 'Tomasz B.', 'Kasia K.']
            ],
            [
                'id' => 2,
                'title' => '1-on-1 Biomechanics & Spine Decompression',
                'coach_name' => 'Dr. Elena Rostova',
                'zone' => 'Private Rehab Studio A',
                'time_start' => '18:30',
                'time_end' => '19:30',
                'capacity' => 1,
                'booked_count' => 1,
                'type' => 'personal_training',
                'attendees' => ['Thomas Miller (Trial Prospect)']
            ],
            [
                'id' => 3,
                'title' => '-110°C Whole-Body Cryotherapy Batch',
                'coach_name' => 'Kasia Dąbrowska (Lead Physio)',
                'zone' => 'Sub-Zero Cryo Chamber',
                'time_start' => '19:00',
                'time_end' => '19:45',
                'capacity' => 6,
                'booked_count' => 4,
                'type' => 'spa_recovery',
                'attendees' => ['David Smith', 'Alex Morgan', 'Piotr G.', 'Robert M.']
            ],
            [
                'id' => 4,
                'title' => 'Olympic Weightlifting & Barbell Mechanics',
                'coach_name' => 'Coach Marek Kowalski',
                'zone' => 'Eleiko Platform Arena',
                'time_start' => '20:00',
                'time_end' => '21:15',
                'capacity' => 10,
                'booked_count' => 8,
                'type' => 'group_class',
                'attendees' => ['Kacper Adamski', 'Marcin Z.', 'Wojciech S.']
            ]
        ];
    }

    private static function getDefaultPayrollRecords(): array
    {
        return [
            [
                'employee_id' => 101,
                'name' => 'Marek Kowalski',
                'position' => 'Lead Biomechanics Coach',
                'department' => 'Coaching',
                'salary_type' => 'hybrid',
                'base_salary' => 6500.00,
                'rate_per_session' => 85.00,
                'sessions_completed' => 34,
                'commissions_amount' => 2890.00, // 34 * 85
                'bonus_amount' => 500.00,
                'total_gross' => 9890.00,
                'total_net' => 7912.00,
                'iban' => 'PL84 1050 1445 1000 0090 8124 5510',
                'payout_day' => 10,
                'status' => 'calculated', // calculated, approved, paid
                'approved_at' => null,
                'paid_at' => null
            ],
            [
                'employee_id' => 102,
                'name' => 'Dr. Elena Rostova',
                'position' => 'Sports Physiotherapist & Rehab Spec.',
                'department' => 'Medical & Physio',
                'salary_type' => 'per_session',
                'base_salary' => 4000.00,
                'rate_per_session' => 120.00,
                'sessions_completed' => 42,
                'commissions_amount' => 5040.00, // 42 * 120
                'bonus_amount' => 750.00,
                'total_gross' => 9790.00,
                'total_net' => 7832.00,
                'iban' => 'PL12 1140 2004 0000 3102 7891 4420',
                'payout_day' => 10,
                'status' => 'approved',
                'approved_at' => date('Y-m-d H:i', strtotime('-1 day')),
                'paid_at' => null
            ],
            [
                'employee_id' => 103,
                'name' => 'Kasia Dąbrowska',
                'position' => 'Recovery & Cryo Chamber Supervisor',
                'department' => 'Medical & Spa',
                'salary_type' => 'fixed_monthly',
                'base_salary' => 6200.00,
                'rate_per_session' => 0.00,
                'sessions_completed' => 0,
                'commissions_amount' => 0.00,
                'bonus_amount' => 400.00,
                'total_gross' => 6600.00,
                'total_net' => 5280.00,
                'iban' => 'PL49 1020 2892 2276 3005 0000 1198',
                'payout_day' => 10,
                'status' => 'calculated',
                'approved_at' => null,
                'paid_at' => null
            ],
            [
                'employee_id' => 104,
                'name' => 'Tomasz Bąk',
                'position' => 'Front Desk & Member Concierge',
                'department' => 'Reception',
                'salary_type' => 'hourly',
                'base_salary' => 4800.00,
                'rate_per_session' => 0.00,
                'sessions_completed' => 0,
                'commissions_amount' => 0.00,
                'bonus_amount' => 300.00,
                'total_gross' => 5100.00,
                'total_net' => 4080.00,
                'iban' => 'PL90 1090 1014 0000 0001 2293 8841',
                'payout_day' => 10,
                'status' => 'paid',
                'approved_at' => date('Y-m-d H:i', strtotime('-3 days')),
                'paid_at' => date('Y-m-d H:i', strtotime('-2 days'))
            ]
        ];
    }
}
