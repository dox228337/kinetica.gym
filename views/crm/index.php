<!-- Executive Overview Dashboard -->

<!-- KPI Metric Cards Grid -->
<div class="crm-stats-grid">
    <div class="crm-stat-card accent-lime">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Monthly Recurring Revenue</span>
            <span class="crm-stat-icon">💰</span>
        </div>
        <div class="crm-stat-value"><?= number_format($stats['mrr'], 0, '.', ' ') ?> <span style="font-size: 1.1rem; color: var(--crm-lime);">PLN</span></div>
        <div class="crm-stat-subtext">
            <span class="crm-trend-up">↑ +14.2%</span> vs previous month
        </div>
    </div>

    <div class="crm-stat-card accent-cyan">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Active Club Members</span>
            <span class="crm-stat-icon">🏋️</span>
        </div>
        <div class="crm-stat-value"><?= e($stats['active_members']) ?></div>
        <div class="crm-stat-subtext">
            <span>Standart, Premium & Max tiers</span>
        </div>
    </div>

    <div class="crm-stat-card accent-emerald">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Warsaw Club Occupancy</span>
            <span class="crm-stat-icon">📍</span>
        </div>
        <div class="crm-stat-value"><?= e($stats['occupancy_current']) ?> <span style="font-size: 1rem; color: #94a3b8;">/ <?= e($stats['occupancy_max']) ?></span></div>
        <div class="crm-stat-subtext">
            <span class="crm-trend-up">● <?= e($stats['occupancy_percentage']) ?>% capacity</span> • Złota 44
        </div>
    </div>

    <div class="crm-stat-card accent-amber">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Leads Funnel & Conversion</span>
            <span class="crm-stat-icon">🎯</span>
        </div>
        <div class="crm-stat-value"><?= e($stats['conversion_rate']) ?>%</div>
        <div class="crm-stat-subtext">
            <span><?= e($stats['leads_count']) ?> prospects in pipeline</span>
        </div>
    </div>
</div>

