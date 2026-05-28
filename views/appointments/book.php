<?php
$pageTitle = "Book Appointment";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Book New Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php?page=my_appointments">My Appointments</a></li>
                        <li class="breadcrumb-item active">Book</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php require_once 'views/partials/alerts.php'; ?>
            
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-plus mr-1"></i>
                        Appointment Details
                    </h3>
                </div>
                <form action="index.php?page=store_appointment" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                    <div class="card-body">
                        <?php if (Auth::role() === 'admin'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Select Patient *</label>
                                    <select name="patient_id" class="form-control" required>
                                        <option value="">-- Select Patient --</option>
                                        <?php foreach ($this->userModel->getPatients() as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= sanitize($p['name']) ?> (<?= sanitize($p['email']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-user-md"></i> Select Doctor *</label>
                                    <select name="doctor_id" id="doctor_id" class="form-control" required>
                                        <option value="">-- Select Doctor --</option>
                                        <?php foreach ($doctors as $doc): ?>
                                            <option value="<?= $doc['id'] ?>">
                                                Dr. <?= sanitize($doc['name']) ?> - <?= sanitize($doc['specialization_name'] ?? 'General') ?>
                                                (<?= number_format($doc['consultation_fee'] ?? 0, 2) ?> USD)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-stethoscope"></i> Specialization</label>
                                    <input type="text" id="specialization" class="form-control" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-calendar"></i> Appointment Date *</label>
                                    <input type="date" name="appt_date" id="appt_date" class="form-control" 
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-clock"></i> Appointment Time *</label>
                                    <select name="appt_time" class="form-control" required>
                                        <option value="">-- Select Time --</option>
                                        <option value="09:00:00">09:00 AM</option>
                                        <option value="09:30:00">09:30 AM</option>
                                        <option value="10:00:00">10:00 AM</option>
                                        <option value="10:30:00">10:30 AM</option>
                                        <option value="11:00:00">11:00 AM</option>
                                        <option value="11:30:00">11:30 AM</option>
                                        <option value="12:00:00">12:00 PM</option>
                                        <option value="12:30:00">12:30 PM</option>
                                        <option value="13:00:00">01:00 PM</option>
                                        <option value="13:30:00">01:30 PM</option>
                                        <option value="14:00:00">02:00 PM</option>
                                        <option value="14:30:00">02:30 PM</option>
                                        <option value="15:00:00">03:00 PM</option>
                                        <option value="15:30:00">03:30 PM</option>
                                        <option value="16:00:00">04:00 PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-notes-medical"></i> Reason for Visit *</label>
                            <textarea name="reason" class="form-control" rows="4" 
                                      placeholder="Please describe your symptoms or reason for visit..." required></textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Book Appointment
                        </button>
                        <a href="index.php?page=dashboard" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('doctor_id').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    var text = selected.text;
    var match = text.match(/-\s(.*?)\s\(/);
    if (match) {
        document.getElementById('specialization').value = match[1];
    } else {
        document.getElementById('specialization').value = '';
    }
});
</script>

<?php require_once 'views/partials/footer.php'; ?>