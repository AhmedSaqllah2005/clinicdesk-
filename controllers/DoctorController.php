<?php
class DoctorController
{
    private $doctorModel;
    private $appointmentModel;
    private $userModel;
    private $specializationModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
        $this->appointmentModel = new AppointmentModel();
        $this->userModel = new UserModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index()
    {
        Auth::requireRole('admin');

        $page = (int) ($_GET['p'] ?? 1);

        $allDoctors = $this->doctorModel->getAll();
        $totalDoctors = count($allDoctors);
        $paginator = new Paginator($totalDoctors, ITEMS_PER_PAGE, $page);

        // Paginate manually
        $doctors = array_slice($allDoctors, $paginator->offset(), ITEMS_PER_PAGE);

        $specializations = $this->specializationModel->getAll();

        require 'views/doctors/index.php';
    }

    public function store()
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Invalid Request");
        }

        // Create user first
        $userData = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'role' => 'doctor',
            'phone' => trim($_POST['phone'] ?? '')
        ];

        if ($this->userModel->findByEmail($userData['email'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email already exists'];
            redirect('index.php?page=doctors');
        }

        $userId = $this->userModel->create($userData);

        // Create doctor record
        $availableDays = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : 'Sun,Mon,Tue,Wed,Thu';

        $doctorData = [
            'user_id' => $userId,
            'specialization_id' => (int) $_POST['specialization_id'],
            'consultation_fee' => (float) $_POST['consultation_fee'],
            'available_days' => $availableDays,
            'bio' => trim($_POST['bio'] ?? ''),
            'license_number' => 'LIC-' . time() . '-' . $userId,
            'years_experience' => (int) ($_POST['years_experience'] ?? 0)
        ];

        $this->doctorModel->create($doctorData);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Doctor added successfully'];
        redirect('index.php?page=doctors');
    }

    public function edit()
    {
        Auth::requireRole('admin');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect('index.php?page=doctors');
        }

        $user = $this->userModel->findById($id);
        if (!$user) {
            redirect('index.php?page=doctors');
        }

        $doctor = $this->doctorModel->findByUserId($id);
        $specializations = $this->specializationModel->getAll();

        require 'views/doctors/edit.php';
    }

    public function update()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=doctors');
        }

        $userId = (int) $_POST['user_id'];

        // Update user data
        $userData = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone'] ?? '')
        ];
        $this->userModel->update($userId, $userData);

        // Update doctor data
        $doctor = $this->doctorModel->findByUserId($userId);
        if ($doctor) {
            $availableDays = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : 'Sun,Mon,Tue,Wed,Thu';

            $doctorData = [
                'specialization_id' => (int) $_POST['specialization_id'],
                'consultation_fee' => (float) $_POST['consultation_fee'],
                'available_days' => $availableDays,
                'bio' => trim($_POST['bio'] ?? '')
            ];
            $this->doctorModel->update($doctor['id'], $doctorData);
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Doctor updated successfully'];
        redirect('index.php?page=doctors');
    }

    public function delete()
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=doctors');
        }

        $userId = (int) ($_POST['user_id'] ?? 0);
        $this->userModel->deleteUser($userId);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Doctor deleted successfully'];
        redirect('index.php?page=doctors');
    }

    public function dashboard()
    {
        Auth::requireRole('doctor');

        $userId = Auth::userId();
        $doctor = $this->doctorModel->findByUserId($userId);

        if (!$doctor) {
            die("Doctor profile not found");
        }

        $doctorId = $doctor['id'];
        $page = (int) ($_GET['p'] ?? 1);
        $filters = ['status' => $_GET['status'] ?? ''];

        $appointments = $this->appointmentModel->getByDoctor($doctorId, $page, $filters);
        $totalItems = $this->appointmentModel->countFiltered('doctor', $doctorId, $filters);
        $paginator = new Paginator($totalItems, ITEMS_PER_PAGE, $page);

        $db = Database::getInstance();
        $result = $db->query("
            SELECT a.*, p.name as patient_name 
            FROM appointments a 
            JOIN users p ON a.patient_id = p.id 
            WHERE a.doctor_id = ? AND a.appt_date = CURDATE() 
            ORDER BY a.appt_time ASC
        ", 'i', [$doctorId]);
        $todayAppointments = $result->fetch_all(MYSQLI_ASSOC);

        $todayCount = $this->appointmentModel->getTodayCount($doctorId);
        $stats = $this->appointmentModel->getDashboardStats($doctorId);

        $today = $todayCount;
        $monthly = $stats['total'] ?? 0;
        $pending = $stats['pending'] ?? 0;
        $completed = $stats['completed'] ?? 0;
        $upcoming = $todayAppointments;
        $schedule = $appointments;

        require 'views/dashboard/doctor.php';
    }
    public function profile()
    {
    Auth::requireRole('doctor');

    $userId = Auth::userId();

    $user = $this->userModel->findById($userId);

    $doctor = $this->doctorModel->findByUserId($userId);

    require 'views/doctors/profile.php';
    }

    public function updateProfile()
    {
    Auth::requireRole('doctor');

    if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {

        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => 'Invalid CSRF token'
        ];

        redirect('index.php?page=doctor_profile');
    }

    $userId = Auth::userId();

    $data = [

        'name'  => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone'])

    ];

    $this->userModel->update($userId, $data);

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Profile updated successfully'
    ];

    redirect('index.php?page=doctor_profile');
    }

}