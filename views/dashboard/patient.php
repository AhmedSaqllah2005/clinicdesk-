<?php
$pageTitle = "Patient Dashboard";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">

    <!-- HEADER -->
    <div class="content-header">
        <div class="container-fluid">

            <div class="row mb-2 align-items-center">

                <div class="col-sm-6">
                    <h1 class="m-0">My Dashboard</h1>
                </div>

                <div class="col-sm-6 text-right">
                    <a href="index.php?page=patient_profile" class="btn btn-primary">
                        <i class="fas fa-user-edit"></i>
                        Edit Profile
                    </a>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="index.php?page=dashboard">Home</a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>

    <!-- CONTENT -->
    <section class="content">
        <div class="container-fluid">

            <?php require_once 'views/partials/alerts.php'; ?>

            <!-- STATS -->
            <div class="row">

                <div class="col-lg-6 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $activeCount ?? 0 ?></h3>
                            <p>Active Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $completedCount ?? 0 ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BOOK BUTTON -->
            <div class="row mb-3">
                <div class="col-12">
                    <a href="index.php?page=book_appointment" class="btn btn-info btn-block">
                        <i class="fas fa-plus-circle"></i>
                        Book New Appointment
                    </a>
                </div>
            </div>

            <!-- NEXT APPOINTMENT -->
            <?php if (!empty($nextAppointment)): ?>
                <div class="card card-primary">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-star"></i>
                            Next Appointment
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-3">
                                <strong>Doctor:</strong><br>
                                <?= sanitize($nextAppointment['doctor_name'] ?? '-') ?>
                            </div>

                            <div class="col-md-3">
                                <strong>Specialization:</strong><br>
                                <?= sanitize($nextAppointment['specialization'] ?? 'General') ?>
                            </div>

                            <div class="col-md-3">
                                <strong>Date:</strong><br>
                                <?= !empty($nextAppointment['appt_date']) ? formatDate($nextAppointment['appt_date']) : '-' ?>
                            </div>

                            <div class="col-md-3">
                                <strong>Time:</strong><br>
                                <?= !empty($nextAppointment['appt_time']) ? formatTime($nextAppointment['appt_time']) : '-' ?>
                            </div>

                        </div>

                        <hr>

                        <div>
                            <strong>Reason:</strong>
                            <?= sanitize($nextAppointment['reason'] ?? '-') ?>
                        </div>

                    </div>
                </div>
            <?php endif; ?>

            <!-- RECENT APPOINTMENTS -->
            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        Recent Appointments
                    </h3>

                    <div class="card-tools">

                        <a href="index.php?page=my_appointments" class="btn btn-sm btn-primary">

                            View All

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

                                        <?php if (!empty($recentAppointments)): ?>

                                            <?php foreach ($recentAppointments as $app): ?>

                                    <tr>

                                        <td><?= sanitize($app['doctor_name'] ?? '-') ?></td>

                                        <td><?= !empty($app['appt_date']) ? formatDate($app['appt_date']) : '-' ?></td>

                                        <td><?= !empty($app['appt_time']) ? formatTime($app['appt_time']) : '-' ?></td>

                                        <td>
                                            <span class="badge badge-<?= getStatusBadgeClass($app['status'] ?? '') ?>">
                                                            <?= ucfirst($app['status'] ?? '-') ?>
                                            </span>
                                        </td>

                                        <td><?= sanitize($app['reason'] ?? '-') ?></td>

                                    </tr>

                                            <?php endforeach; ?>

                                        <?php else: ?>

                                <tr>
                                    <td colspan="5" class="text-center text-muted p-3">
                                        No appointments found
                                    </td>
                                </tr>

                                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

                        <?php require_once 'views/partials/footer.php'; ?>