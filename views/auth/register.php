<div class="auth-card-inner">
    <div class="auth-title-area">
        <span class="auth-badge">JOIN KINETICA</span>
        <h1 class="auth-title">Create Account</h1>
        <p class="auth-desc">Register to activate your club membership pass or unlock 12+ free video masterclasses.</p>
    </div>

    <?php if (!empty($selectedPlan)): ?>
        <div class="selected-plan-callout" style="background: rgba(212, 255, 0, 0.08); border: 1px solid var(--accent-lime); border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <div>
                <span style="font-family: var(--font-mono); font-size: 0.72rem; color: var(--accent-lime); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                    ⚡ SELECTED MEMBERSHIP PASS
                </span>
                <div style="font-family: var(--font-heading); font-size: 1.15rem; font-weight: 800; color: #fff;">
                    <?= ucfirst(e($selectedPlan)) ?> Pass (<?= ($selectedCycle ?? 'monthly') === 'annual' ? 'Annual -20%' : 'Monthly' ?>)
                </div>
            </div>
            <div style="font-size: 0.78rem; color: var(--text-secondary); text-align: right; line-height: 1.3;">
                Step 1 of 2: Create Athlete Account<br>
                <strong style="color: var(--accent-lime);">Step 2: Instant Pass Activation →</strong>
            </div>
        </div>
    <?php endif; ?>

    <form action="/register" method="POST" class="auth-form" id="registerForm">
        <?= csrf_field() ?>

        <?php if (!empty($selectedPlan)): ?>
            <input type="hidden" name="selected_plan" value="<?= e($selectedPlan) ?>">
            <input type="hidden" name="selected_cycle" value="<?= e($selectedCycle ?? 'monthly') ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="full_name">Full Name <span class="required-star">*</span></label>
            <input type="text" id="full_name" name="full_name" class="form-input" placeholder="e.g. Alex Morgan" required autofocus>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label for="email">Email Address <span class="required-star">*</span></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-input" placeholder="+48 500 123 456">
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password (at least 6 characters) <span class="required-star">*</span></label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Create a secure password" minlength="6" required>
        </div>

        <!-- Fitness Goal -->
        <div class="form-group" id="goalSelectGroup">
            <label for="fitness_goal">Primary Focus</label>
            <select id="fitness_goal" name="fitness_goal" class="form-input form-select">
                <option value="Biomechanics & Hypertrophy">Biomechanics & Muscle Hypertrophy</option>
                <option value="Spine Decompression & Posture">Spine Decompression & Desk Posture Rehab</option>
                <option value="Hyrox & Functional Endurance">Hyrox Competition & Endurance</option>
                <option value="Sub-Zero Cryo & Longevity">Sub-Zero Cryo & Physical Longevity</option>
            </select>
        </div>

        <div class="form-group">
            <label for="preferred_language">Preferred Language</label>
            <select id="preferred_language" name="preferred_language" class="form-input form-select">
                <option value="en" <?= ($_SESSION['lang'] ?? 'en') === 'en' ? 'selected' : '' ?>>English (International)</option>
                <option value="pl" <?= ($_SESSION['lang'] ?? 'en') === 'pl' ? 'selected' : '' ?>>Polski (Warszawa)</option>
            </select>
        </div>

        <div class="form-consent">
            <label class="checkbox-label">
                <input type="checkbox" name="terms" required checked>
                <span>I agree to Kinetica Warsaw club rules, digital access policies, and privacy terms.</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg auth-submit-btn" id="regSubmitBtn">
            <span><?= !empty($selectedPlan) ? 'Create Account & Proceed to Pass Checkout' : 'Complete Registration' ?></span>
            <span class="btn-arrow">→</span>
        </button>
    </form>

    <div class="auth-switch-link">
        Already have an account? 
        <a href="/login<?= !empty($selectedPlan) ? '?plan=' . urlencode($selectedPlan) . '&cycle=' . urlencode($selectedCycle ?? 'monthly') : '' ?>" class="link-highlight">Sign in here</a>
    </div>
</div>
