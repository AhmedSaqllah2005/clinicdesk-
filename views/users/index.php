<?php
$pageTitle = "Manage Users";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Manage Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- ALERTS -->
            <?php require_once 'views/partials/alerts.php'; ?>

            <!-- Add User Form -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus mr-1"></i>
                        Add New User
                    </h3>
                </div>
                <form action="index.php?page=store_user" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Password *</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Role *</label>
                                    <select name="role" id="role_select" class="form-control" required>
                                        <option value="patient">Patient</option>
                                        <option value="doctor">Doctor</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor-specific fields (hidden by default) -->
                        <div id="doctor_fields" style="display: none;">
                            <hr>
                            <h5>Doctor Information</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Specialization</label>
                                        <select name="specialization_id" class="form-control">
                                            <option value="">Select Specialization</option>
                                            <?php
                                            $specModel = new SpecializationModel();
                                            $specializations = $specModel->getAll();
                                            foreach ($specializations as $spec): ?>
                                                <option value="<?= $spec['id'] ?>"><?= sanitize($spec['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Consultation Fee (USD)</label>
                                        <input type="number" name="consultation_fee" class="form-control" step="0.01"
                                            value="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Available Days</label>
                                        <select name="available_days[]" class="form-control" multiple>
                                            <option value="Sun">Sunday</option>
                                            <option value="Mon">Monday</option>
                                            <option value="Tue">Tuesday</option>
                                            <option value="Wed">Wednesday</option>
                                            <option value="Thu">Thursday</option>
                                            <option value="Fri">Friday</option>
                                            <option value="Sat">Saturday</option>
                                        </select>
                                        <small class="text-muted">Hold Ctrl to select multiple</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Bio / Experience</label>
                                        <textarea name="bio" class="form-control" rows="2"
                                            placeholder="Doctor's biography, experience, education..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Users</h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <input type="hidden" name="page" value="users">
                            <select name="role" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <option value="">All Roles</option>
                                <option value="admin" <?= ($_GET['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin
                                </option>
                                <option value="doctor" <?= ($_GET['role'] ?? '') == 'doctor' ? 'selected' : '' ?>>Doctor
                                </option>
                                <option value="patient" <?= ($_GET['role'] ?? '') == 'patient' ? 'selected' : '' ?>>Patient
                                </option>
                            </select>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search..." value="<?= sanitize($_GET['search'] ?? '') ?>">
                            <button type="submit" class="btn btn-sm btn-primary ml-1"><i
                                    class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= sanitize($user['name']) ?></td>
                                    <td><?= sanitize($user['email']) ?></td>
                                    <td><?= sanitize($user['phone'] ?? '-') ?></td>
                                    <td>
                                        <span
                                            class="badge badge-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'doctor' ? 'info' : 'success') ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>

                                        <?php if (isset($user['is_active'])): ?>

                                            <form action="index.php?page=toggle_user" method="POST" style="display:inline;">

                                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                                                <input type="hidden" name="id" value="<?= $user['id'] ?>">

                                                <button type="submit"
                                                    class="btn btn-sm btn-<?= $user['is_active'] ? 'success' : 'secondary' ?>">

                                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>

                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span class="badge badge-success">
                                                Active
                                            </span>

                                        <?php endif; ?>

                                    </td>
                                    <td><?= formatDate($user['created_at']) ?></td>
                                    <td>
                                        <a href="index.php?page=edit_user&id=<?= $user['id'] ?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="index.php?page=delete_user" method="POST" style="display:inline"
                                            onsubmit="return confirm('Delete <?= sanitize($user['name']) ?>? This cannot be undone.')">
                                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                    </div>
                    </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                    <?php if ($paginator->hasPrev()): ?>
                        <li class="page-item"><a class="page-link"
                                href="?page=users&p=<?= $paginator->getPrevPage() ?>&role=<?= $_GET['role'] ?? '' ?>&search=<?= urlencode($_GET['search'] ?? '') ?>">«
                                Prev</a></li>
                    <?php endif; ?>
                    <li class="page-item active"><span class="page-link"><?= $paginator->currentPage() ?> /
                            <?= $paginator->totalPages() ?></span></li>
                    <?php if ($paginator->hasNext()): ?>
                        <li class="page-item"><a class="page-link"
                                href="?page=users&p=<?= $paginator->getNextPage() ?>&role=<?= $_GET['role'] ?? '' ?>&search=<?= urlencode($_GET['search'] ?? '') ?>">Next
                                »</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
</div>
</section>
</div>

<script>
    document.getElementById('role_select').addEventListener('change', function () {
        var doctorFields = document.getElementById('doctor_fields');
        if (this.value === 'doctor') {
            doctorFields.style.display = 'block';
        } else {
            doctorFields.style.display = 'none';
        }
    });
</script>

<?php require_once 'views/partials/footer.php'; ?>