<?php
$pageTitle = "My Prescriptions";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">

            <div class="d-flex justify-content-between">

                <h1>My Prescriptions</h1>

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
                                <th>Date</th>
                                <th>Time</th>
                                <th>Doctor</th>
                                <th>Patient Condition</th>
                                <th>Diagnosis</th>
                                <th>Medications</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($prescriptions)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted p-4">
                                        No prescriptions found.
                                    </td>
                                </tr>
                            <?php else: ?>

                            <?php foreach ($prescriptions as $p): ?>

                                <tr>

                                    <td><?= formatDate($p['appt_date']) ?></td>

                                    <td><?= isset($p['appt_time']) ? formatTime($p['appt_time']) : '—' ?></td>

                                    <td>
                                        <strong><?= sanitize($p['doctor_name']) ?></strong>
                                    </td>

                                    <td><?= !empty($p['patient_condition']) ? nl2br(sanitize($p['patient_condition'])) : '<span class="text-muted">—</span>' ?></td>

                                    <td><?= nl2br(sanitize($p['diagnosis'])) ?></td>

                                    <td><?= nl2br(sanitize($p['medications'])) ?></td>

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