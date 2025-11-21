<?php
$user = $currentUser ?? null;
$role = $user['role'] ?? 'guest';
$navItems = [
    ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'dashboard/index', 'roles' => ['admin','hr','manager','employee']],
    ['label' => 'Employees', 'icon' => 'bi-people', 'route' => 'employees/index', 'roles' => ['admin','hr','manager','employee']],
    ['label' => 'Attendance', 'icon' => 'bi-calendar-check', 'route' => 'attendance/index', 'roles' => ['admin','hr','manager','employee']],
    ['label' => 'Leaves', 'icon' => 'bi-sun', 'route' => 'leaves/index', 'roles' => ['admin','hr','manager','employee']],
    ['label' => 'Payroll', 'icon' => 'bi-cash-stack', 'route' => 'payroll/index', 'roles' => ['admin','hr']],
    ['label' => 'Reports', 'icon' => 'bi-graph-up', 'route' => 'reports/index', 'roles' => ['admin','hr','manager']],
    ['label' => 'Configurations', 'icon' => 'bi-sliders2', 'route' => 'config/index', 'roles' => ['admin','hr']],
    ['label' => 'Users', 'icon' => 'bi-shield-lock', 'route' => 'users/index', 'roles' => ['admin']],
];
?>
<aside class="app-sidebar">
    <div class="logo-box">
        <span class="logo-icon">HR</span>
        <div>
            <p class="logo-title">PHP HRM</p>
            <small class="logo-subtitle">Human Resource Suite</small>
        </div>
    </div>
    <nav class="mt-4">
        <?php foreach ($navItems as $item): ?>
            <?php if (!in_array($role, $item['roles'], true)) continue; ?>
            <a class="nav-link <?= strpos($currentRoute, $item['route']) === 0 ? 'active' : '' ?>"
               href="<?= route_to($item['route']) ?>">
                <i class="bi <?= $item['icon'] ?>"></i>
                <span><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>

