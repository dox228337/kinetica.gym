<div class="auth-card-inner">
    <div class="auth-title-area">
        <span class="auth-badge">KINETICA PORTAL</span>
        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-desc">Sign in to access your digital pass, workouts, and biohacking perks.</p>
    </div>

    <?php if (!empty($selectedPlan)): ?>
        <div class="selected-plan-callout" style="background: rgba(212, 255, 0, 0.08); border: 1px solid var(--accent-lime); border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <div>
                <span style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--accent-lime); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                    ⚡ SELECTED PASS
                </span>
                <div style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #fff;">
                    <?= ucfirst(e($selectedPlan)) ?> Pass
                </div>
            </div>
            <div style="font-size: 0.78rem; color: var(--text-secondary); text-align: right; line-height: 1.3;">
                Sign in to proceed directly<br>
                <strong style="color: var(--accent-lime);">to pass activation →</strong>
            </div>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST" class="auth-form" id="loginForm">
        <?= csrf_field() ?>

        <?php if (!empty($selectedPlan)): ?>
            <input type="hidden" name="selected_plan" value="<?= e($selectedPlan) ?>">
            <input type="hidden" name="selected_cycle" value="<?= e($selectedCycle ?? 'monthly') ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
        </div>

        <div class="form-row-between">
            <label class="checkbox-label">
                <input type="checkbox" name="remember" checked>
                <span>Remember this device</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg auth-submit-btn">
            <span><?= !empty($selectedPlan) ? 'Sign In & Continue to Pass Checkout' : 'Sign In to Portal' ?></span>
            <span class="btn-arrow">→</span>
        </button>
    </form>

    <div class="auth-switch-link">
        Don’t have a Kinetica account yet? 
        <a href="/register<?= !empty($selectedPlan) ? '?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle ?? 'monthly') : '' ?>" class="link-highlight">Create an account</a>
    </div>
</div>
