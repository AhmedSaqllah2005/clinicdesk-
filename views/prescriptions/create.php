<?php
$pageTitle = "Add Prescription";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Add Prescription</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php require_once 'views/partials/alerts.php'; ?>
            
            <div class="card card-success">
                <div class="card-header">
                    <h3>Prescription for Appointment #<?= $appointmentId ?></h3>
                </div>
                <form action="index.php?page=store_prescription" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <input type="hidden" name="appointment_id" value="<?= $appointmentId ?>">
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label>Diagnosis *</label>
                            <textarea name="diagnosis" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Medications *</label>
                            <textarea name="medications" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="form-group">
                            <label>PDF File (Optional)</label>
                            <input type="file" name="prescription_file" class="form-control" accept=".pdf">
                            <small>Max 3MB, PDF only</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">Save Prescription</button>
                        <a href="index.php?page=doctor_dashboard" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php require_once 'views/partials/footer.php'; ?>