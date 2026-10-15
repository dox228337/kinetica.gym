<!-- Sales Pipeline Kanban Board -->
<div class="crm-panel-header" style="border: none; margin-bottom: 20px; padding: 0;">
    <div>
        <h2 style="font-size: 1.3rem;">🎯 Sales Pipeline & Lead Generation Funnel</h2>
        <p class="crm-panel-subtitle">Manage registered leads, assign trial screenings, and track conversions into paying club members.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="crm-btn crm-btn-primary" onclick="openLeadModal()">
            <span>+ Add Prospect (Phone / Walk-In)</span>
        </button>
    </div>
</div>

<!-- Kanban Columns Grid -->
<div class="crm-kanban-board">
    
    <!-- Column 1: New Prospects -->
    <div class="crm-kanban-col">
        <div class="crm-kanban-col-header">
            <span class="crm-col-title">📥 New Leads</span>
            <span class="crm-col-count"><?= count($kanban['new'] ?? []) ?></span>
        </div>
        <div class="crm-kanban-cards-list">
            <?php foreach (($kanban['new'] ?? []) as $lead): ?>
                <?php include __DIR__ . '/_lead_card.php'; ?>
            <?php endforeach; ?>
            <?php if (empty($kanban['new'])): ?>
                <div style="text-align: center; color: #64748b; font-size: 0.78rem; padding: 20px;">No new leads</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Column 2: First Contact -->
    <div class="crm-kanban-col">
        <div class="crm-kanban-col-header">
            <span class="crm-col-title">📞 Contacted</span>
            <span class="crm-col-count"><?= count($kanban['contacted'] ?? []) ?></span>
        </div>
        <div class="crm-kanban-cards-list">
            <?php foreach (($kanban['contacted'] ?? []) as $lead): ?>
                <?php include __DIR__ . '/_lead_card.php'; ?>
            <?php endforeach; ?>
            <?php if (empty($kanban['contacted'])): ?>
                <div style="text-align: center; color: #64748b; font-size: 0.78rem; padding: 20px;">No contacted leads</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Column 3: Trial Scheduled -->
    <div class="crm-kanban-col" style="border-top: 2px solid var(--crm-cyan);">
        <div class="crm-kanban-col-header">
            <span class="crm-col-title">🧪 Trial Scheduled</span>
            <span class="crm-col-count"><?= count($kanban['trial_scheduled'] ?? []) ?></span>
        </div>
        <div class="crm-kanban-cards-list">
            <?php foreach (($kanban['trial_scheduled'] ?? []) as $lead): ?>
                <?php include __DIR__ . '/_lead_card.php'; ?>
            <?php endforeach; ?>
            <?php if (empty($kanban['trial_scheduled'])): ?>
                <div style="text-align: center; color: #64748b; font-size: 0.78rem; padding: 20px;">No trials booked</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Column 4: Converted Clients -->
    <div class="crm-kanban-col" style="border-top: 2px solid var(--crm-lime);">
        <div class="crm-kanban-col-header">
            <span class="crm-col-title">🏆 Converted</span>
            <span class="crm-col-count"><?= count($kanban['converted'] ?? []) ?></span>
        </div>
        <div class="crm-kanban-cards-list">
            <?php foreach (($kanban['converted'] ?? []) as $lead): ?>
                <?php include __DIR__ . '/_lead_card.php'; ?>
            <?php endforeach; ?>
            <?php if (empty($kanban['converted'])): ?>
                <div style="text-align: center; color: #64748b; font-size: 0.78rem; padding: 20px;">No converted leads</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Column 5: Unqualified / Lost -->
    <div class="crm-kanban-col">
        <div class="crm-kanban-col-header">
            <span class="crm-col-title">🚫 Unqualified</span>
            <span class="crm-col-count"><?= count($kanban['unqualified'] ?? []) ?></span>
        </div>
        <div class="crm-kanban-cards-list">
            <?php foreach (($kanban['unqualified'] ?? []) as $lead): ?>
                <?php include __DIR__ . '/_lead_card.php'; ?>
            <?php endforeach; ?>
            <?php if (empty($kanban['unqualified'])): ?>
                <div style="text-align: center; color: #64748b; font-size: 0.78rem; padding: 20px;">No lost leads</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal: Create New Lead -->
<div class="crm-modal" id="createLeadModal">
    <div class="crm-modal-dialog">
        <div class="crm-modal-header">
            <h3>+ Add New Prospect to Funnel</h3>
            <button type="button" class="crm-modal-close" onclick="closeLeadModal()">&times;</button>
        </div>

        <form action="/crm/leads/create" method="POST">
            <?= csrf_field() ?>
            <div class="form-group" style="margin-bottom: 14px;">
                <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Prospect Full Name *</label>
                <input type="text" name="full_name" class="form-input" placeholder="e.g. Maciej Kozłowski" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Email Address</label>
                    <input type="email" name="email" class="form-input" placeholder="maciej@example.com">
                </div>
                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Phone (Warsaw / PL)</label>
                    <input type="tel" name="phone" class="form-input" placeholder="+48 500 000 000">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Lead Acquisition Source</label>
                    <select name="lead_source" class="form-input form-select">
                        <option value="reception_walk_in">🏢 Reception Walk-In (Złota 44)</option>
                        <option value="inbound_phone">📞 Inbound Telephone</option>
                        <option value="free_courses">🎁 Free Video Masterclasses</option>
                        <option value="instagram_ad">📸 Instagram / Meta Ads</option>
                        <option value="corporate_partner">🤝 Corporate Wellbeing</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Primary Fitness Focus</label>
                    <select name="fitness_goal" class="form-input form-select">
                        <option value="Biomechanics & Hypertrophy">Biomechanics & Hypertrophy</option>
                        <option value="Spine Decompression & Posture">Spine Decompression & Posture</option>
                        <option value="Hyrox & Endurance">Hyrox Competition & Engine</option>
                        <option value="Sub-Zero Cryo & Longevity">Sub-Zero Cryo & Longevity</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-size: 0.82rem; font-weight: 700; color: #cbd5e1; display: block; margin-bottom: 6px;">Manager Initial Notes</label>
                <textarea name="notes" class="form-input" rows="3" placeholder="Client walked in asking for personal training prices and cryo..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="crm-btn crm-btn-secondary" onclick="closeLeadModal()">Cancel</button>
                <button type="submit" class="crm-btn crm-btn-primary">Save to Pipeline →</button>
            </div>
        </form>
    </div>
</div>

<script>
function openLeadModal() {
    document.getElementById('createLeadModal').classList.add('show');
}
function closeLeadModal() {
    document.getElementById('createLeadModal').classList.remove('show');
}
</script>
