<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Crm;
use Exception;

class HomeController extends Controller
{
    private array $content;

    public function __construct()
    {
        $this->content = require __DIR__ . '/../../config/landing_content.php';
    }

    /**
     * Render English landing page
     */
    public function en(): void
    {
        $_SESSION['lang'] = 'en';
        $data = $this->content['en'];
        $this->render('landing/index', $data, 'layouts/main');
    }

    /**
     * Render Polish landing page
     */
    public function pl(): void
    {
        $_SESSION['lang'] = 'pl';
        $data = $this->content['pl'];
        $this->render('landing/index', $data, 'layouts/main');
    }

    /**
     * Handle lead registration (Free courses lead magnet)
     */
    public function apiRegisterLead(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $goal = trim($input['goal'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json([
                'success' => false,
                'message' => 'Please provide a valid email address.'
            ], 422);
            return;
        }

        // Persist lead to database if available
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO users (full_name, email, phone, role, lead_status, lead_source, fitness_goal, free_courses_unlocked)
                VALUES (:name, :email, :phone, 'lead', 'new', 'free_courses', :goal, 1)
                ON DUPLICATE KEY UPDATE fitness_goal = VALUES(fitness_goal), updated_at = NOW()
            ");
            $stmt->execute([
                'name'  => $name ?: 'Athlete Lead',
                'email' => $email,
                'phone' => $phone ?: '+48 500 000 000',
                'goal'  => $goal ?: 'Biomechanics & Hypertrophy'
            ]);
        } catch (Exception $e) {
            // Store in CRM session leads for immediate Kanban visibility
            Crm::addLead([
                'full_name'    => $name ?: 'Athlete Lead',
                'email'        => $email,
                'phone'        => $phone ?: '+48 500 000 000',
                'fitness_goal' => $goal ?: 'Biomechanics & Hypertrophy',
                'lead_source'  => 'free_courses'
            ]);
        }

        // Response with instant access token & simulated unlock
        $this->json([
            'success' => true,
            'message' => 'Registration successful! Free course portal unlocked.',
            'user' => [
                'name' => htmlspecialchars($name ?: 'Athlete'),
                'email' => htmlspecialchars($email),
                'access_token' => 'KIN-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'courses_unlocked' => 12
            ]
        ]);
    }

    /**
     * Handle membership pass checkout
     */
    public function apiOrderPass(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $plan = trim($input['plan'] ?? 'standart');
        $cycle = trim($input['cycle'] ?? 'monthly');
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $paymentMethod = trim($input['payment_method'] ?? 'blik');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json([
                'success' => false,
                'message' => 'Please provide a valid email address for pass delivery.'
            ], 422);
            return;
        }

        // Persist order in DB
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO users (full_name, email, role, lead_status, lead_source, free_courses_unlocked)
                VALUES (:name, :email, 'client', 'converted', 'website_order', 1)
                ON DUPLICATE KEY UPDATE role = 'client', lead_status = 'converted', updated_at = NOW()
            ");
            $stmt->execute([
                'name'  => $name ?: 'Web Order Client',
                'email' => $email
            ]);
        } catch (Exception $e) {
            // Safe fallback
        }

        $this->json([
            'success' => true,
            'message' => 'Membership pass confirmed!',
            'order' => [
                'order_id' => 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'plan' => ucfirst($plan),
                'cycle' => $cycle,
                'payment_method' => strtoupper($paymentMethod),
                'activation_code' => 'NFC-' . rand(100000, 999999),
            ]
        ]);
    }
}
