<?php
$pageTitle = "All Appointments";
require_once 'views/partials/header.php';
require_once 'views/partials/navbar.php';
require_once 'views/partials/sidebar.php';


$currentRole  = Auth::role();
$isPatient    = ($currentRole === 'patient');
$isAdmin      = ($currentRole === 'admin');


$todayFilter   = !$isPatient && ($_GET['filter'] ?? '') === 'today';

$currentStatus = $_GET['status'] ?? '';
$currentDoctor = (int) ($_GET['doctor_id'] ?? 0);
$patientName   = $_GET['patient_name'] ?? '';
$startDate     = $todayFilter ? date('Y-m-d') : ($_GET['start_date'] ?? '');
$endDate       = $todayFilter ? date('Y-m-d') : ($_GET['end_date'] ?? '');


$filterPage    = $isPatient ? 'my_appointments' : 'appointments';
?>

<div class="content-wrapper">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <?php if ($todayFilter): ?>
                            <i class="fas fa-calendar-day mr-1 text-warning"></i> Today's Appointments
                        <?php elseif ($isPatient): ?>
                            <i class="fas fa-calendar-alt mr-1"></i> My Appointments
                        <?php else: ?>
                            All Appointments
                        <?php endif; ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Appointments</li>
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
                        <i class="fas fa-calendar-check mr-1"></i> Appointments List
                    </h3>
                    <div class="card-tools">
                        <?php if ($isPatient): ?>
                            <!-- المريض: زر حجز موعد جديد فقط، بدون Export -->
                            <a href="index.php?page=book_appointment" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> New Appointment
                            </a>
                        <?php else: ?>
                            <!-- Admin: زر حجز + Export CSV -->
                            <a href="index.php?page=appointments&action=book" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> New Appointment
                            </a>
                            <a href="index.php?page=export_appointments" class="btn btn-success btn-sm ml-1">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="card-body border-bottom pt-3 pb-3">
                    <form method="GET" action="index.php" class="row align-items-end">
                        <!-- page صح حسب الدور -->
                        <input type="hidden" name="page" value="<?= $filterPage ?>">

                        <?php if (!$isPatient): ?>
                            <!-- فلاتر الـ Admin: اسم المريض + الطبيب -->
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold small">Patient Name</label>
                                <input type="text" name="patient_name" class="form-control form-control-sm"
                                    placeholder="Search patient..." value="<?= sanitize($patientName) ?>">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label class="mb-1 font-weight-bold small">Doctor</label>
                                <select name="doctor_id" class="form-control form-control-sm">
                                    <option value="">All Doctors</option>
                                    <?php foreach ($doctors as $doc): ?>
                                        <option value="<?= $doc['id'] ?>" <?= $currentDoctor == $doc['id'] ? 'selected' : '' ?>>
                                            <?= sanitize($doc['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Status: مشترك للكل -->
                        <div class="col-md-2 mb-2">
                            <label class="mb-1 font-weight-bold small">Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All</option>
                                <option value="pending"   <?= $currentStatus === 'pending'   ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= $currentStatus === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="completed" <?= $currentStatus === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $currentStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>

                        <?php if (!$isPatient): ?>
                            <!-- فلاتر التاريخ للـ Admin فقط -->
                            <div class="col-md-2 mb-2">
                                <label class="mb-1 font-weight-bold small">From</label>
                                <input type="date" name="start_date" class="form-control form-control-sm"
                                    value="<?= sanitize($startDate) ?>">
                            </div>

                            <div class="col-md-2 mb-2">
                                <label class="mb-1 font-weight-bold small">To</label>
                                <input type="date" name="end_date" class="form-control form-control-sm"
                                    value="<?= sanitize($endDate) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="col-md-2 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-sm btn-secondary mr-1" title="Filter">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="index.php?page=<?= $filterPage ?>" class="btn btn-sm btn-outline-secondary" title="Clear">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>

                    <?php if ($todayFilter): ?>
                        <div class="mt-2">
                            <span class="badge badge-warning px-3 py-2">
                                <i class="fas fa-calendar-day mr-1"></i>
                                Showing today's appointments — <?= date('d M Y') ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <?php if (!$isPatient): ?><th>Doctor</th><?php endif; ?>
                                <?php if ($isPatient): ?><th>Doctor</th><?php endif; ?>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                        No appointments found
                                        <?php if ($todayFilter): ?>
                                            <br><small>No appointments scheduled for today</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $appt): ?>
                                    <tr>
                                        <td><?= $appt['id'] ?></td>
                                        <td><?= sanitize($appt['patient_name']) ?></td>
                                        <td><?= sanitize($appt['doctor_name']) ?></td>
                                        <td><?= formatDate($appt['appt_date']) ?></td>
                                        <td><?= formatTime($appt['appt_time']) ?></td>
                                        <td>
                                            <span class="badge badge-<?= getStatusBadgeClass($appt['status']) ?>">
                                                <?= ucfirst($appt['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= sanitize($appt['reason'] ?? '—') ?></td>
                                        <td>
                                            <?php if ($isPatient): ?>
                                                <!-- المريض: يقدر يلغي فقط إذا كان pending --
                                                     ويشوف الوصفة إذا كان completed وفيه وصفة -->
                                                <?php if ($appt['status'] === 'pending'): ?>
                                                    <form action="index.php?page=cancel_appointment" method="POST" class="d-inline"
                                                        onsubmit="return confirm('Cancel this appointment?')">
                                                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                        <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                                        <button type="submit" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-times-circle"></i> Cancel
                                                        </button>
                                                    </form>
                                                <?php elseif ($appt['status'] === 'completed' && in_array($appt['id'], $prescriptionAppointmentIds ?? [])): ?>
                                                    <a href="index.php?page=my_prescriptions"
                                                       class="btn btn-success btn-sm">
                                                        <i class="fas fa-file-medical"></i> View Prescription
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">—</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- Admin: تغيير الحالة + حذف -->
                                                <form action="index.php?page=update_appointment_status" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                    <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                                    <select name="status" class="form-control form-control-sm d-inline w-auto"
                                                        onchange="this.form.submit()">
                                                        <option value="pending"   <?= $appt['status'] === 'pending'   ? 'selected' : '' ?>>Pending</option>
                                                        <option value="confirmed" <?= $appt['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                        <option value="completed" <?= $appt['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                                        <option value="cancelled" <?= $appt['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                    </select>
                                                </form>
                                                <form action="index.php?page=delete_appointment" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Delete this appointment?')">
                                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                    <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm ml-1">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($paginator->totalPages() > 1): ?>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <?php if ($paginator->hasPrev()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $filterPage ?>&p=<?= $paginator->getPrevPage() ?>&status=<?= $currentStatus ?>&doctor_id=<?= $currentDoctor ?>&patient_name=<?= urlencode($patientName) ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>">
                                        &laquo;
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $paginator->totalPages(); $i++): ?>
                                <li class="page-item <?= $i == $paginator->currentPage() ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $filterPage ?>&p=<?= $i ?>&status=<?= $currentStatus ?>&doctor_id=<?= $currentDoctor ?>&patient_name=<?= urlencode($patientName) ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($paginator->hasNext()): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $filterPage ?>&p=<?= $paginator->getNextPage() ?>&status=<?= $currentStatus ?>&doctor_id=<?= $currentDoctor ?>&patient_name=<?= urlencode($patientName) ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>">
                                        &raquo;
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </section>

</div>

<?php require_once 'views/partials/footer.php'; ?>
