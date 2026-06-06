<?php
$pageTitle = "Manage Medicines";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1><i class="fas fa-pills mr-2"></i>Manage Medicines</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once 'views/partials/alerts.php'; ?>

            <div class="row">
                <!-- Add Medicine Form -->
                <div class="col-md-4">
                    <div class="card card-success">
                        <div class="card-header"><h3 class="card-title">Add New Medicine</h3></div>
                        <form action="index.php?page=store_medicine" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Medicine Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required placeholder="e.g. Amoxicillin">
                                </div>
                                <div class="form-group">
                                    <label>Dosage Forms</label>
                                    <input type="text" name="dosage_forms" class="form-control" placeholder="e.g. 250mg, 500mg">
                                    <small class="text-muted">Comma-separated strengths</small>
                                </div>
                                <div class="form-group">
                                    <label>Default Dosage</label>
                                    <input type="text" name="default_dosage" class="form-control" placeholder="e.g. 500mg twice daily">
                                </div>
                                <div class="form-group">
                                    <label>Unit</label>
                                    <select name="unit" class="form-control">
                                        <option value="">-- Select --</option>
                                        <option value="mg">mg</option>
                                        <option value="g">g</option>
                                        <option value="ml">ml</option>
                                        <option value="mcg">mcg</option>
                                        <option value="IU">IU</option>
                                        <option value="tablet">Tablet</option>
                                        <option value="capsule">Capsule</option>
                                        <option value="syrup">Syrup</option>
                                        <option value="drops">Drops</option>
                                        <option value="cream">Cream</option>
                                        <option value="injection">Injection</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-plus mr-1"></i> Add Medicine
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Medicines List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">All Medicines (<?= count($medicines) ?>)</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Dosage Forms</th>
                                        <th>Default Dosage</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($medicines)): ?>
                                        <tr><td colspan="6" class="text-center text-muted p-4">No medicines added yet.</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($medicines as $m): ?>
                                        <tr>
                                            <td><strong><?= sanitize($m['name']) ?></strong></td>
                                            <td><?= sanitize($m['dosage_forms']) ?: '—' ?></td>
                                            <td><?= sanitize($m['default_dosage']) ?: '—' ?></td>
                                            <td><?= sanitize($m['unit']) ?: '—' ?></td>
                                            <td>
                                                <span class="badge badge-<?= $m['is_active'] ? 'success' : 'secondary' ?>">
                                                    <?= $m['is_active'] ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Toggle -->
                                                <form action="index.php?page=toggle_medicine" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-<?= $m['is_active'] ? 'warning' : 'success' ?>"
                                                            title="<?= $m['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                                        <i class="fas fa-<?= $m['is_active'] ? 'ban' : 'check' ?>"></i>
                                                    </button>
                                                </form>
                                                <!-- Edit -->
                                                <button class="btn btn-sm btn-info" title="Edit"
                                                        onclick="openEditModal(<?= htmlspecialchars(json_encode($m)) ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <!-- Delete -->
                                                <form action="index.php?page=delete_medicine" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Delete this medicine?')">
                                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=update_medicine" method="POST">
                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white">Edit Medicine</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Dosage Forms</label>
                        <input type="text" name="dosage_forms" id="edit_dosage_forms" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Default Dosage</label>
                        <input type="text" name="default_dosage" id="edit_default_dosage" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit" id="edit_unit" class="form-control">
                            <option value="">-- Select --</option>
                            <?php foreach (['mg','g','ml','mcg','IU','tablet','capsule','syrup','drops','cream','injection'] as $u): ?>
                                <option value="<?= $u ?>"><?= $u ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(m) {
    document.getElementById('edit_id').value = m.id;
    document.getElementById('edit_name').value = m.name;
    document.getElementById('edit_dosage_forms').value = m.dosage_forms || '';
    document.getElementById('edit_default_dosage').value = m.default_dosage || '';
    document.getElementById('edit_unit').value = m.unit || '';
    $('#editModal').modal('show');
}
</script>

<?php require_once 'views/partials/footer.php'; ?>
