<?php

$pageTitle = "Prescriptions";

require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';

?>

<div class="content-wrapper">

    <section class="content-header">

        <div class="container-fluid">

            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Prescriptions</h1>
                </div>

            </div>

        </div>

    </section>

    <section class="content">

        <div class="container-fluid">

            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        Prescriptions List
                    </h3>

                </div>

                <div class="card-body table-responsive p-0">

                    <table class="table table-hover table-striped">

                        <thead>

                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Diagnosis</th>
                                <th>Medications</th>
                                <th>Notes</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if(empty($prescriptions)): ?>

                                <tr>
                                    <td colspan="6" class="text-center">
                                        No prescriptions found
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach($prescriptions as $p): ?>

                                    <tr>

                                        <td>
                                            <?= sanitize($p['patient_name']?? 'N/A') ?>
                                        </td>

                                        <td>
                                            <?= sanitize($p['doctor_name']?? 'N/A') ?>
                                        </td>

                                        <td>
                                            <?= formatDate($p['appt_date']) ?>
                                        </td>

                                        <td>
                                            <?= sanitize($p['diagnosis']) ?>
                                        </td>

                                        <td>
                                            <?= sanitize($p['medications']) ?>
                                        </td>

                                        <td>
                                            <?= sanitize($p['notes']) ?>
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