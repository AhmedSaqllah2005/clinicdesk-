<?php
$pageTitle = "My Appointments";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">

                <h1>My Appointments</h1>
                <a href="index.php?page=dashboard" class="btn btn-secondary">

                    <i class="fas fa-arrow-left"></i>
                    Back To Dashboard
                </a>
            </div>

        </div>
    </div>

    <section class="content">

        <div class="container-fluid">

            <div class="card">

                <div class="card-body table-responsive">

                    <table class="table table-bordered table-striped">

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

                            <?php foreach ($appointments as $app): ?>

                                <tr>

                                    <td><?= sanitize($app['doctor_name']) ?></td>

                                    <td><?= formatDate($app['appt_date']) ?></td>

                                    <td><?= formatTime($app['appt_time']) ?></td>

                                    <td>

                                        <span class="badge badge-<?= getStatusBadgeClass($app['status']) ?>">

                                            <?= ucfirst($app['status']) ?>

                                        </span>

                                    </td>

                                    <td><?= sanitize($app['reason']) ?></td>

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