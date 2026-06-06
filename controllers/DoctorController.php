<?php
class DoctorController
{
    private $doctorModel;
    private $appointmentModel;
    private $userModel;
    private $specializationModel;

    public function __construct()
    {
        $this->doctorModel        = new DoctorModel();
        $this->appointmentModel   = new AppointmentModel();
        $this->userModel          = new UserModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index()
    {
        Auth::requireRole('admin');

        $page   = (int) ($_GET['p'] ?? 1);
        $search = trim($_GET['search'] ?? '');

        $allDoctors   = $this->doctorModel->searchDoctors($search);
        $totalDoctors = count($allDoctors);
        $paginator    = new Paginator($totalDoctors, ITEMS_PER_PAGE, $page);


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


        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=doctors');
        }


        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid email format'];
            redirect('index.php?page=doctors');
        }


        if (strlen($_POST['password']) < 6) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Password must be at least 6 characters'];
            redirect('index.php?page=doctors');
        }


        $userData = [
            'name'     => trim($_POST['name']),
            'email'    => $email,
            'password' => $_POST['password'],
            'role'     => 'doctor',
            'phone'    => trim($_POST['phone'] ?? '')
        ];

        if ($this->userModel->findByEmail($userData['email'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email already exists'];
            redirect('index.php?page=doctors');
        }

        $userId = $this->userModel->create($userData);


        $db = Database::getInstance();
        $db->beginTransaction();

        try {


            $availableDays = isset($_POST['available_days'])
                ? implode(',', $_POST['available_days'])
                : 'Sun,Mon,Tue,Wed,Thu';

            $doctorData = [
                'user_id'           => $userId,
                'specialization_id' => (int) ($_POST['specialization_id'] ?? 0),
                'consultation_fee'  => (float) ($_POST['consultation_fee'] ?? 0),
                'available_days'    => $availableDays,
                'bio'               => trim($_POST['bio'] ?? ''),
                'years_experience'  => (int) ($_POST['years_experience'] ?? 0),
            ];


            if ($doctorData['specialization_id'] <= 0) {
                throw new Exception('Please select a specialization.');
            }

            $this->doctorModel->create($doctorData);


            if (isset($_FILES['doctor_photo']) && $_FILES['doctor_photo']['error'] === UPLOAD_ERR_OK) {
                $photoName = $this->uploadDoctorPhoto($_FILES['doctor_photo'], $userId);
                if ($photoName !== false && $photoName !== null) {
                    $this->doctorModel->updatePhoto($this->doctorModel->getIdByUserId($userId), $photoName);
                }
            }

            $db->commit();

        } catch (Exception $e) {
            $db->rollback();


            $this->userModel->deleteUser($userId);
            $_SESSION['flash'] = ['type' => 'danger', 'message' => $e->getMessage()];
            redirect('index.php?page=doctors');
        }

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


        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid email format'];
            redirect('index.php?page=doctors');
        }


        $userData = [
            'name'  => trim($_POST['name']),
            'email' => $email,
            'phone' => trim($_POST['phone'] ?? '')
        ];
        $this->userModel->update($userId, $userData);


        $doctor = $this->doctorModel->findByUserId($userId);
        if ($doctor) {
            $availableDays = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : 'Sun,Mon,Tue,Wed,Thu';

            $doctorData = [
                'specialization_id' => (int) $_POST['specialization_id'],
                'consultation_fee'  => (float) $_POST['consultation_fee'],
                'available_days'    => $availableDays,
                'bio'               => trim($_POST['bio'] ?? '')
            ];
            $this->doctorModel->update($doctor['id'], $doctorData);


            if (isset($_FILES['doctor_photo']) && $_FILES['doctor_photo']['error'] === UPLOAD_ERR_OK) {
                $photoName = $this->uploadDoctorPhoto($_FILES['doctor_photo'], $userId);
                if ($photoName !== false && $photoName !== null) {


                    if (!empty($doctor['photo'])) {
                        $oldPath = UPLOAD_PATH . 'doctor_photos/' . $doctor['photo'];
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $this->doctorModel->updatePhoto($doctor['id'], $photoName);
                }
            }
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

        $userId   = Auth::userId();
        $doctor   = $this->doctorModel->findByUserId($userId);

        if (!$doctor) {
            die("Doctor profile not found");
        }

        $doctorId = $doctor['id'];
        $page     = (int) ($_GET['p'] ?? 1);
        $filters  = ['status' => $_GET['status'] ?? ''];

        $appointments = $this->appointmentModel->getByDoctor($doctorId, $page, $filters);
        $totalItems   = $this->appointmentModel->countFiltered('doctor', $doctorId, $filters);
        $paginator    = new Paginator($totalItems, ITEMS_PER_PAGE, $page);


        $todayAppointments = $this->appointmentModel->getTodayAppointmentsByDoctor($doctorId);

        $todayCount = $this->appointmentModel->getTodayCount($doctorId);
        $stats      = $this->appointmentModel->getDashboardStats($doctorId);

        require 'views/dashboard/doctor.php';
    }

    public function appointments()
    {
        Auth::requireRole('doctor');

        $userId   = Auth::userId();
        $doctor   = $this->doctorModel->findByUserId($userId);

        if (!$doctor) {
            die("Doctor profile not found");
        }

        $doctorId = $doctor['id'];
        $page     = max(1, (int) ($_GET['p'] ?? 1));
        $filters  = ['status' => $_GET['status'] ?? ''];

        $appointments = $this->appointmentModel->getByDoctor($doctorId, $page, $filters);
        $totalItems   = $this->appointmentModel->countFiltered('doctor', $doctorId, $filters);
        $paginator    = new Paginator($totalItems, ITEMS_PER_PAGE, $page);

        require 'views/appointments/doctor_appointments.php';
    }

    public function profile()
    {
        Auth::requireRole('doctor');

        $userId = Auth::userId();

        $user   = $this->userModel->findById($userId);

        $doctor = $this->doctorModel->findByUserId($userId);

        require 'views/doctors/profile.php';
    }

    public function updateProfile()
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {

            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'Invalid CSRF token'
            ];

            redirect('index.php?page=doctor_profile');
        }

        $userId = Auth::userId();


        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'Invalid email format'
            ];
            redirect('index.php?page=doctor_profile');
        }

        $data = [

            'name'  => trim($_POST['name']),
            'email' => $email,
            'phone' => trim($_POST['phone'])

        ];

        $this->userModel->update($userId, $data);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => 'Profile updated successfully'
        ];

        redirect('index.php?page=doctor_profile');
    }


    private function uploadDoctorPhoto(array $file, int $userId): ?string
    {


        if ($file['size'] > MAX_IMAGE_SIZE) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Photo too large. Maximum size is 1MB.'];
            return false;
        }


        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid image file.'];
            return false;
        }

        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowedTypes)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Only JPEG and PNG images are allowed.'];
            return false;
        }


        $uploadDir = UPLOAD_PATH . 'doctor_photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }


        $ext      = ($imageInfo[2] === IMAGETYPE_JPEG) ? 'jpg' : 'png';
        $filename = 'doctor_' . $userId . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $filename;


        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Failed to save photo. Please try again.'];
            return false;
        }

        return $filename;
    }
}
