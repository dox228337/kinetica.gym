<?php

// Load local environment variables from .env file if it exists
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// Error reporting: only display detailed trace in non-production environments
$appEnv = getenv('APP_ENV') ?: 'local';
if ($appEnv === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// If running via PHP built-in web server, serve existing static files directly
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($filePath)) {
        return false;
    }
}

// PSR-4 Autoloader (App\ -> src/)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Require global helper functions
require_once __DIR__ . '/../src/Helpers/helpers.php';

use App\Core\Router;
use App\Core\Session;

// Start session before any headers or output are emitted
Session::start();

$router = new Router();

// Root route: Redirect to /en or detect Polish browser
$router->get('/', function () {
    $acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if (stripos($acceptLang, 'pl') !== false) {
        header('Location: /pl', true, 302);
    } else {
        header('Location: /en', true, 302);
    }
    exit;
});

// Landing Page routes (Separate SEO pages for EN and PL)
$router->get('/en', 'HomeController@en');
$router->get('/pl', 'HomeController@pl');

// Authentication Routes
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Personal Dashboard Routes (Role-based: Member vs Trainer)
$router->get('/dashboard', 'DashboardController@index');
$router->get('/dashboard/memberships', 'DashboardController@memberships');
$router->get('/dashboard/schedule', 'DashboardController@schedule');
$router->get('/dashboard/profile', 'DashboardController@profile');
$router->post('/dashboard/freeze', 'DashboardController@freeze');
$router->post('/dashboard/unfreeze', 'DashboardController@unfreeze');
$router->post('/dashboard/book-class', 'DashboardController@bookClass');
$router->post('/dashboard/cancel-class', 'DashboardController@cancelClass');

// Checkout & Payment Simulation Routes (Sandbox for Repository)
$router->get('/checkout', 'CheckoutController@index');
$router->post('/checkout/process', 'CheckoutController@process');

// CRM System Routes (Sales Funnel, Athlete 360, Turnstiles, Payroll, Schedule)
$router->get('/crm', 'CrmController@index');
$router->get('/crm/leads', 'CrmController@leads');
$router->post('/crm/leads/status', 'CrmController@updateLeadStatus');
$router->post('/crm/leads/create', 'CrmController@createLead');
$router->get('/crm/athletes', 'CrmController@athletes');
$router->post('/crm/athletes/action', 'CrmController@athleteAction');
$router->get('/crm/turnstiles', 'CrmController@turnstiles');
$router->post('/crm/turnstiles/simulate', 'CrmController@simulateScan');
$router->get('/crm/schedule', 'CrmController@schedule');
$router->post('/crm/schedule/book', 'CrmController@bookSession');
$router->get('/crm/payroll', 'CrmController@payroll');
$router->post('/crm/payroll/approve', 'CrmController@approvePayroll');
$router->post('/crm/payroll/paid', 'CrmController@markPayrollPaid');

// Lightweight AJAX API endpoints for front-end form interaction (CSRF exempt — use JSON content-type)
$router->post('/api/register-lead', 'HomeController@apiRegisterLead');
$router->post('/api/order-pass', 'HomeController@apiOrderPass');

// Global CSRF protection middleware for all POST form submissions
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($requestMethod === 'POST') {
    // Skip CSRF for JSON API endpoints and checkout process (uses JSON body)
    $csrfExemptPaths = ['/api/register-lead', '/api/order-pass', '/checkout/process'];
    $isExempt = in_array(rtrim($requestUri, '/') ?: '/', $csrfExemptPaths, true);

    if (!$isExempt) {
        $submittedToken = $_POST['_csrf_token'] ?? null;
        if (!Session::verifyCsrf($submittedToken)) {
            Session::setFlash('error', 'Security token expired or invalid. Please try again.');
            $referer = $_SERVER['HTTP_REFERER'] ?? '/en';
            header('Location: ' . $referer, true, 302);
            exit;
        }
    }
}

// Dispatch request
$router->dispatch();
