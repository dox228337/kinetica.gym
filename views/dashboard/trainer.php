<!-- Trainer & Staff Personal Portal -->
<div class="dashboard-header-area">
    <div class="header-titles">
        <span class="dashboard-tag badge-coach">👨‍🏫 COACH & STAFF WORKSPACE</span>
        <h1 class="dashboard-title">Welcome, Coach <?= e($employee['first_name'] ?? $user['full_name']) ?></h1>
        <p class="dashboard-desc">Position: <strong><?= e($employee['position_title'] ?? 'Lead Biomechanics Coach') ?></strong> • Department: Coaching & Biomechanics</p>
    </div>
    <div class="header-actions-group">
        <span class="duty-status-badge">
            <span class="duty-dot"></span>
            <span>ON DUTY (Checked In: 08:30)</span>
        </span>
        <a href="/crm" class="btn btn-primary btn-sm">
            <span>⚡ Open Club CRM →</span>
        </a>
    </div>
</div>

<!-- Financial & Automated Payroll Widget (Connected to employees & payroll_records) -->
<div class="trainer-payroll-card">
    <div class="payroll-card-header">
        <div>
            <span class="payroll-tag">💰 MONTHLY ACCRUAL TELEMETRY</span>
            <h3>Salary Calculation & Payout Schedule</h3>
        </div>
        <div class="payout-due-badge">
            <span>Payout Date: <strong>10th of every month</strong></span>
            <small>Status: Notification sent to Accountant</small>
        </div>
    </div>

    <div class="payroll-metrics-grid">
        <div class="payroll-metric-box">
            <span class="metric-label">Base Monthly Salary</span>
            <strong class="metric-value"><?= number_format($employee['base_salary'] ?? 6500, 2) ?> PLN</strong>
            <span class="metric-subtext">Fixed contract rate</span>
        </div>

        <div class="payroll-metric-box">
            <span class="metric-label">PT Sessions Completed</span>
            <strong class="metric-value"><?= e($employee['sessions_this_month'] ?? 32) ?> Sessions</strong>
            <span class="metric-subtext">+<?= number_format(($employee['sessions_this_month'] ?? 32) * ($employee['rate_per_session'] ?? 85), 2) ?> PLN (@ 85 PLN/session)</span>
        </div>

        <div class="payroll-metric-box highlighted-metric">
            <span class="metric-label">Total Accrued to Payout</span>
            <strong class="metric-value text-gradient"><?= number_format($employee['total_calculated_salary'] ?? 9220, 2) ?> PLN</strong>
            <span class="metric-subtext">Estimated Net: ~7,480 PLN</span>
        </div>

        <div class="payroll-metric-box">
            <span class="metric-label">Bank Transfer (IBAN)</span>
            <code class="metric-iban"><?= e($employee['iban_account'] ?? 'PL84 1050 1445 1000 0090 8124 5510') ?></code>
            <span class="metric-subtext">Verified for automatic SEPA transfer</span>
        </div>
    </div>
</div>

<!-- Two Columns Grid: Schedule and Clients Roster -->
<div class="dashboard-grid-2">
    <!-- Column 1: Today's Training Schedule -->
    <div class="dash-card" id="schedule">
        <div class="dash-card-header">
            <h3>📋 Today's Training Schedule (<?= date('l, M d') ?>)</h3>
            <span class="badge-free"><?= count($schedule) ?> SESSIONS</span>
        </div>
        <p class="dash-card-subtitle">Scheduled one-on-one biomechanics assessments and group classes.</p>

        <div class="trainer-schedule-list">
            <?php foreach ($schedule as $idx => $session): ?>
                <div class="schedule-item-card status-<?= $session['status'] ?>">
                    <div class="schedule-time-box">
                        <strong><?= e($session['time']) ?></strong>
                        <span class="schedule-zone"><?= e($session['zone']) ?></span>
                    </div>

                    <div class="schedule-client-info">
                        <strong class="client-name"><?= e($session['client_name']) ?></strong>
                        <span class="session-type"><?= e($session['type']) ?></span>
                        <small class="session-notes">📝 <?= e($session['notes']) ?></small>
                    </div>

                    <div class="schedule-action">
                        <?php if ($session['status'] === 'completed'): ?>
                            <span class="completed-check">✓ Done</span>
                        <?php elseif ($session['status'] === 'in_progress'): ?>
                            <span class="badge-pulse">In Progress</span>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm btn-outline" onclick="this.innerHTML='✓ Completed'; this.classList.add('btn-primary');">
                                Check In
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Column 2: My Athletes / Client Roster -->
    <div class="dash-card" id="clients">
        <div class="dash-card-header">
            <h3>👥 My Athletes & Clients Roster</h3>
            <span class="dash-status-dot"><?= count($clients) ?> Active Clients</span>
        </div>
        <p class="dash-card-subtitle">Athletes assigned to your biomechanics transformation protocols.</p>

        <div class="clients-roster-list">
            <?php foreach ($clients as $client): ?>
                <div class="client-roster-item">
                    <div class="client-avatar-circle">
                        <?= mb_substr($client['name'], 0, 1) ?>
                    </div>
                    <div class="client-roster-details">
                        <div class="client-row-title">
                            <strong><?= e($client['name']) ?></strong>
                            <span class="client-plan-pill"><?= e($client['plan']) ?></span>
                        </div>
                        <span class="client-goal-text">🎯 Goal: <?= e($client['goal']) ?></span>
                        <div class="client-progress-row">
                            <span>Completed: <strong><?= e($client['sessions_completed']) ?> sessions</strong></span>
                            <span class="progress-val">Progress: <?= e($client['progress']) ?></span>
                        </div>
                    </div>
                    <div class="client-contact-btn">
                        <a href="tel:<?= e($client['phone']) ?>" class="btn btn-sm btn-secondary" title="Call or WhatsApp">
                            💬 Contact
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="roster-footer-box">
            <button type="button" class="btn btn-outline btn-block btn-sm" onclick="alert('Workout progress logging modal opened.');">
                + Log Athlete Biomechanics Evaluation
            </button>
        </div>
    </div>
</div>
