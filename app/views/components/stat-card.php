<div class="stat-card shadow-sm">
    <div class="stat-body">
        <p class="stat-label text-uppercase"><?= htmlspecialchars($title) ?></p>
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="stat-value"><?= htmlspecialchars($value) ?></h2>
            <span class="stat-icon">
                <i class="bi <?= htmlspecialchars($icon ?? 'bi-circle') ?>"></i>
            </span>
        </div>
        <?php if (!empty($subtext)): ?>
            <p class="stat-subtext"><?= htmlspecialchars($subtext) ?></p>
        <?php endif; ?>
    </div>
</div>

