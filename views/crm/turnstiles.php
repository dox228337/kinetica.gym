<!-- Turnstiles & Access Control Telemetry -->

<div class="crm-panel-header" style="border: none; margin-bottom: 20px; padding: 0;">
    <div>
        <h2 style="font-size: 1.3rem;">🚪 Turnstiles & Biometric Access Control (СКУД)</h2>
        <p class="crm-panel-subtitle">Warsaw Złota 44 real-time gate telemetry, biometric face scanners, and club occupancy monitoring.</p>
    </div>
</div>

<!-- Top Stats Grid: Occupancy & Gates Status -->
<div class="crm-grid-3">
    <!-- Card 1: Live Warsaw Club Occupancy -->
    <div class="crm-panel-card accent-emerald" style="border-left: 3px solid var(--crm-emerald);">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Warsaw Club Occupancy</span>
            <span class="crm-live-gate-pill"><span class="live-pulse-dot"></span> LIVE</span>
        </div>
        <div class="crm-stat-value" style="margin: 8px 0;"><?= e($stats['occupancy_current']) ?> <span style="font-size: 1rem; color: #94a3b8;">/ <?= e($stats['occupancy_max']) ?> Max</span></div>
        <div style="background: rgba(255,255,255,0.06); height: 8px; border-radius: 4px; overflow: hidden; margin-bottom: 8px;">
            <div style="background: var(--crm-emerald); width: <?= e($stats['occupancy_percentage']) ?>%; height: 100%;"></div>
        </div>
        <span style="font-size: 0.76rem; color: #94a3b8;">35% of max safe floor capacity utilized</span>
    </div>

    <!-- Card 2: Zone Breakdown -->
    <div class="crm-panel-card" style="border-left: 3px solid var(--crm-cyan);">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Zone Distribution</span>
            <span class="crm-stat-icon">📍</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 10px; font-size: 0.8rem;">
            <div style="display: flex; justify-content: space-between;">
                <span>Main Biomechanics & Gym Floor:</span>
                <strong style="color: #fff;">28 athletes</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Eleiko Olympic Platforms:</span>
                <strong style="color: #fff;">9 athletes</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Sub-Zero Cryo & Recovery Spa:</span>
                <strong style="color: var(--crm-cyan);">5 athletes</strong>
            </div>
        </div>
    </div>

    <!-- Card 3: Today's Total Check-Ins -->
    <div class="crm-panel-card" style="border-left: 3px solid var(--crm-lime);">
        <div class="crm-stat-header">
            <span class="crm-stat-title">Daily Access Scans</span>
            <span class="crm-stat-icon">⚡</span>
        </div>
        <div class="crm-stat-value" style="margin: 8px 0;"><?= e($stats['visits_today']) ?> <span style="font-size: 0.95rem; color: var(--crm-lime);">Scans</span></div>
        <span style="font-size: 0.76rem; color: #94a3b8;">Avg session length: <strong>76 minutes</strong></span>
    </div>
</div>

<!-- Biometric Turnstile Scanner Simulator Card -->
<div class="crm-panel-card" style="margin-bottom: 26px; border: 1px solid var(--crm-border-glow); background: linear-gradient(135deg, rgba(212, 255, 0, 0.03), #131724);">
    <div class="crm-panel-header">
        <div>
            <h3>⚡ Warsaw Turnstile Biometric Scanner (Simulator)</h3>
            <p class="crm-panel-subtitle">Simulate real-time NFC token, Face-ID, or QR application entry through Warsaw turnstiles.</p>
        </div>
        <span class="crm-status-pill status-active">Hikvision / Dahua Active</span>
    </div>

    <form action="/crm/turnstiles/simulate" method="POST" style="display: grid; grid-template-columns: 2fr 1.5fr 2fr auto; gap: 14px; align-items: flex-end;">
        <?= csrf_field() ?>
        <div>
            <label style="font-size: 0.78rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Athlete Name / Pass Holder</label>
            <input type="text" name="athlete_name" class="form-input" value="Alex Morgan" placeholder="e.g. Alex Morgan" required>
        </div>

        <div>
            <label style="font-size: 0.78rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Authentication Method</label>
            <select name="method" class="form-input form-select">
                <option value="Apple Wallet NFC">📱 Apple Wallet NFC</option>
                <option value="Biometric Face-ID">👤 Biometric Face-ID</option>
                <option value="Mobile QR App">📲 Mobile QR App</option>
                <option value="RFID Bracelet">⌚ RFID Silicone Band</option>
            </select>
        </div>

        <div>
            <label style="font-size: 0.78rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Turnstile Gate</label>
            <select name="gate" class="form-input form-select">
                <option value="Warsaw Main Gate - Turnstile 01">🚪 Warsaw Main Gate - Turnstile 01</option>
                <option value="Warsaw Main Gate - Turnstile 02">🚪 Warsaw Main Gate - Turnstile 02</option>
                <option value="Sub-Zero Cryo Chamber Gate">🧊 Sub-Zero Cryo Chamber Gate</option>
                <option value="Finnish Sauna & Spa Zone">🧖 Finnish Sauna & Spa Zone</option>
            </select>
        </div>

        <div>
            <button type="submit" class="crm-btn crm-btn-primary" style="height: 42px;">
                <span>⚡ Simulate Scan</span>
            </button>
        </div>
    </form>
</div>

<!-- Live Scans Table Card -->
<div class="crm-panel-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 18px 22px; border-bottom: 1px solid var(--crm-border-subtle); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; font-size: 1rem;">📡 Warsaw Gate Access Logs (Real-Time Feed)</h3>
        <span style="font-size: 0.75rem; color: #64748b; font-family: var(--font-mono);">Auto-updating biometric telemetry</span>
    </div>

    <div class="crm-table-wrapper">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Date & Time</th>
                    <th>Athlete</th>
                    <th>Gate Location</th>
                    <th>Direction</th>
                    <th>Method</th>
                    <th style="text-align: right;">Access Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td><code>#<?= e($l['id']) ?></code></td>
                            <td style="font-family: var(--font-mono); font-size: 0.78rem;">
                                <strong><?= e($l['time']) ?></strong> <small style="color: #64748b;"><?= e($l['date']) ?></small>
                            </td>
                            <td><strong style="color: #fff;"><?= e($l['athlete_name']) ?></strong></td>
                            <td style="font-size: 0.8rem; color: #cbd5e1;"><?= e($l['gate']) ?></td>
                            <td>
                                <strong style="color: <?= ($l['direction'] ?? '') === 'ENTRY' ? 'var(--crm-lime)' : '#94a3b8' ?>;">
                                    <?= e($l['direction'] ?? 'ENTRY') ?>
                                </strong>
                            </td>
                            <td>
                                <span style="font-size: 0.72rem; background: rgba(255,255,255,0.06); padding: 3px 7px; border-radius: 4px; font-family: var(--font-mono);">
                                    <?= e($l['method']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <span class="crm-status-pill status-<?= e($l['status_badge'] ?? 'granted') ?>">
                                    <?= e($l['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
