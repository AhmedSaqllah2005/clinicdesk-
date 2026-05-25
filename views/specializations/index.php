<?php
Auth::requireRole('admin');
$pageTitle = "Specializations";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Specializations</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Specializations</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once 'views/partials/alerts.php'; ?>

            <div class="row">

                <!-- ── قائمة التخصصات ── -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i> All Specializations
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($specializations)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No specializations found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($specializations as $spec): ?>
                                            <tr>
                                                <td><?= (int) $spec['id'] ?></td>
                                                <td><?= htmlspecialchars($spec['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <form action="index.php?page=delete_specialization" method="POST"
                                                        onsubmit="return confirm('Delete this specialization?')">
                                                        <input type="hidden" name="csrf_token"
                                                            value="<?= CSRF::generateToken() ?>">
                                                        <input type="hidden" name="specialization_id"
                                                            value="<?= (int) $spec['id'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i> Delete
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

                <!-- ── نموذج إضافة تخصص ── -->
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus mr-1"></i> Add Specialization
                            </h3>
                        </div>
                        <form action="index.php?page=store_specialization" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Specialization Name *</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Cardiology"
                                        maxlength="100" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<?php require_once 'views/partials/footer.php'; ?>