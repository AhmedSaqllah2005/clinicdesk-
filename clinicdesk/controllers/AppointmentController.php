<?php

class AppointmentController
{
    private $appointmentModel;
    private $userModel;
    private $doctorModel;
    private $prescriptionModel;

    public function __construct()
    {
        $this->appointmentModel  = new AppointmentModel();
        $this->userModel         = new UserModel();
        $this->doctorModel       = new DoctorModel();
        $this->prescriptionModel = new PrescriptionModel();
    }

    // =========================================================================
    // index — Admin: قائمة كل المواعيد مع فلتر
    // =========================================================================

    public function index()
    {
        Auth::requireRole('admin');

        $page = max(1, (int) ($_GET['p'] ?? 1));

        // لما يجي من "Today's Appointments" في الداشبورد
        $todayFilter = ($_GET['filter'] ?? '') === 'today';

        $filters = [
            'doctor_id'    => (int) ($_GET['doctor_id'] ?? 0),
            'status'       => $_GET['status'] ?? '',
            'patient_name' => trim($_GET['patient_name'] ?? ''),
            'start_date'   => $todayFilter ? date('Y-m-d') : ($_GET['start_date'] ?? ''),
            'end_date'     => $todayFilter ? date('Y-m-d') : ($_GET['end_date'] ?? ''),
        ];

        $appointments = $this->appointmentModel->getAll($page, $filters);
        $totalItems   = $this->appointmentModel->countFiltered('admin', 0, $filters);
        $paginator    = new Paginator($totalItems, ITEMS_PER_PAGE, $page);

        $patients = $this->userModel->getPatients();
        $doctors  = $this->doctorModel->getAll();

        require 'views/appointments/index.php';
    }

    // =========================================================================
    // book — Patient/Admin: عرض نموذج الحجز (GET)
    // =========================================================================

    public function book()
    {
        Auth::requireRole('patient', 'admin');

        $doctors = $this->doctorModel->getAll();

        // استرجاع القيم المحفوظة بعد validation error
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);

