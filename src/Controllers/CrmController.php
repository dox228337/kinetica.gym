<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Session;
use App\Models\Crm;
use App\Models\User;

class CrmController extends Controller
{
    /**
     * Check staff access permissions
     */
    private function checkAccess(): void
    {
        if (!Auth::check()) {
            Session::setFlash('error', 'Please sign in with staff or admin credentials to access Kinetica CRM.');
            header('Location: /login');
            exit;
        }

        // Only staff, trainers, and admins may access the CRM
        $role = Auth::role();
        if (!in_array($role, ['employee', 'trainer', 'admin'], true)) {
            Session::setFlash('error', 'Access denied. Staff or admin credentials are required to view the CRM.');
            header('Location: /dashboard');
            exit;
        }
    }

    /**
     * 1. Executive Analytics & Overview Dashboard (/crm)
     */
    public function index(): void
    {
        $this->checkAccess();

        $stats = Crm::getExecutiveStats();
        $recentLogs = Crm::getTurnstileLogs(6);
        $leads = Crm::getLeads();
        $user = Auth::user();

        $this->render('crm/index', [
            'title' => 'CRM Executive Overview | Kinetica Warsaw',
            'stats' => $stats,
            'recentLogs' => $recentLogs,
            'leads' => $leads,
            'user' => $user,
            'currentNav' => 'overview'
        ], 'layouts/crm');
    }

    /**
     * 2. Sales Pipeline & Leads Kanban Board (/crm/leads)
     */
    public function leads(): void
    {
        $this->checkAccess();

        $kanbanLeads = Crm::getLeads();
        $user = Auth::user();

        $this->render('crm/leads', [
            'title' => 'Sales Pipeline Kanban | Kinetica CRM',
            'kanban' => $kanbanLeads,
            'user' => $user,
            'currentNav' => 'leads'
        ], 'layouts/crm');
    }

    /**
     * Handle updating lead status and notes (AJAX or POST)
     */
    public function updateLeadStatus(): void
    {
        $this->checkAccess();

        $leadId = (int)($_POST['lead_id'] ?? 0);
        $newStatus = trim($_POST['lead_status'] ?? 'new');
        $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : null;
        $followUp = !empty($_POST['follow_up_date']) ? trim($_POST['follow_up_date']) : null;

        if ($leadId > 0) {
            Crm::updateLeadStatus($leadId, $newStatus, $notes, $followUp);
            Session::setFlash('success', 'Lead #' . $leadId . ' successfully updated in pipeline!');
        }

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Status updated']);
            return;
        }

