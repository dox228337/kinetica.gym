<!-- Coach Payroll & Accounting Automation -->

<div class="crm-panel-header" style="border: none; margin-bottom: 20px; padding: 0;">
    <div>
        <h2 style="font-size: 1.3rem;">💰 Coach Payroll & Accounting Automation (Rozliczenia)</h2>
        <p class="crm-panel-subtitle">Automated monthly calculation of base salaries, PT session commissions, and bank disbursement schedules for the club accountant.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="crm-btn crm-btn-secondary crm-btn-sm" onclick="alert('Exporting Polish Banking CSV (mBank / Santander Elixir-0 format)...');">
            <span>📥 Export Banking CSV</span>
        </button>
        <button type="button" class="crm-btn crm-btn-primary crm-btn-sm" onclick="alert('Faktura VAT summary generated. Sync ready for wfirma/Fakturownia.');">
            <span>🧾 Faktura VAT Sync</span>
        </button>
    </div>
</div>

<!-- Payroll Header Metrics Grid -->
<div class="crm-grid-3">
    <div class="crm-panel-card accent-lime" style="border-left: 3px solid var(--crm-lime);">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Total Monthly Outlay</span>
            <span class="crm-stat-icon">💰</span>
        </div>
        <div class="crm-stat-value" style="margin: 8px 0;"><?= number_format($totalPayout ?? 31380, 2) ?> <span style="font-size: 1rem; color: var(--crm-lime);">PLN</span></div>
        <span style="font-size: 0.75rem; color: #94a3b8;">Gross salary commitment for coaching staff</span>
    </div>

    <div class="crm-panel-card accent-cyan" style="border-left: 3px solid var(--crm-cyan);">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Disbursement Due Date</span>
            <span class="crm-status-pill status-calculated">10th OF MONTH</span>
        </div>
        <div class="crm-stat-value" style="margin: 8px 0; font-size: 1.4rem;">
            <?= e($stats['next_payout_date'] ?? '10.11.2026') ?>
        </div>
        <span style="font-size: 0.75rem; color: #94a3b8;">Automatic notification queued for Chief Accountant</span>
    </div>

    <div class="crm-panel-card accent-emerald" style="border-left: 3px solid var(--crm-emerald);">
        <div class="crm-stat-header">
            <span class="crm-stat-title">PT Commissions Accrued</span>
            <span class="crm-stat-icon">🏋️</span>
        </div>
        <div class="crm-stat-value" style="margin: 8px 0;">76 <span style="font-size: 1rem; color: #94a3b8;">Sessions</span></div>
        <span style="font-size: 0.75rem; color: #34d399;">7,930 PLN paid out in coach performance bonuses</span>
    </div>
</div>

<!-- Payroll Statements Table Card -->
<div class="crm-panel-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 18px 22px; border-bottom: 1px solid var(--crm-border-subtle); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1rem;">📋 Coaching Staff Payroll Statements</h3>
        <span style="font-size: 0.75rem; color: #64748b;">Period: <?= date('F Y') ?> (Warsaw Złota 44 Team)</span>
    </div>

    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Coach & Position</th>
                    <th>Model</th>
                    <th>Base Salary</th>
                    <th>PT Sessions & Rate</th>
                    <th>Bonuses</th>
                    <th>Total Gross (PLN)</th>
                    <th>Polish IBAN Account</th>
                    <th>Status</th>
                    <th style="text-align: right;">Accounting Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($records)): ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td>
                                <strong style="color: #fff; font-size: 0.92rem;"><?= e($r['name']) ?></strong>
                                <div style="font-size: 0.72rem; color: #64748b;"><?= e($r['position']) ?> • <?= e($r['department']) ?></div>
                            </td>

                            <td>
                                <span style="font-size: 0.75rem; text-transform: uppercase; font-family: var(--font-mono); color: #cbd5e1;">
                                    <?= e($r['salary_type']) ?>
                                </span>
                            </td>

                            <td style="font-family: var(--font-mono);">
                                <?= number_format($r['base_salary'], 2) ?> PLN
                            </td>

                            <td>
                                <?php if (($r['sessions_completed'] ?? 0) > 0): ?>
                                    <strong style="color: var(--crm-lime); font-family: var(--font-mono);"><?= e($r['sessions_completed']) ?></strong>
                                    <small style="color: #94a3b8;">@ <?= number_format($r['rate_per_session'], 0) ?> PLN</small>
                                    <div style="font-size: 0.72rem; color: #64748b;">+<?= number_format($r['commissions_amount'], 2) ?> PLN</div>
                                <?php else: ?>
                                    <span style="color: #64748b; font-size: 0.78rem;">— (Fixed)</span>
                                <?php endif; ?>
                            </td>

                            <td style="font-family: var(--font-mono); color: #34d399;">
                                +<?= number_format($r['bonus_amount'] ?? 0, 2) ?> PLN
                            </td>

                            <td>
                                <strong style="font-size: 0.95rem; font-family: var(--font-mono); color: #fff;">
                                    <?= number_format($r['total_gross'], 2) ?> PLN
                                </strong>
                                <div style="font-size: 0.7rem; color: #94a3b8;">Net: ~<?= number_format($r['total_net'], 2) ?> PLN</div>
                            </td>

                            <td>
                                <code style="font-size: 0.7rem; background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px; color: #94a3b8;">
                                    <?= e($r['iban']) ?>
                                </code>
                            </td>

                            <td>
                                <span class="crm-status-pill status-<?= e($r['status']) ?>">
                                    <?= e($r['status']) ?>
                                </span>
                            </td>

                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 6px;">
                                    <?php if ($r['status'] === 'calculated'): ?>
                                        <form action="/crm/payroll/approve" method="POST" style="margin: 0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="employee_id" value="<?= e($r['employee_id']) ?>">
                                            <button type="submit" class="crm-btn crm-btn-secondary crm-btn-sm" title="Approve for Accountant">
                                                ✓ Approve
                                            </button>
                                        </form>
                                    <?php elseif ($r['status'] === 'approved'): ?>
                                        <form action="/crm/payroll/paid" method="POST" style="margin: 0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="employee_id" value="<?= e($r['employee_id']) ?>">
                                            <button type="submit" class="crm-btn crm-btn-primary crm-btn-sm" title="Mark as Disbursed via Bank Transfer">
                                                💸 Mark Paid
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size: 0.75rem; color: var(--crm-lime); font-family: var(--font-mono);">Disbursed</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
