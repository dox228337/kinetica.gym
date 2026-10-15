<div class="dashboard-header-area">
    <div class="header-titles">
        <span class="dashboard-tag">PASS MANAGEMENT</span>
        <h1 class="dashboard-title">My Membership & Upgrade</h1>
        <p class="dashboard-desc">Manage your billing cycle, freeze period, or upgrade to a higher tier.</p>
    </div>
</div>

<!-- Active Plan Summary Box -->
<?php if (!empty($membership['is_active_paid'])): ?>
<div class="membership-overview-card">
    <div class="membership-card-top">
        <div>
            <span class="membership-badge-live"><?= e($membership['plan_badge'] ?? 'CURRENT PASS') ?></span>
            <h2 class="membership-plan-title"><?= e($membership['plan_name'] ?? 'Premium Pass') ?></h2>
            <p class="text-secondary"><?= e($membership['purchased_price'] ?? 319) ?> PLN / month • Automatic Monthly Renewal</p>
        </div>
        <div class="timer-box">
            <span class="timer-number"><?= e($membership['days_left'] ?? 22) ?></span>
            <span class="timer-label">DAYS REMAINING</span>
        </div>
    </div>

    <div class="freeze-banner-row">
        <div>
            <strong>Vacation / Injury Freeze Policy:</strong>
            <p class="text-secondary">You have <?= e($membership['freeze_days_remaining'] ?? 30) ?> days of complimentary freeze allowance left this calendar year.</p>
        </div>
        <?php if (($membership['status'] ?? '') === 'frozen'): ?>
            <form action="/dashboard/unfreeze" method="POST" onsubmit="return confirm('Unfreeze pass now and resume 24/7 club access?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary" style="background: var(--accent-cyan); color: #000; border-color: var(--accent-cyan);">
                    ⚡ Unfreeze Pass Now
                </button>
            </form>
        <?php else: ?>
            <?php $canFreeze = ($membership['freeze_days_remaining'] ?? 0) >= 14; ?>
            <form action="/dashboard/freeze" method="POST" onsubmit="return confirm('Freeze membership for 14 days?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-outline freeze-btn" <?= !$canFreeze ? 'disabled title="Minimum 14 freeze days required"' : '' ?>>
                    Freeze for 14 Days
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>
<div class="membership-overview-card" style="border-color: rgba(212, 255, 0, 0.4); background: linear-gradient(135deg, rgba(212, 255, 0, 0.05), #12141e);">
    <div class="membership-card-top">
        <div>
            <span class="membership-badge-live">FREE COMMUNITY PASS</span>
            <h2 class="membership-plan-title">No Active Gym Floor Pass</h2>
            <p class="text-secondary">Your account includes full access to 12+ video masterclasses. Select a club pass below to unlock 24/7 biometric turnstile access, recovery spa, and training floors.</p>
        </div>
        <div class="timer-box">
            <span class="timer-number">0</span>
            <span class="timer-label">DAYS REMAINING</span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Upgrade Tiers Comparison Grid -->
<div class="membership-upgrade-section">
    <h3 class="section-sub-title">Explore All 4 Membership Tiers</h3>
    <div class="pricing-grid">
        <?php foreach ($plans as $plan): ?>
            <div class="pricing-card <?= $plan['popular'] ? 'featured' : '' ?>">
                <?php if ($plan['popular']): ?>
                    <div class="popular-ribbon"><?= e($plan['badge']) ?></div>
                <?php else: ?>
                    <div class="plan-top-badge"><?= e($plan['badge']) ?></div>
                <?php endif; ?>

                <div class="plan-header">
                    <h4 class="plan-name"><?= e($plan['name']) ?></h4>
                    <p class="plan-desc"><?= e($plan['desc']) ?></p>
                </div>

                <div class="plan-price-wrapper">
                    <div class="price-display">
                        <span class="price-val"><?= $plan['price_monthly'] ?></span>
                        <span class="price-currency">PLN</span>
                        <span class="price-period">/mo</span>
                    </div>
                </div>

                <ul class="plan-features-list">
                    <?php foreach ($plan['features'] as $f): ?>
                        <li class="feature-item <?= $f['included'] ? 'included' : 'excluded' ?>">
                            <span class="feature-icon"><?= $f['included'] ? '✓' : '✕' ?></span>
                            <span class="feature-text"><?= e($f['text']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="plan-action">
                    <?php if (!empty($membership['is_active_paid']) && strtolower($membership['plan_name'] ?? '') === strtolower($plan['name'])): ?>
                        <button type="button" class="btn btn-secondary btn-block" disabled>Active Current Pass</button>
                    <?php else: ?>
                        <a href="/checkout?plan=<?= e($plan['id']) ?>&cycle=monthly" class="btn <?= $plan['popular'] ? 'btn-primary' : 'btn-outline' ?> btn-block">
                            <?= !empty($membership['is_active_paid']) ? 'Upgrade to ' . e($plan['name']) . ' →' : 'Activate ' . e($plan['name']) . ' →' ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
