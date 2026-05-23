<?php
$pageTitle = "Appointment Reports";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">

    <div class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">

                    <h1 class="m-0">
                        Appointment Reports
                    </h1>

                </div>

                <div class="col-sm-6">

                    <ol class="breadcrumb float-sm-right">

                        <li class="breadcrumb-item">
                            <a href="index.php?page=dashboard">
                                Home
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            Reports
                        </li>

                    </ol>

                </div>

            </div>

        </div>

    </div>

    <section class="content">

        <div class="container-fluid">

            <?php require_once 'views/partials/alerts.php'; ?>

            <!-- Filter Form -->
            <div class="card card-primary">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-filter mr-1"></i>

                        Filter Reports

                    </h3>

                </div>

                <div class="card-body">

                    <form method="GET" class="row">

                        <input type="hidden" name="page" value="reports">

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Start Date *
                                </label>

                                <input
                                    type="date"
                                    name="start_date"
                                    class="form-control"
                                    value="<?= sanitize($_GET['start_date'] ?? '') ?>"
                                    required>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    End Date *
                                </label>

                                <input
                                    type="date"
                                    name="end_date"
                                    class="form-control"
                                    value="<?= sanitize($_GET['end_date'] ?? '') ?>"
                                    required>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Doctor
                                </label>

                                <select name="doctor_id" class="form-control">

                                    <option value="">
                                        All Doctors
                                    </option>

                                    <?php foreach ($doctors as $doc): ?>

                                        <option
                                            value="<?= $doc['id'] ?>"
                                            <?= (($_GET['doctor_id'] ?? 0) == $doc['id']) ? 'selected' : '' ?>>

                                            Dr. <?= sanitize($doc['name']) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>
                                    Status
                                </label>

                                <select name="status" class="form-control">

                                    <option value="">
                                        All Status
                                    </option>

                                    <option
                                        value="pending"
                                        <?= ($_GET['status'] ?? '') == 'pending' ? 'selected' : '' ?>>

                                        Pending

                                    </option>

                                    <option
                                        value="confirmed"
                                        <?= ($_GET['status'] ?? '') == 'confirmed' ? 'selected' : '' ?>>

                                        Confirmed

                                    </option>

                                    <option
                                        value="completed"
                                        <?= ($_GET['status'] ?? '') == 'completed' ? 'selected' : '' ?>>

                                        Completed

                                    </option>

                                    <option
                                        value="cancelled"
                                        <?= ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : '' ?>>

                                        Cancelled

                                    </option>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-12">

                            <button type="submit" class="btn btn-primary">

                                <i class="fas fa-chart-line"></i>

                                Generate Report

                            </button>

<a
    href="index.php?page=export_report_csv&start_date=<?= urlencode($_GET['start_date'] ?? '') ?>&end_date=<?= urlencode($_GET['end_date'] ?? '') ?>&doctor_id=<?= urlencode($_GET['doctor_id'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>"
    class="btn btn-success">

    <i class="fas fa-file-csv"></i>

    Export CSV

</a>
                         <a
                            href="index.php?page=reports"
                                class="btn btn-secondary">

                           Reset

                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <?php if (!empty($_GET['start_date']) && !empty($_GET['end_date'])): ?>

                <!-- Summary -->
                <div class="row">

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-info">

                            <div class="inner">

                                <h3><?= $summary['total'] ?></h3>

                                <p>Total Appointments</p>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-warning">

                            <div class="inner">

                                <h3><?= $summary['pending'] ?></h3>

                                <p>Pending</p>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-success">

                            <div class="inner">

                                <h3><?= $summary['completed'] ?></h3>

                                <p>Completed</p>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-danger">

                            <div class="inner">

                                <h3><?= $summary['cancelled'] ?></h3>

                                <p>Cancelled</p>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- Table -->
                <div class="card">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i class="fas fa-table mr-1"></i>

                            Report Details:
                            <?= formatDate($_GET['start_date']) ?>
                            -
                            <?= formatDate($_GET['end_date']) ?>

                        </h3>

                    </div>

                    <div class="card-body table-responsive">

                        <table
                            class="table table-bordered table-striped"
                            id="reportTable">

                            <thead>

                                <tr>

                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Reason</th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php if (!empty($appointments)): ?>

                                    <?php foreach ($appointments as $app): ?>

                                        <?php
                                        $badgeClass = match ($app['status']) {

                                            'pending' => 'warning',

                                            'confirmed' => 'info',

                                            'completed' => 'success',

                                            'cancelled' => 'danger',

                                            default => 'secondary'
                                        };
                                        ?>

                                        <tr>

                                            <td>
                                                <?= $app['id'] ?>
                                            </td>

                                            <td>
                                                <?= sanitize($app['patient_name']) ?>
                                            </td>

                                            <td>
                                                Dr. <?= sanitize($app['doctor_name']) ?>
                                            </td>

                                            <td>
                                                <?= sanitize($app['specialization'] ?? '-') ?>
                                            </td>

                                            <td>
                                                <?= formatDate($app['appt_date']) ?>
                                            </td>

                                            <td>
                                                <?= formatTime($app['appt_time']) ?>
                                            </td>

                                            <td>

                                                <span class="badge badge-<?= $badgeClass ?>">

                                                    <?= ucfirst($app['status']) ?>

                                                </span>

                                            </td>

                                            <td>
                                                <?= sanitize($app['reason']) ?>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>

                                        <td colspan="8" class="text-center text-muted">

                                            No reports found for selected filters.

                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </section>

</div>

<script>

$(document).ready(function () {

    $('#reportTable').DataTable({

        "paging": true,

        "lengthChange": true,

        "searching": true,

        "ordering": true,

        "info": true,

        "autoWidth": false,

        "pageLength": 25

    });

});

</script>

<?php require_once 'views/partials/footer.php'; ?>