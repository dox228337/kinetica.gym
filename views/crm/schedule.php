<!-- Coaching & Class Schedule Management -->

<div class="crm-panel-header" style="border: none; margin-bottom: 20px; padding: 0;">
    <div>
        <h2 style="font-size: 1.3rem;">📅 Coaching, Classes & Equipment Schedule</h2>
        <p class="crm-panel-subtitle">Manage daily trainer appointments, Hyrox group classes, and sub-zero cryo chamber reservations.</p>
    </div>
    <div>
        <button type="button" class="crm-btn crm-btn-primary" onclick="openBookModal()">
            <span>+ Book Athlete into Class</span>
        </button>
    </div>
</div>

<!-- Schedule Timeline Grid -->
<div class="crm-grid-2">
    <?php if (!empty($schedule)): ?>
        <?php foreach ($schedule as $session): ?>
            <div class="crm-panel-card" style="border-top: 3px solid <?= $session['type'] === 'personal_training' ? 'var(--crm-lime)' : ($session['type'] === 'spa_recovery' ? 'var(--crm-cyan)' : 'var(--crm-purple)') ?>;">
                <div class="crm-panel-header">
                    <div>
                        <span style="font-family: var(--font-mono); font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">
                            <?= e(str_replace('_', ' ', $session['type'])) ?>
                        </span>
                        <h3 style="margin-top: 3px; font-size: 1.05rem;"><?= e($session['title']) ?></h3>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-family: var(--font-mono); font-weight: 700; color: #fff; font-size: 0.95rem;">
                            <?= e($session['time_start']) ?> – <?= e($session['time_end']) ?>
                        </span>
                        <div style="font-size: 0.72rem; color: #64748b;">Warsaw (GMT+2)</div>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.82rem; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">👨‍🏫 Lead Coach:</span>
                        <strong style="color: #fff;"><?= e($session['coach_name']) ?></strong>
                    </div>

                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #94a3b8;">📍 Club Zone:</span>
                        <span style="color: #cbd5e1;"><?= e($session['zone']) ?></span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                        <span style="color: #94a3b8;">👥 Capacity:</span>
                        <div>
                            <strong style="color: <?= $session['booked_count'] >= $session['capacity'] ? '#f43f5e' : 'var(--crm-lime)' ?>;">
                                <?= e($session['booked_count']) ?> / <?= e($session['capacity']) ?>
                            </strong>
                            <span style="font-size: 0.72rem; color: #64748b;"> spots taken</span>
                        </div>
                    </div>
                </div>

                <!-- Attendee Athletes Tag Roster -->
                <div style="background: rgba(0,0,0,0.25); padding: 10px 12px; border-radius: 6px; border: 1px solid var(--crm-border-subtle);">
                    <span style="font-size: 0.7rem; font-family: var(--font-mono); color: #64748b; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 6px;">
                        Registered Athletes:
                    </span>
                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                        <?php if (!empty($session['attendees'])): ?>
                            <?php foreach ($session['attendees'] as $att): ?>
                                <span style="font-size: 0.74rem; background: rgba(255,255,255,0.06); color: #cbd5e1; padding: 2px 7px; border-radius: 4px;">
                                    ✓ <?= e($att) ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 14px;">
                    <button type="button" class="crm-btn crm-btn-secondary crm-btn-sm" onclick="openBookModalForSession(<?= e($session['id']) ?>, '<?= e(addslashes($session['title'])) ?>')">
                        + Add Athlete to this Class
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal: Book Athlete -->
<div class="crm-modal" id="bookModal">
    <div class="crm-modal-dialog">
        <div class="crm-modal-header">
            <h3>Book Athlete into Session</h3>
            <button type="button" class="crm-modal-close" onclick="closeBookModal()">&times;</button>
        </div>

        <form action="/crm/schedule/book" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 14px;">
                <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Select Class / Session</label>
                <select name="session_id" id="bookSessionSelect" class="form-input form-select" required>
                    <?php foreach ($schedule as $s): ?>
                        <option value="<?= e($s['id']) ?>"><?= e($s['title']) ?> (<?= e($s['time_start']) ?> – <?= e($s['time_end']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Athlete Name</label>
                <input type="text" name="athlete_name" class="form-input" placeholder="e.g. Alex Morgan" required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="crm-btn crm-btn-secondary" onclick="closeBookModal()">Cancel</button>
                <button type="submit" class="crm-btn crm-btn-primary">Confirm Reservation →</button>
            </div>
        </form>
    </div>
</div>

<script>
function openBookModal() {
    document.getElementById('bookModal').classList.add('show');
}
function openBookModalForSession(id, title) {
    document.getElementById('bookSessionSelect').value = id;
    document.getElementById('bookModal').classList.add('show');
}
function closeBookModal() {
    document.getElementById('bookModal').classList.remove('show');
}
</script>
