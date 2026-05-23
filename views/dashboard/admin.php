<?php
$pageTitle = "Admin Dashboard";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Admin Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once 'views/partials/alerts.php'; ?>

            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $totalPatients ?></h3>
                            <p>Total Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="index.php?page=users&role=patient" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $totalDoctors ?></h3>
                            <p>Total Doctors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <a href="index.php?page=doctors" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $todayAppointments ?></h3>
                            <p>Today's Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <a href="index.php?page=appointments&filter=today" class="small-box-footer">

                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $totalAppointments ?></h3>
                            <p>Total Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <a href="index.php?page=appointments" class="small-box-footer">

                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Weekly Stats Row -->
            <div class="row d-flex align-items-stretch">
                <!-- Weekly Appointments by Status -->
                <div class="col-md-6 d-flex">
                    <div class="card shadow-sm w-100 mb-4 border-0">
                        <div class="card-header border-0 bg-white">
                            <h3 class="card-title text-dark" style="font-weight: 600;">
                                <i class="fas fa-chart-pie mr-2"></i> Weekly Appointments by Status
                            </h3>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <!-- Pending -->
                                <div class="col-6 mb-2">
                                    <div class="p-3 d-flex align-items-center rounded text-dark"
                                        style="background-color: #feba17; height: 100px;">
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                            style="width: 45px; height: 45px; flex-shrink: 0;">
                                            <i class="fas fa-clock" style="color: #feba17; font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.9rem; font-weight: 500;">Pending</div>
                                            <div style="font-size: 1.4rem; font-weight: bold;">
                                                <?= $weeklyStats['pending'] ?? 4 ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Confirmed -->
                                <div class="col-6 mb-2">
                                    <div class="p-3 d-flex align-items-center rounded text-white"
                                        style="background-color: #17a2b8; height: 100px;">
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                            style="width: 45px; height: 45px; flex-shrink: 0;">
                                            <i class="fas fa-check" style="color: #17a2b8; font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.9rem; font-weight: 500;">Confirmed</div>
                                            <div style="font-size: 1.4rem; font-weight: bold;">
                                                <?= $weeklyStats['confirmed'] ?? 4 ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Completed -->
                                <div class="col-6">
                                    <div class="p-3 d-flex align-items-center rounded text-white"
                                        style="background-color: #28a745; height: 100px;">
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                            style="width: 45px; height: 45px; flex-shrink: 0;">
                                            <i class="fas fa-check-double"
                                                style="color: #28a745; font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.9rem; font-weight: 500;">Completed</div>
                                            <div style="font-size: 1.4rem; font-weight: bold;">
                                                <?= $weeklyStats['completed'] ?? 2 ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Cancelled -->
                                <div class="col-6">
                                    <div class="p-3 d-flex align-items-center rounded text-white"
                                        style="background-color: #dc3545; height: 100px;">
                                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                            style="width: 45px; height: 45px; flex-shrink: 0;">
                                            <i class="fas fa-times" style="color: #dc3545; font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size: 0.9rem; font-weight: 500;">Cancelled</div>
                                            <div style="font-size: 1.4rem; font-weight: bold;">
                                                <?= $weeklyStats['cancelled'] ?? 1 ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-md-6 d-flex">
                    <div class="card shadow-sm w-100 mb-4 border-0">
                        <div class="card-header border-0 bg-white">
                            <h3 class="card-title text-dark" style="font-weight: 600;">
                                <i class="fas fa-asterisk mr-2"></i> Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <a href="index.php?page=appointments"
                                        class="btn btn-block text-white d-flex align-items-center justify-content-center"
                                        style="background-color: #007bff; border-radius: 6px; height: 60px; font-weight: 500;">
                                        <i class="fas fa-calendar-plus mr-2"></i> New Appointment
                                    </a>
                                </div>
                                <div class="col-6 mb-3">
                                    <a href="index.php?page=doctors"
                                        class="btn btn-block text-white d-flex align-items-center justify-content-center"
                                        style="background-color: #28a745; border-radius: 6px; height: 60px; font-weight: 500;">
                                        <i class="fas fa-user-md mr-2"></i> Add Doctor
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="index.php?page=users&role=patient"
                                        class="btn btn-block text-white d-flex align-items-center justify-content-center"
                                        style="background-color: #17a2b8; border-radius: 6px; height: 60px; font-weight: 500;">
                                        <i class="fas fa-user-plus mr-2"></i> Add Patient
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="index.php?page=reports"
                                        class="btn btn-block text-dark d-flex align-items-center justify-content-center"
                                        style="background-color: #feba17; border-radius: 6px; height: 60px; font-weight: 500;">
                                        <i class="fas fa-file-alt mr-2"></i> Generate Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="card ">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i>
                        Recent Appointments
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAppointments as $app): ?>
                                <tr>
                                    <td><?= $app['id'] ?></td>
                                    <td><?= sanitize($app['patient_name']) ?></td>
                                    <td><?= sanitize($app['doctor_name']) ?></td>
                                    <td><?= formatDate($app['appt_date']) ?></td>
                                    <td><?= formatTime($app['appt_time']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= getStatusBadgeClass($app['status']) ?>">
                                            <?= ucfirst($app['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'views/partials/footer.php'; ?>