<?php
$pageTitle = "Book Appointment";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';

// القيم المحفوظة بعد validation error (تعبّى من $old في book())
$oldPatient = (int) ($old['patient_id'] ?? 0);
$oldDoctor  = (int) ($old['doctor_id']  ?? 0);
$oldTime    = $old['appt_time'] ?? '';
$oldReason  = htmlspecialchars($old['reason'] ?? '', ENT_QUOTES);
// appt_date لا نعيدها عمداً — المستخدم يختار تاريخاً صحيحاً
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
                                                <option value="<?= $p['id'] ?>"
                                                    <?= $p['id'] == $oldPatient ? 'selected' : '' ?>>
                                                    <?= sanitize($p['name']) ?> (<?= sanitize($p['email']) ?>)
                                                </option>
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
                                            <option value="<?= $doc['id'] ?>"
                                                data-spec="<?= sanitize($doc['specialization_name'] ?? 'General') ?>"
                                                <?= $doc['id'] == $oldDoctor ? 'selected' : '' ?>>
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
                                    <?php if (!empty($old)): ?>
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Please choose a valid date.
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-clock"></i> Appointment Time *</label>
                                    <select name="appt_time" class="form-control" required>
                                        <option value="">-- Select Time --</option>
                                        <?php
                                        $times = [
                                            '09:00:00' => '09:00 AM', '09:30:00' => '09:30 AM',
                                            '10:00:00' => '10:00 AM', '10:30:00' => '10:30 AM',
                                            '11:00:00' => '11:00 AM', '11:30:00' => '11:30 AM',
                                            '12:00:00' => '12:00 PM', '12:30:00' => '12:30 PM',
                                            '13:00:00' => '01:00 PM', '13:30:00' => '01:30 PM',
                                            '14:00:00' => '02:00 PM', '14:30:00' => '02:30 PM',
                                            '15:00:00' => '03:00 PM', '15:30:00' => '03:30 PM',
                                            '16:00:00' => '04:00 PM',
                                        ];
                                        foreach ($times as $val => $label):
                                        ?>
                                            <option value="<?= $val ?>"
                                                <?= $val === $oldTime ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-notes-medical"></i> Reason for Visit *</label>
                            <textarea name="reason" class="form-control" rows="4"
                                placeholder="Please describe your symptoms or reason for visit..."
                                required><?= $oldReason ?></textarea>
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
    // تعبية حقل Specialization عند تغيير الطبيب
    function updateSpec() {
        var sel = document.getElementById('doctor_id');
        var opt = sel.options[sel.selectedIndex];
        document.getElementById('specialization').value = opt.dataset.spec || '';
    }

    document.getElementById('doctor_id').addEventListener('change', updateSpec);

    // تعبية Specialization عند تحميل الصفحة (لو رجعنا بعد error)
    window.addEventListener('DOMContentLoaded', function () {
        var sel = document.getElementById('doctor_id');
        if (sel.value) updateSpec();
    });
</script>

<?php require_once 'views/partials/footer.php'; ?>