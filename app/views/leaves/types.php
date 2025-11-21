<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Leave Types</h5>
        <a href="<?= route_to('leaves/index') ?>" class="btn btn-outline-secondary btn-sm">Back to Leaves</a>
    </div>
    <div class="card-body">
        <form class="row g-3 align-items-end" action="<?= route_to('leaves/saveType') ?>" method="post">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <div class="col-md-4">
                <label class="form-label">Type Name</label>
                <input type="text" name="type_name" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Default Days</label>
                <input type="number" name="default_days" class="form-control" min="0" value="10" required>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">Carry Forward</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="carry_forward" checked>
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Save Type</button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Default Days</th>
                    <th>Carry Forward</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td><?= htmlspecialchars($type['type_name']) ?></td>
                        <td><?= htmlspecialchars($type['default_days']) ?></td>
                        <td><?= $type['carry_forward'] ? 'Yes' : 'No' ?></td>
                        <td><span class="badge bg-<?= $type['status'] ? 'success' : 'secondary' ?>"><?= $type['status'] ? 'Active' : 'Inactive' ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

