<!-- Athletes & Members 360° Directory -->

<div class="crm-panel-header" style="border: none; margin-bottom: 20px; padding: 0;">
    <div>
        <h2 style="font-size: 1.3rem;">👥 Athlete 360° Directory & Retention Management</h2>
        <p class="crm-panel-subtitle">Search registered members, inspect biometric access credentials, and monitor churn-risk alerts.</p>
    </div>
</div>

<!-- Search & Filter Controls -->
<div class="crm-panel-card" style="margin-bottom: 22px; padding: 16px 20px;">
    <form action="/crm/athletes" method="GET" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: center; justify-content: space-between;">
        <div style="display: flex; gap: 10px; flex: 1; max-width: 500px;">
            <input type="text" name="search" class="form-input" placeholder="Search by athlete name, email, or phone..." value="<?= e($search ?? '') ?>" style="padding: 8px 14px; font-size: 0.85rem;">
            <button type="submit" class="crm-btn crm-btn-primary crm-btn-sm">Search</button>
            <?php if (!empty($search) || ($filterStatus ?? 'all') !== 'all'): ?>
                <a href="/crm/athletes" class="crm-btn crm-btn-secondary crm-btn-sm">Clear</a>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 8px; align-items: center; font-size: 0.8rem;">
            <span style="color: #64748b; font-weight: 600;">Status:</span>
            <a href="/crm/athletes?status=all<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="crm-btn <?= ($filterStatus ?? 'all') === 'all' ? 'crm-btn-primary' : 'crm-btn-secondary' ?> crm-btn-sm">All</a>
            <a href="/crm/athletes?status=active<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="crm-btn <?= ($filterStatus ?? 'all') === 'active' ? 'crm-btn-primary' : 'crm-btn-secondary' ?> crm-btn-sm">Active</a>
            <a href="/crm/athletes?status=frozen<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="crm-btn <?= ($filterStatus ?? 'all') === 'frozen' ? 'crm-btn-primary' : 'crm-btn-secondary' ?> crm-btn-sm">Frozen</a>
            <a href="/crm/athletes?status=expired<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="crm-btn <?= ($filterStatus ?? 'all') === 'expired' ? 'crm-btn-primary' : 'crm-btn-secondary' ?> crm-btn-sm">Expired</a>
        </div>
    </form>
</div>

<!-- Athletes Table Card -->
<div class="crm-panel-card" style="padding: 0; overflow: hidden;">
    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Athlete Name & Contact</th>
                    <th>Current Pass</th>
                    <th>Status</th>
                    <th>Days Left</th>
                    <th>Cryo Quota</th>
                    <th>Last Turnstile Visit</th>
                    <th>NFC Token</th>
                    <th style="text-align: right;">Membership Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($athletes)): ?>
                    <?php foreach ($athletes as $a): ?>
                        <tr>
                            <td>
                                <strong style="color: #fff; font-size: 0.92rem;"><?= e($a['full_name']) ?></strong>
                                <div style="font-size: 0.74rem; color: #64748b; margin-top: 2px;">
                                    <span><?= e($a['email']) ?></span> • <span><?= e($a['phone']) ?></span>
                                </div>
                            </td>

                            <td>
                                <span style="font-weight: 700; color: <?= $a['plan_code'] === 'max' ? 'var(--crm-cyan)' : ($a['plan_code'] === 'premium' ? 'var(--crm-lime)' : '#cbd5e1') ?>;">
                                    <?= e($a['plan_name']) ?>
                                </span>
                                <div style="font-size: 0.72rem; color: #94a3b8;"><?= number_format($a['price_monthly'] ?? 0, 0) ?> PLN/mo</div>
                            </td>

                            <td>
                                <span class="crm-status-pill status-<?= e($a['membership_status']) ?>">
                                    <?= e($a['membership_status']) ?>
                                </span>
                            </td>

                            <td>
                                <strong style="font-family: var(--font-mono); font-size: 0.9rem; color: <?= ($a['days_left'] ?? 0) <= 5 ? '#f43f5e' : '#fff' ?>;">
                                    <?= e($a['days_left'] ?? 0) ?>d
                                </strong>
                            </td>

                            <td>
                                <span style="font-size: 0.78rem;">
                                    <?= ($a['cryo_sessions_limit'] ?? 0) > 100 ? '👑 Unlimited' : e($a['cryo_sessions_limit'] ?? 0) . ' / mo' ?>
                                </span>
                            </td>

                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="font-size: 0.78rem; color: #cbd5e1;"><?= e($a['last_visit'] ?? 'Never') ?></span>
                                    <?php if (!empty($a['churn_alert'])): ?>
                                        <span class="crm-churn-alert-badge" title="Athlete has not entered gym in over 10 days! Proactive retention call required.">
                                            ⚠️ RETENTION RISK (<?= e($a['days_since_last_visit'] ?? 10) ?>d absent)
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td>
                                <code style="font-size: 0.72rem; background: rgba(0,0,0,0.4); padding: 2px 6px; border-radius: 4px; color: var(--crm-cyan);">
                                    <?= e($a['nfc_tag'] ?? 'NFC-XXXX') ?>
                                </code>
                            </td>

                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 6px;">
                                    <?php if (($a['membership_status'] ?? '') === 'active'): ?>
                                        <form action="/crm/athletes/action" method="POST" style="margin: 0;" onsubmit="return confirm('Freeze membership for 14 days?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="athlete_id" value="<?= e($a['id']) ?>">
                                            <input type="hidden" name="action" value="freeze">
                                            <button type="submit" class="crm-btn crm-btn-secondary crm-btn-sm" title="Freeze Pass for 14 days">🧊 Freeze</button>
                                        </form>
                                    <?php elseif (($a['membership_status'] ?? '') === 'frozen'): ?>
                                        <form action="/crm/athletes/action" method="POST" style="margin: 0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="athlete_id" value="<?= e($a['id']) ?>">
                                            <input type="hidden" name="action" value="unfreeze">
                                            <button type="submit" class="crm-btn crm-btn-primary crm-btn-sm">Unfreeze</button>
                                        </form>
                                    <?php endif; ?>

                                    <form action="/crm/athletes/action" method="POST" style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="athlete_id" value="<?= e($a['id']) ?>">
                                        <input type="hidden" name="action" value="extend_30_days">
                                        <button type="submit" class="crm-btn crm-btn-secondary crm-btn-sm" title="Extend 30 Days">+30d</button>
                                    </form>

                                    <form action="/crm/athletes/action" method="POST" style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="athlete_id" value="<?= e($a['id']) ?>">
                                        <input type="hidden" name="action" value="grant_cryo">
                                        <button type="submit" class="crm-btn crm-btn-secondary crm-btn-sm" title="Grant +1 Complimentary Cryo Session">+1 Cryo</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">
                            No athletes match the current search filter.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
