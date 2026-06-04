<?php
$pageTitle = "My Appointments";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';

$currentStatus = $_GET['status'] ?? '';
?>

<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-calendar-check mr-1"></i>
                        My Appointments
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="index.php?page=doctor_dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">My Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once 'views/partials/alerts.php'; ?>

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i>
                        All Appointments
                    </h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <input type="hidden" name="page" value="doctor_appointments">
                            <select name="status" class="form-control form-control-sm mr-1"
                                onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="pending"   <?= $currentStatus == 'pending'   ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= $currentStatus == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="completed" <?= $currentStatus == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $currentStatus == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <?php if ($currentStatus): ?>
                                <a href="index.php?page=doctor_appointments" class="btn btn-sm btn-outline-secondary ml-1">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th width="250">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-4">
                                        <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                        No appointments found
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $app): ?>
                                    <tr>
                                        <td><strong><?= sanitize($app['patient_name']) ?></strong></td>
                                        <td><?= formatDate($app['appt_date']) ?></td>
                                        <td><?= formatTime($app['appt_time']) ?></td>
                                        <td><?= sanitize($app['reason']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= getStatusBadgeClass($app['status']) ?>">
                                                <?= ucfirst($app['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form action="index.php?page=update_appointment_status"
                                                  method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token"
                                                       value="<?= CSRF::generateToken() ?>">
                                                <input type="hidden" name="appointment_id"
                                                       value="<?= $app['id'] ?>">
                                                <select name="status" onchange="this.form.submit()"
                                                        class="form-control form-control-sm d-inline"
                                                        style="width:140px;">
                                                    <option value="pending"   <?= $app['status'] == 'pending'   ? 'selected' : '' ?>>Pending</option>
                                                    <option value="confirmed" <?= $app['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                    <option value="completed" <?= $app['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="cancelled" <?= $app['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </form>
                                            <?php if ($app['status'] == 'completed'): ?>
                                                <a href="index.php?page=create_prescription&appointment_id=<?= $app['id'] ?>"
                                                   class="btn btn-success btn-sm ml-1"
                                                   title="Add Prescription">
                                                    <i class="fas fa-prescription-bottle-alt"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($paginator->totalPages() > 1): ?>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <?php if ($paginator->hasPrev()): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                       href="?page=doctor_appointments&p=<?= $paginator->getPrevPage() ?>&status=<?= $currentStatus ?>">
                                        &laquo;
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                                <li class="page-item <?= $i == $paginator->currentPage() ? 'active' : '' ?>">
                                    <a class="page-link"
                                       href="?page=doctor_appointments&p=<?= $i ?>&status=<?= $currentStatus ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($paginator->hasNext()): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                       href="?page=doctor_appointments&p=<?= $paginator->getNextPage() ?>&status=<?= $currentStatus ?>">
                                        &raquo;
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </section>

</div>

<?php require_once 'views/partials/footer.php'; ?>
