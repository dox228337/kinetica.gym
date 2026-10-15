<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function loginForm(): void
    {
        $selectedPlan = !empty($_GET['plan']) ? strtolower(trim($_GET['plan'])) : null;
        $selectedCycle = strtolower(trim($_GET['cycle'] ?? 'monthly')) === 'annual' ? 'annual' : 'monthly';

        if (Auth::check()) {
            if ($selectedPlan) {
                header('Location: /checkout?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle));
            } else {
                header('Location: /dashboard');
            }
            exit;
        }

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');

        $this->render('auth/login', [
            'title' => 'Sign In | Kinetica Warsaw',
            'error' => $error,
            'success' => $success,
            'selectedPlan' => $selectedPlan,
            'selectedCycle' => $selectedCycle
        ], 'layouts/auth');
    }

    /**
     * Handle login submission
     */
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $selectedPlan = !empty($_POST['selected_plan']) ? strtolower(trim($_POST['selected_plan'])) : null;
        $selectedCycle = strtolower(trim($_POST['selected_cycle'] ?? 'monthly')) === 'annual' ? 'annual' : 'monthly';
        $redirectUrl = '/login' . ($selectedPlan ? '?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle) : '');

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Please enter both email and password.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        $user = User::findByEmail($email);

        if (!$user) {
            Session::setFlash('error', 'No account found with this email.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        // Verify password hash or allow demo login if password matches default
        $isValidPassword = false;
        if (!empty($user['password_hash'])) {
            $isValidPassword = password_verify($password, $user['password_hash']);
        }

        if (!$isValidPassword) {
            Session::setFlash('error', 'Incorrect password. Please try again.');
            header('Location: ' . $redirectUrl);
            exit;
        }

        // Regenerate session ID to prevent session fixation attacks
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        // Log the user in
        Auth::login($user);
        Session::setFlash('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');

        if (in_array(($user['role'] ?? ''), ['admin', 'employee'], true)) {
            header('Location: /crm');
        } elseif (!empty($selectedPlan)) {
            header('Location: /checkout?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle));
        } else {
            header('Location: /dashboard');
        }
        exit;
    }

    /**
     * Show registration form
     */
    public function registerForm(): void
    {
        $selectedPlan = !empty($_GET['plan']) ? strtolower(trim($_GET['plan'])) : null;
        $selectedCycle = strtolower(trim($_GET['cycle'] ?? 'monthly')) === 'annual' ? 'annual' : 'monthly';

        if (Auth::check()) {
            if ($selectedPlan) {
                header('Location: /checkout?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle));
            } else {
                header('Location: /dashboard');
            }
            exit;
        }

        $error = Session::getFlash('error');
        $success = Session::getFlash('success');
        $selectedRole = $_GET['role'] ?? 'client';

        $this->render('auth/register', [
            'title' => 'Create Account | Kinetica Warsaw',
            'error' => $error,
            'success' => $success,
            'selectedRole' => $selectedRole,
            'selectedPlan' => $selectedPlan,
            'selectedCycle' => $selectedCycle
        ], 'layouts/auth');
    }

    /**
     * Handle user registration
     */
    public function register(): void
    {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = 'client';
        $fitnessGoal = trim($_POST['fitness_goal'] ?? 'Biomechanics & Hypertrophy');
        $preferredLang = trim($_POST['preferred_language'] ?? 'en');
        $selectedPlan = !empty($_POST['selected_plan']) ? strtolower(trim($_POST['selected_plan'])) : null;
        $selectedCycle = strtolower(trim($_POST['selected_cycle'] ?? 'monthly')) === 'annual' ? 'annual' : 'monthly';

        $registerUrl = '/register' . ($selectedPlan ? '?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle) : '');
        $loginUrl = '/login' . ($selectedPlan ? '?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle) : '');

        if (empty($fullName) || empty($email) || empty($password)) {
            Session::setFlash('error', 'Full name, email, and password are required.');
            header('Location: ' . $registerUrl);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Please provide a valid email address.');
            header('Location: ' . $registerUrl);
            exit;
        }

        if (strlen($password) < 6) {
            Session::setFlash('error', 'Password must be at least 6 characters long.');
            header('Location: ' . $registerUrl);
            exit;
        }

        // Check if user already exists
        $existing = User::findByEmail($email);
        if ($existing && !empty($existing['password_hash'])) {
            Session::setFlash('error', 'An account with this email already exists. Please sign in to activate your pass.');
            header('Location: ' . $loginUrl);
            exit;
        }

        // Create new account
        $userId = User::create([
            'full_name'          => $fullName,
            'email'              => $email,
            'phone'              => $phone,
            'password'           => $password,
            'role'               => 'client',
            'fitness_goal'       => $fitnessGoal,
            'preferred_language' => $preferredLang,
            'lead_source'        => $selectedPlan ? 'pass_selection_' . $selectedPlan : 'direct_registration',
            'plan_code'          => $selectedPlan ?: null
        ]);

        $newUser = [
            'id'                  => $userId,
            'full_name'           => $fullName,
            'email'               => $email,
            'phone'               => $phone,
            'role'                => 'client',
            'lead_status'         => 'converted',
            'fitness_goal'        => $fitnessGoal,
            'preferred_language'  => $preferredLang,
            'free_courses_unlocked' => true
        ];

        Auth::login($newUser);

        if (!empty($selectedPlan)) {
            Session::setFlash('success', 'Account created! Complete pass activation below.');
            header('Location: /checkout?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle));
        } else {
            Session::setFlash('success', 'Your Kinetica account has been successfully created!');
            header('Location: /dashboard');
        }
        exit;
    }

    /**
     * Log out current user
     */
    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}
