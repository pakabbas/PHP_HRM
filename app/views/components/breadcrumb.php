<?php
if (!empty($breadcrumbs)):
?>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= route_to('dashboard/index') ?>"><i class="bi bi-house-door"></i> Home</a></li>
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <?php if ($index === count($breadcrumbs) - 1): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($crumb['label']) ?></li>
            <?php else: ?>
                <li class="breadcrumb-item"><a href="<?= route_to($crumb['route']) ?>"><?= htmlspecialchars($crumb['label']) ?></a></li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
