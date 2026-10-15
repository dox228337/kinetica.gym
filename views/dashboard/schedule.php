<div class="dashboard-header-area">
    <div class="header-titles">
        <span class="dashboard-tag">CLASS BOOKINGS</span>
        <h1 class="dashboard-title">Class Schedule & Booking</h1>
        <p class="dashboard-desc">Reserve your spot in high-performance group classes and Olympic workshops.</p>
    </div>
</div>

<div class="classes-schedule-grid">
    <?php foreach ($classes as $c): ?>
        <div class="class-card">
            <div class="class-card-top">
                <span class="class-category-badge"><?= e($c['category']) ?></span>
                <span class="class-spots-left"><?= e($c['spots_left']) ?> spots left</span>
            </div>

            <h3 class="class-title"><?= e($c['title']) ?></h3>
            <p class="class-coach">👨‍🏫 <?= e($c['coach']) ?></p>
            <p class="class-zone">📍 <?= e($c['zone']) ?></p>
            <div class="class-time">🕒 <?= e($c['time']) ?></div>

            <div class="class-action">
                <?php if (!empty($c['is_booked'])): ?>
                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn btn-secondary btn-sm" disabled style="flex-grow: 1; opacity: 0.9; cursor: default; background: rgba(0, 240, 255, 0.15); color: #00f0ff; border-color: rgba(0, 240, 255, 0.3);">
                            ✓ Reserved
                        </button>
                        <form action="/dashboard/cancel-class" method="POST" onsubmit="return confirm('Cancel reservation for <?= e($c['title']) ?>?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="class_title" value="<?= e($c['title']) ?>">
                            <button type="submit" class="btn btn-outline btn-sm" style="color: #ef4444; border-color: rgba(239, 68, 68, 0.4);" title="Cancel reservation">
                                ✕
                            </button>
                        </form>
                    </div>
                <?php elseif (($c['spots_left'] ?? 0) <= 0): ?>
                    <button type="button" class="btn btn-outline btn-block btn-sm" disabled style="opacity: 0.5; cursor: not-allowed;">
                        Class Full
                    </button>
                <?php else: ?>
                    <form action="/dashboard/book-class" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="class_title" value="<?= e($c['title']) ?>">
                        <button type="submit" class="btn btn-primary btn-block btn-sm">
                            Reserve My Spot
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
