<?php
$pageTitle = "My Appointments";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>My Appointments</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="index.php?page=dashboard">Dashboard</a>
                        </li>

                        <li class="breadcrumb-item active">
                            My Appointments
                        </li>
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
                        <i class="fas fa-calendar mr-1"></i>
                        Appointments List
                    </h3>

                    <div class="card-tools">
                        <a href="index.php?page=book_appointment" class="btn btn-primary btn-sm">

                            <i class="fas fa-plus"></i>
                            Book Appointment
                        </a>
                    </div>

                </div>

                <div class="card-body table-responsive p-0">

                    <table class="table table-hover table-striped">

                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($appointments)): ?>

                                <tr>
                                    <td colspan="5" class="text-center text-muted">

                                        No appointments found

                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($appointments as $appt): ?>

                                    <tr>

                                        <td>
                                            Dr. <?= sanitize($appt['doctor_name']) ?>
                                        </td>

                                        <td>
                                            <?= formatDate($appt['appt_date']) ?>
                                        </td>

                                        <td>
                                            <?= formatTime($appt['appt_time']) ?>
                                        </td>

                                        <td>

                                            <?php
                                            $badge = 'secondary';

                                            switch ($appt['status']) {

                                                case 'pending':
                                                    $badge = 'warning';
                                                    break;

                                                case 'confirmed':
                                                    $badge = 'info';
                                                    break;

                                                case 'completed':
                                                    $badge = 'success';
                                                    break;

                                                case 'cancelled':
                                                    $badge = 'danger';
                                                    break;
                                            }
                                            ?>

                                            <span class="badge badge-<?= $badge ?>">
                                                <?= ucfirst($appt['status']) ?>
                                            </span>

                                        </td>

                                        <td>
                                            <?= sanitize($appt['reason']) ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

</div>

<?php require_once 'views/partials/footer.php'; ?>