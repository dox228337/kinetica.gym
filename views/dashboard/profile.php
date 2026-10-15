<div class="dashboard-header-area">
    <div class="header-titles">
        <span class="dashboard-tag">ACCOUNT SETTINGS</span>
        <h1 class="dashboard-title">Profile & Preferences</h1>
        <p class="dashboard-desc">Manage your contact information, biometric preferences, and club notifications.</p>
    </div>
</div>

<div class="profile-card-container">
    <div class="dash-card">
        <form class="profile-settings-form" onsubmit="event.preventDefault(); alert('Profile information updated successfully!');">
            <div class="form-row-2">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" class="form-input" value="<?= e($user['full_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="userEmail">Email Address</label>
                    <input type="email" id="userEmail" class="form-input" value="<?= e($user['email']) ?>" readonly>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label for="userPhone">Phone Number</label>
                    <input type="tel" id="userPhone" class="form-input" value="<?= e($user['phone'] ?? '+48 500 123 456') ?>">
                </div>
                <div class="form-group">
                    <label for="fitnessGoal">Primary Training Focus</label>
                    <input type="text" id="fitnessGoal" class="form-input" value="<?= e($user['fitness_goal'] ?? 'Biomechanics & Hypertrophy') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Digital Turnstile ID</label>
                <div class="nfc-token-readonly">
                    <code><?= e($user['nfc_token'] ?? 'NFC-849201') ?></code>
                    <small>Permanent NFC Identifier for Warsaw turnstiles</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>
