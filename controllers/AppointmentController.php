<?php

class AppointmentController
{
    private $appointmentModel;
    private $userModel;
    private $doctorModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->userModel = new UserModel();
        $this->doctorModel = new DoctorModel();
    }

    // =========================================================================
    // index — Admin: قائمة كل المواعيد مع فلتر
    // =========================================================================

    public function index()
    {
        Auth::requireRole('admin');

        $page = max(1, (int) ($_GET['p'] ?? 1));
        $filters = [
            'doctor_id' => (int) ($_GET['doctor_id'] ?? 0),
            'status' => $_GET['status'] ?? '',
            'patient_name' => trim($_GET['patient_name'] ?? ''),
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
        ];

        $appointments = $this->appointmentModel->getAll($page, $filters);
        $totalItems = $this->appointmentModel->countFiltered('admin', 0, $filters);
        $paginator = new Paginator($totalItems, ITEMS_PER_PAGE, $page);

        $patients = $this->userModel->getPatients();
        $doctors = $this->doctorModel->getAll();

        require 'views/appointments/index.php';
    }

    // =========================================================================
    // book — Patient: عرض نموذج الحجز (GET)
    // =========================================================================

    public function book()
    {
        Auth::requireRole('patient');

        $doctors = $this->doctorModel->getAll();

        require 'views/appointments/book.php';
    }

    // =========================================================================
    // store — Patient: حفظ الموعد (POST) ← هنا التحقق الكامل
    // =========================================================================

    public function store()
    {
        Auth::requireRole('patient');

        // ── CSRF ──────────────────────────────────────────────────────────────
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=appointments');
        }

        $patientId = Auth::userId();
        $doctorId = (int) ($_POST['doctor_id'] ?? 0);
        $date = trim($_POST['appt_date'] ?? '');
        $time = trim($_POST['appt_time'] ?? '');
        $reason = trim($_POST['reason'] ?? '');

        // ── 1. حقول إلزامية ───────────────────────────────────────────────────
        if (!$doctorId || !$date || !$time || !$reason) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'All fields are required.'];
            redirect('index.php?page=appointments&action=book');
        }

        // ── 2. التاريخ يجب ألا يكون في الماضي ───────────────────────────────
        if ($date < date('Y-m-d')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Appointment date cannot be in the past.'];
            redirect('index.php?page=appointments&action=book');
        }

        // ── 3. التحقق من أن اليوم ضمن أيام عمل الطبيب ───────────────────────
        $availableDays = $this->doctorModel->getAvailableDays($doctorId);
        $dayOfWeek = date('D', strtotime($date)); // مثلاً: Mon, Tue, Sun

        if (!in_array($dayOfWeek, $availableDays)) {
            $daysStr = implode(', ', $availableDays);
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => "This doctor is not available on " . date('l', strtotime($date))
                    . ". Available days: {$daysStr}."
            ];
            redirect('index.php?page=appointments&action=book');
        }

        // ── 4. فحص التعارض (نفس الطبيب + نفس التاريخ + نفس الوقت) ──────────
        if ($this->appointmentModel->hasConflict($doctorId, $date, $time)) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'This time slot is already booked. Please choose another time.'
            ];
            redirect('index.php?page=appointments&action=book');
        }

        // ── 5. حفظ الموعد ────────────────────────────────────────────────────
        $this->appointmentModel->book([
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'appt_date' => $date,
            'appt_time' => $time,
            'reason' => $reason,
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment booked successfully!'];
        redirect('index.php?page=my_appointments');
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

        $id = (int) ($_POST['appointment_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $notes = trim($_POST['doctor_notes'] ?? '');

        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid status.'];
            redirect('index.php?page=dashboard');
        }

        // ── Doctor: يتحقق أن الموعد له ───────────────────────────────────────
        if ($user['role'] === 'doctor') {
            $appointment = $this->appointmentModel->findById($id);
            $doctor = $this->doctorModel->findByUserId($user['id']);

            if (!$appointment || !$doctor || $appointment['doctor_id'] != $doctor['id']) {
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

        $page = max(1, (int) ($_GET['p'] ?? 1));
        $filters = ['status' => $_GET['status'] ?? ''];

        $userId = Auth::userId();
        $appointments = $this->appointmentModel->getByPatient($userId, ITEMS_PER_PAGE, ($page - 1) * ITEMS_PER_PAGE);
        $totalItems = $this->appointmentModel->countFiltered('patient', $userId, $filters);
        $paginator = new Paginator($totalItems, ITEMS_PER_PAGE, $page);

        require 'views/appointments/my_appointments.php';
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

        $id = (int) ($_POST['appointment_id'] ?? 0);
        $appointment = $this->appointmentModel->findById($id);

        // التحقق من الملكية
        if (!$appointment || $appointment['patient_id'] != Auth::userId()) {
            redirect('index.php?page=403');
        }

        // فقط pending يمكن إلغاؤه
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

        $appointments = $this->appointmentModel->getAll(1, []);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="appointments_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

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