        header('Location: /crm/leads');
        exit;
    }

    /**
     * Handle manual lead creation (From Reception or Inbound Call)
     */
    public function createLead(): void
    {
        $this->checkAccess();

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $source = trim($_POST['lead_source'] ?? 'reception_walk_in');
        $goal = trim($_POST['fitness_goal'] ?? 'Biomechanics & Health');
        $lang = trim($_POST['preferred_language'] ?? 'pl');
        $notes = trim($_POST['notes'] ?? '');

        if (!empty($fullName)) {
            $newId = Crm::createLead([
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'lead_source' => $source,
                'fitness_goal' => $goal,
                'preferred_language' => $lang,
                'notes' => $notes,
                'lead_status' => 'new'
            ]);
            Session::setFlash('success', 'Prospect ' . htmlspecialchars($fullName) . ' added to Sales Funnel (ID #' . $newId . ')!');
        } else {
            Session::setFlash('error', 'Full name is required to create a prospect.');
        }

        header('Location: /crm/leads');
        exit;
    }

    /**
     * 3. Athletes & Clients 360° Directory (/crm/athletes)
     */
    public function athletes(): void
    {
        $this->checkAccess();

        $search = trim($_GET['search'] ?? '');
        $filterStatus = trim($_GET['status'] ?? 'all');

        $athletes = Crm::getAthletes($search, $filterStatus);
        $user = Auth::user();

        $this->render('crm/athletes', [
            'title' => 'Athlete 360° Directory | Kinetica CRM',
            'athletes' => $athletes,
            'search' => $search,
            'filterStatus' => $filterStatus,
            'user' => $user,
            'currentNav' => 'athletes'
        ], 'layouts/crm');
    }

    /**
     * Handle Athlete Membership Actions (Freeze, Extend, Add Cryo)
     */
    public function athleteAction(): void
    {
        $this->checkAccess();

        $athleteId = (int)($_POST['athlete_id'] ?? 0);
        $action = trim($_POST['action'] ?? '');

        if ($athleteId > 0 && !empty($action)) {
            Crm::athleteAction($athleteId, $action);
            Session::setFlash('success', 'Action [' . $action . '] executed for Athlete #' . $athleteId);
        }

        header('Location: /crm/athletes');
        exit;
    }

    /**
     * 4. Access Control & Turnstile Telemetry (/crm/turnstiles)
     */
    public function turnstiles(): void
    {
        $this->checkAccess();

        $logs = Crm::getTurnstileLogs(25);
        $stats = Crm::getExecutiveStats();
        $user = Auth::user();

        $this->render('crm/turnstiles', [
            'title' => 'Turnstiles & Biometric Access | Kinetica CRM',
            'logs' => $logs,
            'stats' => $stats,
            'user' => $user,
            'currentNav' => 'turnstiles'
        ], 'layouts/crm');
    }

    /**
     * Handle Turnstile Biometric Scan Simulation
     */
    public function simulateScan(): void
    {
        $this->checkAccess();

        $athleteName = trim($_POST['athlete_name'] ?? 'Alex Morgan');
        $method = trim($_POST['method'] ?? 'nfc');
        $gate = trim($_POST['gate'] ?? 'Warsaw Main Gate - Turnstile 01');

        $result = Crm::simulateScan($athleteName, $method, $gate);
        Session::setFlash('success', 'Biometric scan simulated: ' . $athleteName . ' at ' . $gate);

        if ($this->isAjax()) {
            $this->json(['success' => true, 'scan' => $result]);
            return;
        }

        header('Location: /crm/turnstiles');
        exit;
    }

    /**
     * 5. Coaching & Equipment Schedule (/crm/schedule)
     */
    public function schedule(): void
    {
        $this->checkAccess();

        $schedule = Crm::getSchedule();
        $user = Auth::user();

        $this->render('crm/schedule', [
            'title' => 'Training & Equipment Schedule | Kinetica CRM',
            'schedule' => $schedule,
            'user' => $user,
            'currentNav' => 'schedule'
        ], 'layouts/crm');
    }

    /**
     * Handle booking athlete into schedule session
     */
    public function bookSession(): void
    {
        $this->checkAccess();

        $sessionId = (int)($_POST['session_id'] ?? 0);
        $athleteName = trim($_POST['athlete_name'] ?? '');

        if ($sessionId > 0 && !empty($athleteName)) {
            Crm::bookSession($sessionId, $athleteName);
            Session::setFlash('success', 'Booked ' . htmlspecialchars($athleteName) . ' into session #' . $sessionId);
        }

        header('Location: /crm/schedule');
        exit;
    }

    /**
     * 6. Coach Payroll & Accounting Automation (/crm/payroll)
     */
    public function payroll(): void
    {
        $this->checkAccess();

        $records = Crm::getPayrollData();
        $stats = Crm::getExecutiveStats();
        $user = Auth::user();

        $totalPayout = 0;
        foreach ($records as $r) {
            $totalPayout += (float)($r['total_gross'] ?? 0);
        }

        $this->render('crm/payroll', [
            'title' => 'Coach Payroll & Accounting | Kinetica CRM',
            'records' => $records,
            'totalPayout' => $totalPayout,
            'stats' => $stats,
            'user' => $user,
            'currentNav' => 'payroll'
        ], 'layouts/crm');
    }

    /**
     * Handle Payroll Approval
     */
    public function approvePayroll(): void
    {
        $this->checkAccess();
        $this->requireAdmin();

        $employeeId = (int)($_POST['employee_id'] ?? 0);
        if ($employeeId > 0) {
            Crm::approvePayroll($employeeId);
            Session::setFlash('success', 'Payroll statement for Coach #' . $employeeId . ' approved for bank disbursement.');
        }

        header('Location: /crm/payroll');
        exit;
    }

    /**
     * Handle Payroll Disbursement / Paid
     */
    public function markPayrollPaid(): void
    {
        $this->checkAccess();
        $this->requireAdmin();

        $employeeId = (int)($_POST['employee_id'] ?? 0);
        if ($employeeId > 0) {
            Crm::markPayrollPaid($employeeId);
            Session::setFlash('success', 'Coach #' . $employeeId . ' payout marked as paid via SEPA transfer.');
        }

        header('Location: /crm/payroll');
        exit;
    }

    /**
     * Require admin role for sensitive financial operations
     */
    private function requireAdmin(): void
    {
        if (Auth::role() !== 'admin') {
            Session::setFlash('error', 'Only administrators can perform payroll operations.');
            header('Location: /crm/payroll');
            exit;
        }
    }

    /**
     * Helper to detect AJAX requests
     */
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
