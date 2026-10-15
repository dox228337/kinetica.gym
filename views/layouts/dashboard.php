<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Kinetica Member Portal') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Core & Dashboard Styles -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-layout">
        <!-- Sidebar Navigation -->
        <aside class="dashboard-sidebar" id="dashboardSidebar">
            <div class="sidebar-header">
                <a href="/en" class="brand-logo" aria-label="Kinetica Home">
                    <span class="brand-icon">⚡</span>
                    <span class="brand-text">KINETICA<span class="brand-dot">.</span></span>
                </a>
                <span class="portal-tag"><?= \App\Core\Auth::isEmployee() ? 'COACH HUB' : 'MEMBER' ?></span>
            </div>

            <!-- Mini User Card in Sidebar -->
            <div class="sidebar-user-card">
                <div class="sidebar-avatar">
                    <?= mb_substr($user['full_name'] ?? 'U', 0, 1) ?>
                </div>
                <div class="sidebar-user-info">
                    <strong class="sidebar-user-name"><?= e($user['full_name'] ?? 'Athlete') ?></strong>
                    <span class="sidebar-role-badge <?= \App\Core\Auth::isEmployee() ? 'badge-coach' : 'badge-client' ?>">
                        <?= \App\Core\Auth::isEmployee() ? '👨‍🏫 Gym Staff / Coach' : '🏋️ Club Athlete' ?>
                    </span>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="sidebar-nav">
                <span class="nav-section-title">PORTAL MENU</span>
                
                <a href="/dashboard" class="sidebar-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '' ?>">
                    <span class="sidebar-link-icon">📊</span>
                    <span>Overview & Pass</span>
                </a>

                <?php if (!\App\Core\Auth::isEmployee()): ?>
                    <a href="/dashboard/memberships" class="sidebar-link <?= $_SERVER['REQUEST_URI'] === '/dashboard/memberships' ? 'active' : '' ?>">
                        <span class="sidebar-link-icon">💳</span>
                        <span>My Membership</span>
                    </a>
                    <a href="/dashboard/schedule" class="sidebar-link <?= $_SERVER['REQUEST_URI'] === '/dashboard/schedule' ? 'active' : '' ?>">
                        <span class="sidebar-link-icon">📅</span>
                        <span>Class Schedule</span>
                    </a>
                <?php else: ?>
                    <a href="/dashboard#schedule" class="sidebar-link">
                        <span class="sidebar-link-icon">📋</span>
                        <span>Today's Sessions</span>
                    </a>
                    <a href="/dashboard#clients" class="sidebar-link">
                        <span class="sidebar-link-icon">👥</span>
                        <span>My Athletes Roster</span>
                    </a>
                    <a href="/dashboard#payroll" class="sidebar-link">
                        <span class="sidebar-link-icon">💰</span>
                        <span>Salary & Payouts</span>
                    </a>
                <?php endif; ?>

                <a href="/dashboard/profile" class="sidebar-link <?= $_SERVER['REQUEST_URI'] === '/dashboard/profile' ? 'active' : '' ?>">
                    <span class="sidebar-link-icon">⚙️</span>
                    <span>Account Settings</span>
                </a>

                <?php if (\App\Core\Auth::isEmployee()): ?>
                    <span class="nav-section-title">STAFF & ADMIN</span>
                    <a href="/crm" class="sidebar-link" style="color: #d4ff00; font-weight: 600;">
                        <span class="sidebar-link-icon">⚡</span>
                        <span>Open Staff CRM →</span>
                    </a>
                <?php endif; ?>

                <span class="nav-section-title">QUICK ACCESS</span>
                <a href="/<?= ($_SESSION['lang'] ?? 'en') === 'pl' ? 'pl' : 'en' ?>" class="sidebar-link external-link" target="_blank">
                    <span class="sidebar-link-icon">🌐</span>
                    <span>Club Website →</span>
                </a>
                <a href="/logout" class="sidebar-link logout-link">
                    <span class="sidebar-link-icon">🚪</span>
                    <span>Sign Out</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <small>Warsaw, Złota 44 • 24/7 Access</small>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="dashboard-main-wrapper">
            <!-- Top Dashboard Bar -->
            <header class="dashboard-topbar">
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="Toggle menu">☰</button>
                
                <div class="topbar-location">
                    <span class="pulse-indicator"></span>
                    <span>Warsaw Flagship Turnstile • <strong>24/7 ONLINE</strong></span>
                </div>

                <div class="topbar-actions">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="showNfcModal()">
                        <span>📲 Show Digital Pass</span>
                    </button>
                    <a href="/logout" class="btn btn-outline btn-sm logout-btn-top">Sign Out</a>
                </div>
            </header>

            <!-- Main Page Body -->
            <main class="dashboard-content">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <span class="alert-icon">✓</span>
                        <span><?= e($success) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">⚠️</span>
                        <span><?= e($error) ?></span>
                    </div>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Digital NFC Warsaw Turnstile Pass Modal -->
    <div class="modal-overlay" id="nfcModal" aria-hidden="true">
        <div class="modal-dialog text-center" role="dialog" aria-modal="true">
            <button type="button" class="modal-close-btn" onclick="closeNfcModal()">&times;</button>
            <div class="modal-body">
                <div class="modal-badge">WARSAW TURNSTILE SMART PASS</div>
                <h3 class="modal-title"><?= e($user['full_name'] ?? 'Athlete') ?></h3>
                <p class="modal-subtitle">Hold phone close to Kinetica NFC reader at ul. Złota 44</p>
                
                <!-- Digital QR / NFC Card Simulation -->
                <div class="nfc-card-visual">
                    <div class="nfc-card-top">
                        <span>⚡ KINETICA WARSAW</span>
                        <span class="nfc-badge-role"><?= \App\Core\Auth::isEmployee() ? 'STAFF / COACH' : 'MEMBER' ?></span>
                    </div>
                    <div class="nfc-chip-icon">📶</div>
                    <div class="nfc-code-display"><?= e($user['nfc_token'] ?? 'NFC-849201') ?></div>
                    <div class="nfc-card-bottom">
                        <small>VALID: 24/7 ENTRY</small>
                        <small>GATE A & B</small>
                    </div>
                </div>

                <div class="nfc-status-note">
                    ✓ Biometric recognition synced with Apple Wallet / Google Wallet
                </div>

                <button type="button" class="btn btn-primary btn-block" onclick="closeNfcModal()">
                    Done, Close Pass
                </button>
            </div>
        </div>
    </div>

    <script>
    function showNfcModal() {
        document.getElementById('nfcModal').classList.add('open');
    }
    function closeNfcModal() {
        document.getElementById('nfcModal').classList.remove('open');
    }
    document.getElementById('sidebarToggleBtn')?.addEventListener('click', () => {
        document.getElementById('dashboardSidebar').classList.toggle('open');
    });
    </script>
</body>
</html>
