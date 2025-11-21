<div class="stat-card shadow-sm">
    <div class="stat-body">
        <div class="stat-header">
            <p class="stat-label"><?= htmlspecialchars($title) ?></p>
            <span class="stat-icon">
                <i class="bi <?= htmlspecialchars($icon ?? 'bi-circle') ?>"></i>
            </span>
        </div>
        <h2 class="stat-value"><?= htmlspecialchars($value) ?></h2>
        <?php if (!empty($subtext)): ?>
            <p class="stat-subtext"><?= htmlspecialchars($subtext) ?></p>
        <?php endif; ?>
    </div>
</div>

