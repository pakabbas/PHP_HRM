<div class="text-center mb-4">
    <h2 class="fw-bold text-primary">Welcome Back</h2>
    <p class="text-muted">Sign in to manage your workforce</p>
</div>
<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
<form method="post" action="<?= route_to('auth/authenticate') ?>" class="needs-validation" novalidate>
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control form-control-lg" required>
    </div>
    <div class="mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control form-control-lg" required>
    </div>
    <button class="btn btn-primary w-100 btn-lg">Sign In</button>
</form>
<p class="mt-4 text-muted small mb-0">PHP HRM · Role-based HR suite</p>