        require 'views/appointments/book.php';
    }

    // =========================================================================
    // store — Patient/Admin: حفظ الموعد (POST)
    // =========================================================================

    public function store()
    {
        Auth::requireRole('patient', 'admin');

        // ── CSRF ──────────────────────────────────────────────────────────────
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=appointments');
        }

        $user      = Auth::currentUser();
        $patientId = ($user['role'] === 'admin')
            ? (int) ($_POST['patient_id'] ?? 0)
            : Auth::userId();
        $doctorId = (int) ($_POST['doctor_id'] ?? 0);
        $date     = trim($_POST['appt_date'] ?? '');
        $time     = trim($_POST['appt_time'] ?? '');
        $reason   = trim($_POST['reason'] ?? '');

        // ── دالة مساعدة: احفظ البيانات وارجع للفورم ──────────────────────────
        $redirectWithOld = function (string $message) use ($patientId, $doctorId, $time, $reason) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $message];
            // نحفظ كل شي ما عدا التاريخ (هو السبب في الخطأ غالباً)
            $_SESSION['old'] = [
                'patient_id' => $patientId,
                'doctor_id'  => $doctorId,
                'appt_time'  => $time,
                'reason'     => $reason,
            ];
            redirect('index.php?page=appointments&action=book');
        };

        // ── 1. حقول إلزامية ───────────────────────────────────────────────────
        if (!$doctorId || !$date || !$time || !$reason) {
            $redirectWithOld('All fields are required.');
        }

        if ($user['role'] === 'admin' && !$patientId) {
            $redirectWithOld('Please select a patient.');
        }

        // ── 2. التاريخ يجب ألا يكون في الماضي ───────────────────────────────
        if ($date < date('Y-m-d')) {
            $redirectWithOld('Appointment date cannot be in the past.');
        }

        // ── 3. التحقق من أن اليوم ضمن أيام عمل الطبيب ───────────────────────
        $availableDays = $this->doctorModel->getAvailableDays($doctorId);
        $dayOfWeek     = date('D', strtotime($date));

        if (!in_array($dayOfWeek, $availableDays)) {
            $daysStr = implode(', ', $availableDays);
            $redirectWithOld(
                "This doctor is not available on " . date('l', strtotime($date))
                . ". Available days: {$daysStr}."
            );
        }

        // ── 4. فحص التعارض ────────────────────────────────────────────────────
        if ($this->appointmentModel->hasConflict($doctorId, $date, $time)) {
            $redirectWithOld('This time slot is already booked. Please choose another time.');
        }

        // ── 5. حفظ الموعد ────────────────────────────────────────────────────
        $this->appointmentModel->book([
            'patient_id' => $patientId,
            'doctor_id'  => $doctorId,
            'appt_date'  => $date,
            'appt_time'  => $time,
            'reason'     => $reason,
        ]);

        unset($_SESSION['old']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment booked successfully!'];
        $redirect = ($user['role'] === 'admin') ? 'index.php?page=appointments' : 'index.php?page=my_appointments';
        redirect($redirect);
    }

    // =========================================================================
    // updateStatus — Doctor/Admin: تغيير حالة الموعد
    // =========================================================================

    public function updateStatus()
    {
        $user = Auth::currentUser();

        if (!Auth::check()) {
            redirect('index.php?page=login');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=dashboard');
        }

        $id     = (int) ($_POST['appointment_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $notes  = trim($_POST['doctor_notes'] ?? '');

        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid status.'];
            redirect('index.php?page=dashboard');
        }

        $appointment = $this->appointmentModel->findById($id);
        if (!$appointment) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Appointment not found.'];
            redirect('index.php?page=dashboard');
        }

        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);

            if (!$doctor || $appointment['doctor_id'] != $doctor['id']) {
                redirect('index.php?page=403');
            }
        }

        $this->appointmentModel->updateStatus($id, $status, $notes ?: null);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment status updated.'];

        if ($user['role'] === 'doctor') {
            redirect('index.php?page=doctor_dashboard');
        } else {
            redirect('index.php?page=appointments');
        }
    }

    // =========================================================================
    // myAppointments — Patient: مواعيده الخاصة
    // =========================================================================

    public function myAppointments()
    {
        Auth::requireRole('patient');

        $page    = max(1, (int) ($_GET['p'] ?? 1));
        $filters = ['status' => $_GET['status'] ?? ''];

        $userId = Auth::userId();

        $appointments = $this->appointmentModel->getByPatientFiltered($userId, $page, $filters);
        $totalItems   = $this->appointmentModel->countFiltered('patient', $userId, $filters);
        $paginator    = new Paginator($totalItems, ITEMS_PER_PAGE, $page);

        $doctors = [];

        $prescriptionAppointmentIds = [];
        foreach ($appointments as $appt) {
            if ($appt['status'] === 'completed') {
                if ($this->prescriptionModel->existsForAppointment($appt['id'])) {
                    $prescriptionAppointmentIds[] = $appt['id'];
                }
            }
        }

        require 'views/appointments/index.php';
    }

    // =========================================================================
    // cancel — Patient: إلغاء موعد pending
    // =========================================================================

    public function cancel()
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=my_appointments');
        }

        $id          = (int) ($_POST['appointment_id'] ?? 0);
        $appointment = $this->appointmentModel->findById($id);

        if (!$appointment || $appointment['patient_id'] != Auth::userId()) {
            redirect('index.php?page=403');
        }

        if ($appointment['status'] !== 'pending') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Only pending appointments can be cancelled.'];
            redirect('index.php?page=my_appointments');
        }

        $this->appointmentModel->updateStatus($id, 'cancelled');

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment cancelled successfully.'];
        redirect('index.php?page=my_appointments');
    }

    // =========================================================================
    // delete — Admin: حذف موعد
    // =========================================================================

    public function delete()
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=appointments');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=appointments');
        }

        $id = (int) ($_POST['appointment_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid appointment ID.'];
            redirect('index.php?page=appointments');
        }

        $this->appointmentModel->deleteAppointment($id);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment deleted.'];
        redirect('index.php?page=appointments');
    }

    // =========================================================================
    // exportCSV — Admin: تصدير المواعيد
    // =========================================================================

    public function exportCSV()
    {
        Auth::requireRole('admin');

        $appointments = $this->appointmentModel->getAllForExport([]);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="appointments_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, ['ID', 'Patient', 'Doctor', 'Date', 'Time', 'Status', 'Reason']);

        foreach ($appointments as $app) {
            fputcsv($output, [
                $app['id'],
                $app['patient_name'],
                $app['doctor_name'],
                $app['appt_date'],
                $app['appt_time'],
                ucfirst($app['status']),
                $app['reason'],
            ]);
        }

        fclose($output);
        exit;
    }
}