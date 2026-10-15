<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Kinetica Gym CRM | Warsaw Administration') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Core & CRM Styles -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/crm.css">
</head>
<body class="crm-body">

    <div class="crm-layout">
        <!-- Sidebar Navigation -->
        <aside class="crm-sidebar" id="crmSidebar">
            <div class="crm-sidebar-brand">
                <span class="crm-brand-icon">⚡</span>
                <span class="crm-brand-name">KINETICA</span>
                <span class="crm-brand-tag">CRM</span>
            </div>

            <nav class="crm-sidebar-nav">
                <div class="crm-nav-section-title">Operations & Growth</div>
                
                <a href="/crm" class="crm-nav-link <?= ($currentNav ?? '') === 'overview' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span>Executive Overview</span>
                </a>

                <a href="/crm/leads" class="crm-nav-link <?= ($currentNav ?? '') === 'leads' ? 'active' : '' ?>">
                    <span class="nav-icon">🎯</span>
                    <span>Sales Funnel & Leads</span>
                    <span class="crm-nav-badge">Kanban</span>
                </a>

                <a href="/crm/athletes" class="crm-nav-link <?= ($currentNav ?? '') === 'athletes' ? 'active' : '' ?>">
                    <span class="nav-icon">👥</span>
                    <span>Athletes Directory</span>
                    <span class="crm-nav-badge">360°</span>
                </a>

                <div class="crm-nav-section-title">Facility & Access</div>

                <a href="/crm/turnstiles" class="crm-nav-link <?= ($currentNav ?? '') === 'turnstiles' ? 'active' : '' ?>">
                    <span class="nav-icon">🚪</span>
                    <span>Turnstiles Telemetry</span>
                    <span class="crm-nav-badge">Live</span>
                </a>

                <a href="/crm/schedule" class="crm-nav-link <?= ($currentNav ?? '') === 'schedule' ? 'active' : '' ?>">
                    <span class="nav-icon">📅</span>
                    <span>Coaching Schedule</span>
                </a>

                <div class="crm-nav-section-title">Finance & Staff</div>

                <a href="/crm/payroll" class="crm-nav-link <?= ($currentNav ?? '') === 'payroll' ? 'active' : '' ?>">
                    <span class="nav-icon">💰</span>
                    <span>Coach Payroll & ZUS</span>
                    <span class="crm-nav-badge">10th Due</span>
                </a>
            </nav>

            <div class="crm-sidebar-footer">
                <div class="crm-user-profile-badge">
                    <div class="crm-avatar">
                        <?= strtoupper(substr($user['full_name'] ?? 'M', 0, 1)) ?>
                    </div>
                    <div class="crm-user-info">
                        <span class="crm-user-name"><?= e($user['full_name'] ?? 'Staff Member') ?></span>
                        <span class="crm-user-role"><?= e($user['role'] ?? 'Staff') ?> • Warsaw</span>
                    </div>
                </div>
                <div class="crm-footer-links">
                    <a href="/en" class="crm-footer-link" target="_blank">🌐 Client Website</a>
                    <a href="/dashboard" class="crm-footer-link">🏋️ Member Portal</a>
                    <a href="/logout" class="crm-footer-link">🚪 Exit</a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="crm-main">
            <!-- Topbar -->
            <header class="crm-topbar">
                <div class="crm-topbar-left">
                    <button type="button" class="crm-menu-toggle" id="crmMenuToggle" aria-label="Toggle menu">☰</button>
                    <div class="crm-page-title-box">
                        <span class="crm-breadcrumb">Kinetica Club Warsaw • ul. Złota 44</span>
                        <h1><?= e($title ?? 'CRM Dashboard') ?></h1>
                    </div>
                </div>

                <div class="crm-topbar-right">
                    <div class="crm-live-gate-pill">
                        <span class="live-pulse-dot"></span>
                        <span>TURNSTILES ONLINE • 24/7 BIOMETRICS</span>
                    </div>

                    <a href="/crm/leads" class="crm-btn crm-btn-primary crm-btn-sm">
                        <span>+ Quick Add Lead</span>
                    </a>
                </div>
            </header>

            <!-- Alerts -->
            <?php if ($flashSuccess = \App\Core\Session::getFlash('success')): ?>
                <div class="alert alert-success" style="margin: 20px 28px 0;">
                    <span class="alert-icon">✓</span>
                    <span><?= e($flashSuccess) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($flashError = \App\Core\Session::getFlash('error')): ?>
                <div class="alert alert-error" style="margin: 20px 28px 0;">
                    <span class="alert-icon">⚠️</span>
                    <span><?= e($flashError) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($flashInfo = \App\Core\Session::getFlash('info')): ?>
                <div class="alert alert-info" style="margin: 20px 28px 0; background: rgba(0, 240, 255, 0.1); border-color: rgba(0, 240, 255, 0.3); color: #38bdf8;">
                    <span class="alert-icon">ℹ️</span>
                    <span><?= e($flashInfo) ?></span>
                </div>
            <?php endif; ?>

            <!-- Page Body Content -->
            <main class="crm-content">
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Toggle sidebar script for mobile/tablet -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('crmMenuToggle');
        const sidebar = document.getElementById('crmSidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }
    });
    </script>
</body>
</html>
