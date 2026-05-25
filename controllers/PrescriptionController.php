<?php

class PrescriptionController
{
    private $prescriptionModel;
    private $appointmentModel;
    private $doctorModel;

    public function __construct()
    {
        $this->prescriptionModel = new PrescriptionModel();
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
    }

    // =========================================================================
    // index — Admin يرى الكل، Patient يرى وصفاته فقط
    // =========================================================================

    public function index()
    {
        $role = Auth::currentUser()['role'];

        if ($role === 'admin') {

            $prescriptions = $this->prescriptionModel->getAll();

        } elseif ($role === 'patient') {

            $prescriptions = $this->prescriptionModel
                ->getByPatient(Auth::userId());

        } else {

            redirect('index.php?page=403');
        }

        require 'views/prescriptions/index.php';
    }

    // =========================================================================
    // create — فتح نموذج إضافة الوصفة (GET)
    // =========================================================================

    public function create()
    {
        Auth::requireRole('doctor');

        $appointmentId = (int) ($_GET['appointment_id'] ?? 0);

        $appointment = $this->appointmentModel->findById($appointmentId);
        $doctor = $this->doctorModel->findByUserId(Auth::userId());

        // التحقق من الملكية
        if (!$appointment || !$doctor || $appointment['doctor_id'] != $doctor['id']) {
            redirect('index.php?page=403');
        }

        // يجب أن يكون الموعد مكتملاً
        if ($appointment['status'] !== 'completed') {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Prescription can only be added to completed appointments.'
            ];
            redirect('index.php?page=doctor_dashboard');
        }

        // لا يمكن إضافة وصفتين لنفس الموعد
        if ($this->prescriptionModel->existsForAppointment($appointmentId)) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'A prescription already exists for this appointment.'
            ];
            redirect('index.php?page=doctor_dashboard');
        }

        require 'views/prescriptions/create.php';
    }

    // =========================================================================
    // store — حفظ الوصفة مع رفع ملف PDF اختياري (POST)
    // =========================================================================

    public function store()
    {
        Auth::requireRole('doctor');

        // ── CSRF ──────────────────────────────────────────────────────────────
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=doctor_dashboard');
        }

        $appointmentId = (int) ($_POST['appointment_id'] ?? 0);
        $appointment = $this->appointmentModel->findById($appointmentId);
        $doctor = $this->doctorModel->findByUserId(Auth::userId());

        // ── إعادة التحقق من الملكية على POST أيضاً ───────────────────────────
        if (!$appointment || !$doctor || $appointment['doctor_id'] != $doctor['id']) {
            redirect('index.php?page=403');
        }

        if ($appointment['status'] !== 'completed') {
            redirect('index.php?page=403');
        }

        if ($this->prescriptionModel->existsForAppointment($appointmentId)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Prescription already exists.'];
            redirect('index.php?page=doctor_dashboard');
        }

        // ── التحقق من الحقول الإلزامية ────────────────────────────────────────
        $diagnosis = trim($_POST['diagnosis'] ?? '');
        $medications = trim($_POST['medications'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (empty($diagnosis) || empty($medications)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Diagnosis and medications are required.'];
            redirect('index.php?page=create_prescription&appointment_id=' . $appointmentId);
        }

        // ── رفع ملف PDF (اختياري) ─────────────────────────────────────────────
        $filePath = null;

        if (isset($_FILES['prescription_file']) && $_FILES['prescription_file']['error'] === UPLOAD_ERR_OK) {
            $filePath = $this->uploadPrescriptionFile($_FILES['prescription_file'], $appointmentId);

            // إذا فشل الرفع، $filePath يحمل رسالة خطأ بدل مسار
            if ($filePath === false) {
                redirect('index.php?page=create_prescription&appointment_id=' . $appointmentId);
            }
        }

        // ── حفظ في قاعدة البيانات ─────────────────────────────────────────────
        $this->prescriptionModel->create([
            'appointment_id' => $appointmentId,
            'diagnosis' => $diagnosis,
            'medications' => $medications,
            'notes' => $notes,
            'file_path' => $filePath,
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Prescription added successfully.'];
        redirect('index.php?page=doctor_dashboard');
    }

    // =========================================================================
    // download — تحميل PDF مع التحقق من الملكية
    // =========================================================================

    public function download()
    {
        if (!Auth::check()) {
            redirect('index.php?page=login');
        }

        $appointmentId = (int) ($_GET['id'] ?? 0);

        if ($appointmentId <= 0) {
            redirect('index.php?page=404');
        }

        $prescription = $this->prescriptionModel->getByAppointment($appointmentId);
        $appointment = $this->appointmentModel->findById($appointmentId);

        if (!$prescription || !$appointment) {
            redirect('index.php?page=404');
        }

        // ── التحقق من الملكية حسب الدور ──────────────────────────────────────
        $role = Auth::role();
        $userId = Auth::userId();

        if ($role === 'patient' && $appointment['patient_id'] != $userId) {
            redirect('index.php?page=403');
        }

        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($userId);
            if (!$doctor || $appointment['doctor_id'] != $doctor['id']) {
                redirect('index.php?page=403');
            }
        }

        // ── التحقق من وجود الملف ──────────────────────────────────────────────
        if (empty($prescription['file_path'])) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'No PDF file attached to this prescription.'];
            redirect('index.php?page=prescriptions');
        }

        $fullPath = UPLOAD_PATH . 'prescriptions/' . basename($prescription['file_path']);

        if (!file_exists($fullPath)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'File not found on server.'];
            redirect('index.php?page=prescriptions');
        }

        // ── إرسال الملف للمستخدم ──────────────────────────────────────────────
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription.pdf"');
        header('Content-Length: ' . filesize($fullPath));
        header('Cache-Control: private');

        readfile($fullPath);
        exit;
    }

    // =========================================================================
    // uploadPrescriptionFile — دالة خاصة لرفع ملف PDF والتحقق منه
    // =========================================================================

    private function uploadPrescriptionFile(array $file, int $appointmentId)
    {
        // ── 1. حجم الملف ──────────────────────────────────────────────────────
        if ($file['size'] > MAX_PDF_SIZE) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'PDF file too large. Maximum size is 3MB.'
            ];
            return false;
        }

        // ── 2. التحقق من نوع الملف بـ finfo (ليس الامتداد فقط) ───────────────
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if ($mimeType !== 'application/pdf') {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Invalid file type. Only PDF files are allowed.'
            ];
            return false;
        }

        // ── 3. إنشاء مجلد الرفع إذا لم يكن موجوداً ───────────────────────────
        $uploadDir = UPLOAD_PATH . 'prescriptions/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // ── 4. اسم الملف الآمن ────────────────────────────────────────────────
        $filename = 'prescription_' . $appointmentId . '_' . time() . '.pdf';
        $destPath = $uploadDir . $filename;

        // ── 5. نقل الملف من المؤقت ────────────────────────────────────────────
        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Failed to save the file. Please try again.'
            ];
            return false;
        }

        return $filename;
    }
}