<!-- 2 Columns Grid: Membership Distribution & Live Turnstile Feed -->
<div class="crm-grid-2">
    <!-- Panel 1: Club Revenue Distribution by Pass Tier -->
    <div class="crm-panel-card">
        <div class="crm-panel-header">
            <div>
                <h3>📊 Membership Tiers Distribution</h3>
                <p class="crm-panel-subtitle">Revenue breakdown across the 4 club passes</p>
            </div>
            <a href="/crm/athletes" class="crm-btn crm-btn-secondary crm-btn-sm">View Roster →</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px; margin-top: 10px;">
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.82rem;">
                    <span>🔥 <strong>Premium Pass</strong> (319 PLN/mo)</span>
                    <strong style="color: var(--crm-lime);">168 members (53,592 PLN)</strong>
                </div>
                <div style="background: rgba(255,255,255,0.06); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div style="background: var(--crm-lime); width: 62%; height: 100%;"></div>
                </div>
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.82rem;">
                    <span>👑 <strong>Max Pass (VIP All-Inclusive)</strong> (499 PLN/mo)</span>
                    <strong style="color: var(--crm-cyan);">44 members (21,956 PLN)</strong>
                </div>
                <div style="background: rgba(255,255,255,0.06); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div style="background: var(--crm-cyan); width: 25%; height: 100%;"></div>
                </div>
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.82rem;">
                    <span>⚡ <strong>StandartPlus Pass</strong> (239 PLN/mo)</span>
                    <strong style="color: #cbd5e1;">78 members (18,642 PLN)</strong>
                </div>
                <div style="background: rgba(255,255,255,0.06); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div style="background: #94a3b8; width: 22%; height: 100%;"></div>
                </div>
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.82rem;">
                    <span>🛡️ <strong>Standart Pass</strong> (179 PLN/mo)</span>
                    <strong style="color: #cbd5e1;">52 members (9,308 PLN)</strong>
                </div>
                <div style="background: rgba(255,255,255,0.06); height: 8px; border-radius: 4px; overflow: hidden;">
                    <div style="background: #64748b; width: 11%; height: 100%;"></div>
                </div>
            </div>
        </div>

        <div style="margin-top: 22px; padding-top: 14px; border-top: 1px solid var(--crm-border-subtle); display: flex; justify-content: space-between; font-size: 0.8rem; color: #94a3b8;">
            <span>Free Community Tier: <strong>140 registered accounts</strong></span>
            <span>Avg Revenue Per Athlete: <strong>252 PLN/mo</strong></span>
        </div>
    </div>

    <!-- Panel 2: Live Turnstile Scans Feed (Telemetry) -->
    <div class="crm-panel-card">
        <div class="crm-panel-header">
            <div>
                <h3>🚪 Real-Time Turnstile Telemetry</h3>
                <p class="crm-panel-subtitle">Live biometric & NFC scans at Warsaw gates</p>
            </div>
            <a href="/crm/turnstiles" class="crm-btn crm-btn-secondary crm-btn-sm">Telemetry Control →</a>
        </div>

        <div class="crm-table-wrapper">
            <table class="crm-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Athlete</th>
                        <th>Gate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentLogs)): ?>
                        <?php foreach (array_slice($recentLogs, 0, 5) as $log): ?>
                            <tr>
                                <td style="font-family: var(--font-mono); font-size: 0.75rem;"><?= e($log['time']) ?></td>
                                <td><strong><?= e($log['athlete_name']) ?></strong></td>
                                <td style="font-size: 0.78rem; color: #94a3b8;"><?= e($log['gate']) ?></td>
                                <td>
                                    <span class="crm-status-pill status-<?= e($log['status_badge']) ?>">
                                        <?= e($log['direction']) ?>: <?= e($log['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bottom Grid: Quick Actions & Churn Alert Callouts -->
<div class="crm-grid-3">
    <!-- Card 1: Sales Leads Kanban CTA -->
    <div class="crm-panel-card" style="border-left: 3px solid var(--crm-lime);">
        <h3>🎯 Sales Pipeline Kanban</h3>
        <p style="font-size: 0.8rem; color: #94a3b8; margin: 8px 0 16px;">
            58 leads waiting in pipeline. Move leads from <em>New</em> to <em>Trial Scheduled</em> to <em>Converted</em>.
        </p>
        <a href="/crm/leads" class="crm-btn crm-btn-primary btn-block">
            <span>Open Kanban Board (5 Stages) →</span>
        </a>
    </div>

    <!-- Card 2: Churn Alert & Retention CTA -->
    <div class="crm-panel-card" style="border-left: 3px solid var(--crm-rose);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <h3>⚠️ Retention Risk Alerts</h3>
            <span class="crm-churn-alert-badge">6 ATHLETES</span>
        </div>
        <p style="font-size: 0.8rem; color: #94a3b8; margin: 8px 0 16px;">
            6 paying members haven't passed Warsaw turnstiles in over 10 days. Automated proactive coach calls suggested.
        </p>
        <a href="/crm/athletes?status=active" class="crm-btn crm-btn-secondary btn-block">
            <span>Review Inactive Athletes →</span>
        </a>
    </div>

    <!-- Card 3: Coach Payroll 10th-of-Month Schedule -->
    <div class="crm-panel-card" style="border-left: 3px solid var(--crm-cyan);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <h3>💰 Coach Payroll Automation</h3>
            <span class="crm-status-pill status-calculated">10th Due</span>
        </div>
        <p style="font-size: 0.8rem; color: #94a3b8; margin: 8px 0 16px;">
            Hybrid payroll statements ready for review: Base Salary + PT Session commissions for Accountant.
        </p>
        <a href="/crm/payroll" class="crm-btn crm-btn-secondary btn-block">
            <span>Open Payroll Statement (31,380 PLN) →</span>
        </a>
    </div>
</div>
