<?php
$pageTitle = "Doctor Dashboard";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">

    <!-- Header -->
<div class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-user-md"></i>
                    Doctor Dashboard
                </h1>
            </div>

            <div class="col-sm-6 text-right">

                <a href="index.php?page=doctor_profile"
                   class="btn btn-primary">

                    <i class="fas fa-user-edit"></i>
                    Edit Profile

                </a>

            </div>

        </div>

    </div>
</div>
    <!-- Main Content -->
    <section class="content">

        <div class="container-fluid">

            <?php require_once 'views/partials/alerts.php'; ?>

            <!-- Statistics -->
            <div class="row">

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">

                        <div class="inner">
                            <h3><?= $todayCount ?></h3>
                            <p>Today's Appointments</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>

                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">

                        <div class="inner">
                            <h3><?= $stats['total'] ?? 0 ?></h3>
                            <p>This Month</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>

                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">

                        <div class="inner">
                            <h3><?= $stats['pending'] ?? 0 ?></h3>
                            <p>Pending</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>

                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">

                        <div class="inner">
                            <h3><?= $stats['completed'] ?? 0 ?></h3>
                            <p>Completed</p>
                        </div>

                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Today's Appointments -->
            <div class="card card-info">

                <div class="card-header">
                    <h3 class="card-title">

                        <i class="fas fa-calendar-day mr-1"></i>

                        Today's Appointments
                        (<?= date('d M Y') ?>)

                    </h3>
                </div>

                <div class="card-body table-responsive p-0">

                    <table class="table table-hover table-striped">

                        <thead class="thead-light">

                            <tr>
                                <th>Patient</th>
                                <th>Time</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th width="250">Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($todayAppointments)): ?>

                                <tr>
                                    <td colspan="5" class="text-center text-muted p-4">
                                        No appointments today
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($todayAppointments as $app): ?>

                                    <tr>

                                        <td>
                                            <strong>
                                                <?= sanitize($app['patient_name']) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= formatTime($app['appt_time']) ?>
                                        </td>

                                        <td>
                                            <?= sanitize($app['reason']) ?>
                                        </td>

                                        <td>
                                            <span class="badge badge-<?= getStatusBadgeClass($app['status']) ?>">
                                                <?= ucfirst($app['status']) ?>
                                            </span>
                                        </td>

                                        <td>

                                            <form
                                                action="index.php?page=update_appointment_status"
                                                method="POST"
                                                class="d-inline">

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= CSRF::generateToken() ?>">

                                                <input
                                                    type="hidden"
                                                    name="appointment_id"
                                                    value="<?= $app['id'] ?>">

                                                <select
                                                    name="status"
                                                    onchange="this.form.submit()"
                                                    class="form-control form-control-sm d-inline"
                                                    style="width:140px;">

                                                    <option value="pending"
                                                        <?= $app['status'] == 'pending' ? 'selected' : '' ?>>
                                                        Pending
                                                    </option>

                                                    <option value="confirmed"
                                                        <?= $app['status'] == 'confirmed' ? 'selected' : '' ?>>
                                                        Confirmed
                                                    </option>

                                                    <option value="completed"
                                                        <?= $app['status'] == 'completed' ? 'selected' : '' ?>>
                                                        Completed
                                                    </option>

                                                    <option value="cancelled"
                                                        <?= $app['status'] == 'cancelled' ? 'selected' : '' ?>>
                                                        Cancelled
                                                    </option>

                                                </select>

                                            </form>

                                            <?php if ($app['status'] == 'completed'): ?>

                                                <a
                                                    href="index.php?page=create_prescription&appointment_id=<?= $app['id'] ?>"
                                                    class="btn btn-success btn-sm ml-2">

                                                    <i class="fas fa-prescription"></i>
                                                    Prescription

                                                </a>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- All Appointments -->
            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i>
                        All Appointments
                    </h3>

                    <div class="card-tools">

                        <form method="GET" class="form-inline">

                            <input
                                type="hidden"
                                name="page"
                                value="doctor_dashboard">

                            <select
                                name="status"
                                class="form-control form-control-sm"
                                onchange="this.form.submit()">

                                <option value="">All Status</option>

                                <option value="pending"
                                    <?= ($_GET['status'] ?? '') == 'pending' ? 'selected' : '' ?>>
                                    Pending
                                </option>

                                <option value="confirmed"
                                    <?= ($_GET['status'] ?? '') == 'confirmed' ? 'selected' : '' ?>>
                                    Confirmed
                                </option>

                                <option value="completed"
                                    <?= ($_GET['status'] ?? '') == 'completed' ? 'selected' : '' ?>>
                                    Completed
                                </option>

                                <option value="cancelled"
                                    <?= ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>
                                    Cancelled
                                </option>

                            </select>

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

                            <?php foreach ($appointments as $app): ?>

                                <tr>

                                    <td>
                                        <strong>
                                            <?= sanitize($app['patient_name']) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= formatDate($app['appt_date']) ?>
                                    </td>

                                    <td>
                                        <?= formatTime($app['appt_time']) ?>
                                    </td>

                                    <td>
                                        <?= sanitize($app['reason']) ?>
                                    </td>

                                    <td>
                                        <span class="badge badge-<?= getStatusBadgeClass($app['status']) ?>">
                                            <?= ucfirst($app['status']) ?>
                                        </span>
                                    </td>

                                    <td>

                                        <form
                                            action="index.php?page=update_appointment_status"
                                            method="POST"
                                            class="d-inline">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= CSRF::generateToken() ?>">

                                            <input
                                                type="hidden"
                                                name="appointment_id"
                                                value="<?= $app['id'] ?>">

                                            <select
                                                name="status"
                                                onchange="this.form.submit()"
                                                class="form-control form-control-sm d-inline"
                                                style="width:140px;">

                                                <option value="pending"
                                                    <?= $app['status'] == 'pending' ? 'selected' : '' ?>>
                                                    Pending
                                                </option>

                                                <option value="confirmed"
                                                    <?= $app['status'] == 'confirmed' ? 'selected' : '' ?>>
                                                    Confirmed
                                                </option>

                                                <option value="completed"
                                                    <?= $app['status'] == 'completed' ? 'selected' : '' ?>>
                                                    Completed
                                                </option>

                                                <option value="cancelled"
                                                    <?= $app['status'] == 'cancelled' ? 'selected' : '' ?>>
                                                    Cancelled
                                                </option>

                                            </select>

                                        </form>

                                        <?php if ($app['status'] == 'completed'): ?>

                                            <a
                                                href="index.php?page=create_prescription&appointment_id=<?= $app['id'] ?>"
                                                class="btn btn-success btn-sm ml-2">

                                                <i class="fas fa-prescription"></i>

                                            </a>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <!-- Pagination -->
                <div class="card-footer clearfix">

                    <ul class="pagination pagination-sm m-0 float-right">

                        <?php if ($paginator->hasPrev()): ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="?page=doctor_dashboard&p=<?= $paginator->getPrevPage() ?>&status=<?= $_GET['status'] ?? '' ?>">

                                    « Prev

                                </a>

                            </li>

                        <?php endif; ?>

                        <li class="page-item active">

                            <span class="page-link">

                                <?= $paginator->currentPage() ?>
                                /
                                <?= $paginator->totalPages() ?>

                            </span>

                        </li>

                        <?php if ($paginator->hasNext()): ?>

                            <li class="page-item">

                                <a
                                    class="page-link"
                                    href="?page=doctor_dashboard&p=<?= $paginator->getNextPage() ?>&status=<?= $_GET['status'] ?? '' ?>">

                                    Next »

                                </a>

                            </li>

                        <?php endif; ?>

                    </ul>

                </div>

            </div>

        </div>

    </section>

</div>

<?php require_once 'views/partials/footer.php'; ?>