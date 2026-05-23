<?php
class UserController
{
    private $userModel;
    private $doctorModel;
    private $specializationModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->doctorModel = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
    }

    public function index()
    {
        Auth::requireRole('admin');

        $page = (int) ($_GET['p'] ?? 1);
        $role = $_GET['role'] ?? '';

        $users = $this->userModel->getAllPaginated($page, $role);
        $totalUsers = $this->userModel->countAll($role);
        $paginator = new Paginator($totalUsers, ITEMS_PER_PAGE, $page);

        require 'views/users/index.php';
    }

    public function store()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=users');
        }

        $data = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'role' => $_POST['role'],
            'phone' => trim($_POST['phone'] ?? '')
        ];

        if ($this->userModel->findByEmail($data['email'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Email already exists'];
            redirect('index.php?page=users');
        }

        $userId = $this->userModel->create($data);

        if ($data['role'] == 'doctor') {
            $availableDays = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : 'Sun,Mon,Tue,Wed,Thu';
            $doctorData = [
                'user_id' => $userId,
                'specialization_id' => (int) $_POST['specialization_id'],
                'consultation_fee' => (float) $_POST['consultation_fee'],
                'available_days' => $availableDays,
                'bio' => trim($_POST['bio'] ?? ''),
                
            ];
            $this->doctorModel->create($doctorData);
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'User created successfully'];
        redirect('index.php?page=users');
    }

    public function edit()
    {
        Auth::requireRole('admin');

        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            redirect('index.php?page=users');
        }

        $user = $this->userModel->findById($id);

        if (!$user) {
            redirect('index.php?page=users');
        }

        $doctor = null;
        $specializations = [];

        if ($user['role'] == 'doctor') {
            $doctor = $this->doctorModel->findByUserId($id);
            $specializations = $this->specializationModel->getAll();
        }

        require 'views/users/edit.php';
    }

    public function update()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=edit_user&id=' . $_POST['id']);
        }

        $id = (int) $_POST['id'];
        $role = $_POST['role'];

        $userData = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone'] ?? '')
        ];
        $this->userModel->update($id, $userData);

        if ($role == 'doctor') {
            $doctor = $this->doctorModel->findByUserId($id);
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
        }

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'User updated successfully'];
        redirect('index.php?page=users&action=edit&id=' . $id);
    }

    public function delete()
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=users');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=users');
        }

        $id = (int) ($_POST['user_id'] ?? 0);
        $currentUser = Auth::userId();

        if ($id == $currentUser) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'You cannot delete your own account'];
            redirect('index.php?page=users');
        }

        $this->userModel->deleteUser($id);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'User deleted successfully'];
        redirect('index.php?page=users');
    }
    public function toggleActive()
{
    Auth::requireRole('admin');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('index.php?page=users');
    }

    if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
        redirect('index.php?page=users');
    }

    $id = (int) ($_POST['id'] ?? 0);

    if ($id == Auth::userId()) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'You cannot deactivate your own account'];
        redirect('index.php?page=users');
    }

    $this->userModel->toggleActive($id);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'User status updated successfully'];
    redirect('index.php?page=users');
}
}