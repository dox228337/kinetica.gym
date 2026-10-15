<div class="crm-lead-card">
    <div class="crm-lead-card-header">
        <span class="crm-lead-name"><?= e($lead['full_name']) ?></span>
        <span class="crm-lead-score-pill">Score: <?= e($lead['score'] ?? 50) ?></span>
    </div>

    <span class="crm-lead-goal"><?= e($lead['fitness_goal'] ?? 'General Fitness') ?></span>

    <div class="crm-lead-contact">
        <?php if (!empty($lead['phone'])): ?>
            <span>📞 <a href="tel:<?= e($lead['phone']) ?>" style="color: #94a3b8; text-decoration: none;"><?= e($lead['phone']) ?></a></span>
        <?php endif; ?>
        <?php if (!empty($lead['email'])): ?>
            <span>✉️ <a href="mailto:<?= e($lead['email']) ?>" style="color: #64748b; text-decoration: none;"><?= e($lead['email']) ?></a></span>
        <?php endif; ?>
    </div>

    <?php if (!empty($lead['manager_notes'])): ?>
        <div class="crm-lead-notes" title="<?= e($lead['manager_notes']) ?>">
            <?= e($lead['manager_notes']) ?>
        </div>
    <?php endif; ?>

    <div class="crm-lead-actions-row">
        <!-- WhatsApp Quick Link -->
        <?php 
            $cleanPhone = preg_replace('/[^0-9]/', '', $lead['phone'] ?? '');
            if (substr($cleanPhone, 0, 2) !== '48' && strlen($cleanPhone) === 9) {
                $cleanPhone = '48' . $cleanPhone;
            }
        ?>
        <a href="https://wa.me/<?= $cleanPhone ?>?text=Hello%20<?= urlencode($lead['full_name']) ?>%2C%20greetings%20from%20Kinetica%20Gym%20Warsaw!" target="_blank" class="crm-lead-comm-btn wa-btn" title="Open WhatsApp Chat">
            <span>💬 WhatsApp</span>
        </a>

        <!-- Stage Mover Form -->
        <form action="/crm/leads/status" method="POST" style="margin: 0;">
            <?= csrf_field() ?>
            <input type="hidden" name="lead_id" value="<?= e($lead['id']) ?>">
            <select name="lead_status" class="crm-lead-move-select" onchange="this.form.submit()">
                <option value="new" <?= ($lead['lead_status'] ?? '') === 'new' ? 'selected' : '' ?>>Move: New</option>
                <option value="contacted" <?= ($lead['lead_status'] ?? '') === 'contacted' ? 'selected' : '' ?>>Move: Contacted</option>
                <option value="trial_scheduled" <?= ($lead['lead_status'] ?? '') === 'trial_scheduled' ? 'selected' : '' ?>>Move: Trial</option>
                <option value="converted" <?= ($lead['lead_status'] ?? '') === 'converted' ? 'selected' : '' ?>>Move: Converted</option>
                <option value="unqualified" <?= ($lead['lead_status'] ?? '') === 'unqualified' ? 'selected' : '' ?>>Move: Lost</option>
            </select>
        </form>
    </div>
</div>
