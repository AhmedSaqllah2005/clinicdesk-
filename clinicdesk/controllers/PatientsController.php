<?php

// [M11] حذف require_once المكررة — كل الملفات محمّلة بالفعل في index.php

class PatientsController
{
    private $userModel;
    private $patientModel;
    private $appointmentModel;
    private $prescriptionModel;

    public function __construct()
    {
        $this->userModel        = new UserModel();
        $this->patientModel     = new PatientModel();
        $this->appointmentModel = new AppointmentModel();
        $this->prescriptionModel = new PrescriptionModel();
    }

    public function index()
    {
        Auth::requireRole('admin');

        $patients = $this->userModel->getPatients();
        require 'views/patients/index.php';
    }

    public function store()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=patients');
        }

        $name     = trim($_POST['name']);
        $email    = trim($_POST['email']);
        $password = $_POST['password'];
        $phone    = trim($_POST['phone'] ?? '');

        // [M4] التحقق من صيغة الإيميل
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid email format'];
            redirect('index.php?page=patients');
        }

        // [M12] التحقق من طول كلمة المرور
        if (strlen($password) < 8) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Password must be at least 8 characters'];
            redirect('index.php?page=patients');
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email already exists'];
            redirect('index.php?page=patients');
        }

        $data = [
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'role'     => 'patient',
            'phone'    => $phone
        ];

        $this->userModel->create($data);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Patient added successfully'];
        redirect('index.php?page=patients');
    }

    public function edit()
    {
        Auth::requireRole('admin');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {

            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'Invalid patient ID'
            ];

            redirect('index.php?page=patients');
        }

        $patient = $this->userModel->findById($id);

        if (!$patient || $patient['role'] !== 'patient') {

            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'Patient not found'
            ];

            redirect('index.php?page=patients');
        }

        require 'views/patients/edit.php';
    }

    public function update()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=patients');
        }

        $id = (int) $_POST['id'];

        // [M4] التحقق من صيغة الإيميل عند التحديث
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid email format'];
            redirect('index.php?page=patients');
        }

        $userData = [
            'name'  => trim($_POST['name']),
            'email' => $email,
            'phone' => trim($_POST['phone'] ?? '')
        ];

        $this->userModel->update($id, $userData);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Patient updated successfully'];
        redirect('index.php?page=patients');
    }

    public function delete()
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=patients');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=patients');
        }

        $id = (int) ($_POST['patient_id'] ?? 0);
        $this->userModel->deleteUser($id);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Patient deleted successfully'];
        redirect('index.php?page=patients');
    }

    public function profile()
    {
        Auth::requireRole('patient');

        $userId = Auth::userId();

        $patient = $this->patientModel->findByUserId($userId);

        $appointments = $this->appointmentModel->getByPatient($userId);

        $prescriptions = $this->prescriptionModel->getByPatient($userId);

        require 'views/patients/profile.php';
    }

    
    public function updateProfile()
    {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=patient_profile');
        }

        $userId = Auth::userId();

        // [M4] التحقق من صيغة الإيميل
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid email format'];
            redirect('index.php?page=patient_profile');
        }

        $data = [
            'name'  => trim($_POST['name']),
            'email' => $email,
            'phone' => trim($_POST['phone'] ?? '')
        ];

        $this->userModel->update($userId, $data);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile updated successfully'];

        redirect('index.php?page=patient_profile');
    }

    public function appointments()
    {
        Auth::requireRole('patient');

        $userId = Auth::userId();

        $appointments = $this->appointmentModel->getByPatient($userId);

        require 'views/patients/my_appointments.php';
    }

    public function prescriptions()
    {
        Auth::requireRole('patient');

        $userId = Auth::userId();

        $prescriptions = $this->prescriptionModel->getByPatient($userId);

        require 'views/patients/my_prescriptions.php';
    }
}
