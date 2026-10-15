<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\User;
use App\Core\Database;
use PDO;
use Exception;

class CheckoutController extends Controller
{
    /**
     * Show Checkout & Payment Simulation Page
     */
    public function index(): void
    {
        $planCode = strtolower(trim($_GET['plan'] ?? 'premium'));
        $cycle = strtolower(trim($_GET['cycle'] ?? 'monthly')) === 'annual' ? 'annual' : 'monthly';

        $lang = $_SESSION['lang'] ?? 'en';
        if (!in_array($lang, ['en', 'pl'], true)) {
            $lang = 'en';
        }

        $content = require __DIR__ . '/../../config/landing_content.php';
        $plansList = $content[$lang]['pricing_section']['plans'] ?? $content['en']['pricing_section']['plans'];

        $selectedPlan = null;
        foreach ($plansList as $p) {
            if ($p['id'] === $planCode) {
                $selectedPlan = $p;
                break;
            }
        }

        if (!$selectedPlan) {
            $selectedPlan = $plansList[2]; // Default Premium
        }

        $user = Auth::user();

        $this->render('checkout/index', [
            'title' => ($lang === 'pl' ? 'Aktywacja Karnetu' : 'Membership Pass Activation') . ' | Kinetica Warsaw',
            'plan' => $selectedPlan,
            'cycle' => $cycle,
            'user' => $user,
            'lang' => $lang
        ], 'layouts/checkout');
    }

    /**
     * Process simulated payment (Sandbox Simulator for Repository)
     */
    public function process(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

        $planCode = strtolower(trim($input['plan'] ?? 'premium'));
        $cycle = strtolower(trim($input['cycle'] ?? 'monthly'));
        $method = strtolower(trim($input['payment_method'] ?? 'blik'));
        $email = trim($input['email'] ?? '');
        $name = trim($input['name'] ?? 'Athlete');
        $phone = trim($input['phone'] ?? '+48 500 123 456');

        // Determine price
        $content = require __DIR__ . '/../../config/landing_content.php';
        $plansList = $content['en']['pricing_section']['plans'];
        $planData = $plansList[2];
        foreach ($plansList as $p) {
            if ($p['id'] === $planCode) {
                $planData = $p;
                break;
            }
        }

        $price = $cycle === 'annual' ? ($planData['price_annual'] * 12) : $planData['price_monthly'];
        $txRef = 'SIM-' . strtoupper($method) . '-' . rand(100000, 999999);

        // If user is not logged in, auto-create/find user account
        $userId = Auth::id();
        if (!$userId && !empty($email)) {
            $existing = User::findByEmail($email);
            if ($existing) {
                $userId = (int)$existing['id'];
                Auth::login($existing);
            } else {
                $guestPass = !empty($input['password']) ? $input['password'] : bin2hex(random_bytes(6));
                $userId = User::create([
                    'full_name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $guestPass,
                    'role' => 'client',
                    'plan_code' => $planCode
                ]);
                $newUser = User::findById($userId) ?: [
                    'id' => $userId,
                    'full_name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'role' => 'client',
                    'free_courses_unlocked' => true
                ];
                Auth::login($newUser);
            }
        }

        // Record simulated payment in DB if available
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("
                INSERT INTO payments (
                    user_id, amount, currency, payment_method, 
                    payment_status, transaction_reference, paid_at
                ) VALUES (
                    :user_id, :amount, 'PLN', :method, 
                    'completed', :tx_ref, NOW()
                )
            ");
            $stmt->execute([
                'user_id' => $userId ?: 1,
                'amount'  => $price,
                'method'  => $method,
                'tx_ref'  => $txRef
            ]);
        } catch (Exception $e) {
            // Safe fallback for demo
        }

        // Activate membership in DB
        if ($userId) {
            User::activateMembership($userId, $planCode, $cycle);
        }

        $this->json([
            'success' => true,
            'message' => 'Simulated payment successfully processed!',
            'receipt' => [
                'transaction_ref' => $txRef,
                'plan_name' => $planData['name'],
                'amount_paid' => number_format($price, 2) . ' PLN',
                'method' => strtoupper($method),
                'status' => 'APPROVED (Sandbox)',
                'activation_code' => 'NFC-' . rand(100000, 999999),
                'dashboard_url' => '/dashboard'
            ]
        ]);
    }
}
