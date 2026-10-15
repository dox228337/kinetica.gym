<!-- Client / Member Dashboard Overview -->
<div class="dashboard-header-area">
    <div class="header-titles">
        <span class="dashboard-tag">MEMBER OVERVIEW</span>
        <h1 class="dashboard-title">Welcome back, <?= e($user['full_name'] ?? 'Athlete') ?></h1>
        <p class="dashboard-desc">Your personal performance telemetry and Warsaw club access pass.</p>
    </div>
    <div class="header-actions-group">
        <button type="button" class="btn btn-primary" onclick="showNfcModal()">
            <span>📲 Digital NFC Pass</span>
        </button>
        <a href="/dashboard/schedule" class="btn btn-secondary">
            <span>📅 Book Workout</span>
        </a>
    </div>
</div>

<!-- Primary Membership Widget -->
<?php if (!empty($membership['is_active_paid'])): ?>
    <!-- Active Paid Membership Card -->
    <div class="membership-overview-card featured-border">
        <div class="membership-card-top">
            <div class="membership-plan-info">
                <span class="membership-badge-live"><?= e($membership['plan_badge'] ?? 'ACTIVE PASS') ?></span>
                <h2 class="membership-plan-title"><?= e($membership['plan_name'] ?? 'Premium Pass') ?></h2>
                <span class="membership-price-tag"><?= e($membership['purchased_price'] ?? 319) ?> PLN / month</span>
            </div>
            <div class="membership-timer-box">
                <span class="timer-number"><?= e($membership['days_left'] ?? 22) ?></span>
                <span class="timer-label">DAYS REMAINING</span>
            </div>
        </div>

        <!-- Expiration Progress Bar -->
        <div class="membership-progress-wrap">
            <div class="progress-bar-labels">
                <span>Active since <?= e($membership['start_date'] ?? date('Y-m-01')) ?></span>
                <span>Renews on <?= e($membership['end_date'] ?? date('Y-m-d', strtotime('+22 days'))) ?></span>
            </div>
            <?php
                $totalDays = max(1, (strtotime($membership['end_date'] ?? '+22 days') - strtotime($membership['start_date'] ?? date('Y-m-01'))) / 86400);
                $elapsed = max(0, (time() - strtotime($membership['start_date'] ?? date('Y-m-01'))) / 86400);
                $progressPercent = min(100, round(($elapsed / $totalDays) * 100));
            ?>
            <div class="progress-bar-track">
                <div class="progress-bar-fill" style="width: <?= $progressPercent ?>%;"></div>
            </div>
        </div>

        <!-- Monthly Quota Badges Grid -->
        <div class="membership-quotas-grid">
            <div class="quota-item">
                <div class="quota-icon">❄️</div>
                <div class="quota-details">
                    <strong class="quota-val"><?= max(0, ($membership['cryo_sessions_limit'] ?? 4) - ($membership['cryo_sessions_used'] ?? 0)) ?> / <?= e($membership['cryo_sessions_limit'] ?? 4) ?> Left</strong>
                    <span class="quota-name">-110°C Cryotherapy</span>
                </div>
            </div>

            <div class="quota-item">
                <div class="quota-icon">👥</div>
                <div class="quota-details">
                    <strong class="quota-val"><?= max(0, ($membership['guest_passes_per_month'] ?? 2) - ($membership['guest_passes_used'] ?? 0)) ?> / <?= e($membership['guest_passes_per_month'] ?? 2) ?> Left</strong>
                    <span class="quota-name">Free Guest Passes</span>
                </div>
            </div>

            <div class="quota-item">
                <div class="quota-icon">🔑</div>
                <div class="quota-details">
                    <strong class="quota-val">UNLIMITED</strong>
                    <span class="quota-name">24/7 Biometric Entry</span>
                </div>
            </div>

            <div class="quota-item freeze-action-item">
                <div class="quota-icon">🧊</div>
                <div class="quota-details">
                    <strong class="quota-val"><?= e($membership['freeze_days_remaining'] ?? 30) ?> Days</strong>
                    <span class="quota-name">Freeze Allowance</span>
                </div>
                <?php if (($membership['status'] ?? '') === 'frozen'): ?>
                    <form action="/dashboard/unfreeze" method="POST" onsubmit="return confirm('Unfreeze pass now and resume 24/7 club access?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary btn-sm" style="background: var(--accent-cyan); color: #000; border-color: var(--accent-cyan);">
                            ⚡ Unfreeze Pass
                        </button>
                    </form>
                <?php else: ?>
                    <?php $canFreeze = ($membership['freeze_days_remaining'] ?? 0) >= 14; ?>
                    <form action="/dashboard/freeze" method="POST" onsubmit="return confirm('Freeze membership for 14 days? Your expiration date will be automatically extended.');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline btn-sm freeze-btn" <?= !$canFreeze ? 'disabled title="Minimum 14 freeze days required"' : '' ?>>
                            Freeze Pass
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Free Community Lead Card -->
    <div class="free-lead-vault-card">
        <div class="free-vault-badge">🎁 100% FREE COMMUNITY TIER ACTIVE</div>
        <h2>You Have Unlocked 12+ Video Masterclasses!</h2>
        <p>Your free registered account gives you full online access to our biomechanics video vault and sports nutrition blueprints. You haven’t activated a gym floor pass yet.</p>
        
        <div class="free-upgrade-cta-row">
            <div class="free-voucher-pill">
                <span>🔥 Limited Promo: <strong>0 PLN Enrollment Fee</strong> this week</span>
            </div>
            <a href="/dashboard/memberships" class="btn btn-primary pulse-button">
                <span>Explore 4 Club Passes (Standart to Max) →</span>
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Dashboard Grid (2 Columns) -->
<div class="dashboard-grid-2">
    <!-- Column 1: Video Masterclasses & Free Courses Vault -->
    <div class="dash-card">
        <div class="dash-card-header">
            <h3>🎓 Video Masterclasses (Free Streaming)</h3>
            <span class="badge-free">12 UNLOCKED</span>
        </div>
        <p class="dash-card-subtitle">Exclusive biomechanics and rehab routines recorded by Kinetica coaches.</p>

        <div class="courses-mini-list">
            <div class="mini-course-item">
                <div class="mini-course-thumb">▶</div>
                <div class="mini-course-info">
                    <strong>The Bulletproof Squat & Spine Protection</strong>
                    <small>Coach Marek Kowalski • 75 min • 4K Video</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline" onclick="alert('Streaming player launched in high resolution 4K.');">Watch</button>
            </div>

            <div class="mini-course-item">
                <div class="mini-course-thumb">▶</div>
                <div class="mini-course-info">
                    <strong>Hypertrophy Architecture: Science of Muscle Growth</strong>
                    <small>Dr. Elena Rostova • 110 min • Video + PDF</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline" onclick="alert('Streaming player launched in high resolution 4K.');">Watch</button>
            </div>

            <div class="mini-course-item">
                <div class="mini-course-thumb">▶</div>
                <div class="mini-course-info">
                    <strong>Desk-Worker Posture & Shoulder Rehab</strong>
                    <small>Kasia Dąbrowska (Lead Physio) • 45 min</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline" onclick="alert('Streaming player launched in high resolution 4K.');">Watch</button>
            </div>
        </div>
    </div>

    <!-- Column 2: Turnstile Visits & Biometric Access Logs -->
    <div class="dash-card">
        <div class="dash-card-header">
            <h3>📍 Turnstile Entry Logs (Warsaw Złota 44)</h3>
            <span class="dash-status-dot">Live Sync</span>
        </div>
        <p class="dash-card-subtitle">Recent automated access scans through turnstile biometric sensors.</p>

        <div class="visits-table-wrapper">
            <table class="visits-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Gate / Location</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($visits)): ?>
                        <?php foreach ($visits as $visit): ?>
                            <tr>
                                <td><?= date('M d, H:i', strtotime($visit['entry_time'])) ?></td>
                                <td>Warsaw Turnstile #1</td>
                                <td>
                                    <span class="access-pill"><?= strtoupper($visit['access_method']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">No visits recorded yet. Scan your pass at the entrance!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="facility-status-box">
            <span>🕒 Club opening hours: <strong>24/7 (Staffed 06:00 - 23:00)</strong></span>
            <span>📍 Free parking: 2 hours complimentary with your digital pass</span>
        </div>
    </div>
</div>
