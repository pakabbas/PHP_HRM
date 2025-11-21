<?php $user = $currentUser ?? null; ?>
<header class="app-topbar">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-link text-white d-lg-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="page-heading mb-0"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
    </div>
    <div class="topbar-actions">
        <div class="badge bg-light text-dark px-3 py-2 text-uppercase">
            <?= htmlspecialchars($user['role'] ?? 'Guest') ?>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($user['username'] ?? 'Guest') ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <span class="dropdown-item-text text-muted">Signed in</span>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= route_to('auth/logout') ?>">Logout</a>
            </div>
        </div>
    </div>
</header>

