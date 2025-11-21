<?php
$breadcrumbs = [
    ['label' => 'Configurations', 'route' => 'config/index']
];
$entities = [
    'departments' => ['label' => 'Departments', 'fields' => ['department_name' => 'Department Name']],
    'designations' => ['label' => 'Designations', 'fields' => ['designation_name' => 'Designation Name']],
    'cities' => ['label' => 'Cities', 'fields' => ['city_name' => 'City Name']],
    'configurations' => ['label' => 'Dictionary', 'fields' => ['config_type' => 'Type', 'config_key' => 'Key', 'config_value' => 'Value']],
    'holidays' => ['label' => 'Holidays', 'fields' => ['holiday_date' => 'Date', 'description' => 'Description']],
];
$active = $entity ?? 'departments';
$activeMeta = $entities[$active];
?>
<ul class="nav nav-pills mb-3">
    <?php foreach ($entities as $key => $meta): ?>
        <li class="nav-item">
            <a class="nav-link <?= $active === $key ? 'active' : '' ?>" href="<?= route_to('config/index', ['entity' => $key]) ?>"><?= $meta['label'] ?></a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><?= $activeMeta['label'] ?></h5>
    </div>
    <div class="card-body">
        <form class="row g-3" method="post" action="<?= route_to('config/store') ?>">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="entity" value="<?= $active ?>">
            <?php foreach ($activeMeta['fields'] as $field => $label): ?>
                <div class="<?= $field === 'description' ? 'col-12' : 'col-md-4' ?>">
                    <label class="form-label"><?= $label ?></label>
                    <?php if ($field === 'holiday_date'): ?>
                        <input type="date" name="<?= $field ?>" class="form-control" required>
                    <?php elseif ($field === 'description'): ?>
                        <textarea name="<?= $field ?>" class="form-control" rows="2"></textarea>
                    <?php else: ?>
                        <input type="text" name="<?= $field ?>" class="form-control" required>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if (in_array($active, ['departments','designations','cities','configurations'], true)): ?>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-12 text-end">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Save</button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <?php foreach (array_keys($activeMeta['fields']) as $field): ?>
                        <th><?= ucwords(str_replace('_', ' ', $field)) ?></th>
                    <?php endforeach; ?>
                    <?php if (in_array($active, ['departments','designations','cities','configurations'], true)): ?>
                        <th>Status</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No records yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <?php foreach (array_keys($activeMeta['fields']) as $field): ?>
                                <td><?= htmlspecialchars($item[$field]) ?></td>
                            <?php endforeach; ?>
                            <?php if (in_array($active, ['departments','designations','cities','configurations'], true)): ?>
                                <td><span class="badge bg-<?= $item['status'] ? 'success' : 'secondary' ?>"><?= $item['status'] ? 'Active' : 'Inactive' ?></span></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

