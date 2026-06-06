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
                            <label>Patient Condition <span class="text-danger">*</span></label>
                            <small class="form-text text-muted mb-1">Describe the patient's current condition and symptoms.</small>
                            <textarea name="patient_condition" class="form-control" rows="3" required placeholder="e.g. Patient presents with fever, sore throat, and fatigue for 3 days..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Diagnosis <span class="text-danger">*</span></label>
                            <small class="form-text text-muted mb-1">Clinical diagnosis based on examination.</small>
                            <textarea name="diagnosis" class="form-control" rows="3" required placeholder="e.g. Acute tonsillitis"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Medications <span class="text-danger">*</span></label>
                            <small class="form-text text-muted mb-1">List medications with dosage and duration.</small>
                            <textarea name="medications" class="form-control" rows="4" required placeholder="e.g. Amoxicillin 500mg — twice daily for 7 days&#10;Paracetamol 500mg — as needed for fever"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Additional Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Rest for 3 days, drink plenty of fluids..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>PDF File (Optional)</label>
                            <input type="file" name="prescription_file" class="form-control" accept=".pdf">
                            <small class="form-text text-muted">Max 3MB, PDF only</small>
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