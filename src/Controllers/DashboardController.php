<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Enforce authentication for all dashboard routes
     */
    private function requireAuth(): array
    {
        if (!Auth::check()) {
            Session::setFlash('error', 'Please sign in to access your personal dashboard.');
            header('Location: /login');
            exit;
        }
        return Auth::user();
    }

    /**
     * Main Dashboard router: dispatches to client or trainer dashboard
     */
    public function index(): void
    {
        $user = $this->requireAuth();
        $success = Session::getFlash('success');
        $error = Session::getFlash('error');

        // Check if employee or coach
        if (Auth::isEmployee()) {
            $employee = User::getEmployeeDetails($user['id']);
            $schedule = User::getTrainerSchedule($employee['id'] ?? 1);
            $clients = User::getTrainerClients($employee['id'] ?? 1);

            $this->render('dashboard/trainer', [
                'title' => 'Coach & Staff Portal | Kinetica Warsaw',
                'user' => $user,
                'employee' => $employee,
                'schedule' => $schedule,
                'clients' => $clients,
                'success' => $success,
                'error' => $error
            ], 'layouts/dashboard');
            return;
        }

        // Standard client / member dashboard
        $membership = User::getMembership($user['id']);
        $visits = User::getVisits($user['id'], 5);

        $this->render('dashboard/index', [
            'title' => 'Member Portal | Kinetica Warsaw',
            'user' => $user,
            'membership' => $membership,
            'visits' => $visits,
            'success' => $success,
            'error' => $error
        ], 'layouts/dashboard');
    }

    /**
     * Membership details and upgrade page
     */
    public function memberships(): void
    {
        $user = $this->requireAuth();
        $membership = User::getMembership($user['id']);

        $content = require __DIR__ . '/../../config/landing_content.php';
        $plans = $content['en']['pricing_section']['plans'];

        $this->render('dashboard/memberships', [
            'title' => 'My Membership & Plans | Kinetica',
            'user' => $user,
            'membership' => $membership,
            'plans' => $plans
        ], 'layouts/dashboard');
    }

    /**
     * Workout schedule and class booking page
     */
    public function schedule(): void
    {
        $user = $this->requireAuth();

        $classes = [
            [
                'id' => 1,
                'time' => 'Today, 18:00',
                'title' => 'HYROX Athletic Conditioning',
                'coach' => 'Marek Kowalski (Olympic Specialist)',
                'zone' => 'Endurance & Hyrox Arena',
                'spots_left' => 4,
                'category' => 'Cardio & Strength'
            ],
            [
                'id' => 2,
                'time' => 'Tomorrow, 08:30',
                'title' => 'Mobility & Spine Decompression',
                'coach' => 'Kasia Dąbrowska (Lead Physio)',
                'zone' => 'Movement & Zen Suite',
                'spots_left' => 8,
                'category' => 'Recovery & Rehab'
            ],
            [
                'id' => 3,
                'time' => 'Tomorrow, 19:15',
                'title' => 'Biomechanical Heavy Iron Squat Workshop',
                'coach' => 'Dr. Elena Rostova',
                'zone' => 'Biomechanics Floor',
                'spots_left' => 2,
                'category' => 'Strength & Power'
            ],
            [
                'id' => 4,
                'time' => 'Thursday, 12:00',
                'title' => 'Power Cycle & Heart Rate HIIT',
                'coach' => 'Filip Nowak',
                'zone' => 'Cycle Theater',
                'spots_left' => 11,
                'category' => 'High Intensity'
            ]
        ];

        $userBookings = $_SESSION['user_bookings'][$user['id']] ?? [];
        foreach ($classes as &$c) {
            $c['is_booked'] = in_array($c['title'], $userBookings, true);
            if ($c['is_booked']) {
                $c['spots_left'] = max(0, $c['spots_left'] - 1);
            }
        }
        unset($c);

        $this->render('dashboard/schedule', [
            'title' => 'Class Schedule & Booking | Kinetica',
            'user' => $user,
            'classes' => $classes
        ], 'layouts/dashboard');
    }

    /**
     * User profile settings
     */
    public function profile(): void
    {
        $user = $this->requireAuth();
        $success = Session::getFlash('success');

        $this->render('dashboard/profile', [
            'title' => 'Account Settings | Kinetica',
            'user' => $user,
            'success' => $success
        ], 'layouts/dashboard');
    }

    /**
     * Freeze membership request action
     */
    public function freeze(): void
    {
        $user = $this->requireAuth();

        $membership = User::getMembership($user['id']);
        if (empty($membership['is_active_paid'])) {
            Session::setFlash('error', 'You need an active membership to use the freeze feature.');
            header('Location: /dashboard');
            exit;
        }

        if (($membership['status'] ?? '') === 'frozen') {
            Session::setFlash('error', 'Your membership pass is already currently frozen.');
            header('Location: /dashboard');
            exit;
        }

        $freezeRemaining = (int)($membership['freeze_days_remaining'] ?? 0);
        if ($freezeRemaining < 14) {
            Session::setFlash('error', 'Insufficient freeze days remaining (' . $freezeRemaining . ' left). Contact reception for assistance.');
            header('Location: /dashboard');
            exit;
        }

        // Freeze in database or session state
        User::freezeMembership($user['id'], 14);

        Session::setFlash('success', 'Your membership has been successfully frozen for 14 days. Your expiration date has been extended automatically.');
        header('Location: /dashboard');
        exit;
    }

    /**
     * Unfreeze membership early
     */
    public function unfreeze(): void
    {
        $user = $this->requireAuth();
        $membership = User::getMembership($user['id']);

        if (($membership['status'] ?? '') !== 'frozen') {
            Session::setFlash('info', 'Your membership is not currently frozen.');
            header('Location: /dashboard');
            exit;
        }

        User::unfreezeMembership($user['id']);
        Session::setFlash('success', 'Your membership pass has been unfrozen. 24/7 turnstile access is active immediately!');
        header('Location: /dashboard');
        exit;
    }

    /**
     * Class booking action
     */
    public function bookClass(): void
    {
        $user = $this->requireAuth();
        $classTitle = trim($_POST['class_title'] ?? 'Workout Session');

        if (!isset($_SESSION['user_bookings'][$user['id']])) {
            $_SESSION['user_bookings'][$user['id']] = [];
        }

        if (!in_array($classTitle, $_SESSION['user_bookings'][$user['id']], true)) {
            $_SESSION['user_bookings'][$user['id']][] = $classTitle;
            Session::setFlash('success', "Spot reserved for {$classTitle}! A confirmation pass has been added to your app.");
        } else {
            Session::setFlash('info', "You have already reserved a spot in {$classTitle}.");
        }

        header('Location: /dashboard/schedule');
        exit;
    }

    /**
     * Cancel class booking action
     */
    public function cancelClass(): void
    {
        $user = $this->requireAuth();
        $classTitle = trim($_POST['class_title'] ?? '');

        if (!empty($_SESSION['user_bookings'][$user['id']]) && in_array($classTitle, $_SESSION['user_bookings'][$user['id']], true)) {
            $_SESSION['user_bookings'][$user['id']] = array_values(array_diff($_SESSION['user_bookings'][$user['id']], [$classTitle]));
            Session::setFlash('success', "Reservation for {$classTitle} has been cancelled.");
        }

        header('Location: /dashboard/schedule');
        exit;
    }
}
