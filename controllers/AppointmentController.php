<?php
class AppointmentController {
    private $appointmentModel;
    private $userModel;
    private $doctorModel;
    
    public function __construct() {
        $this->appointmentModel = new AppointmentModel();
        $this->userModel = new UserModel();
        $this->doctorModel = new DoctorModel();
    }
    
    public function index() {
        Auth::requireRole('admin');
        
        $page = (int)($_GET['p'] ?? 1);
        $filters = [
            'doctor_id' => (int)($_GET['doctor_id'] ?? 0),
            'status' => $_GET['status'] ?? '',
            'patient_name' => $_GET['patient_name'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? ''
        ];
        
        $appointments = $this->appointmentModel->getAll($page, $filters);
        $totalItems = $this->appointmentModel->countFiltered('admin', 0, $filters);
        $paginator = new Paginator($totalItems, ITEMS_PER_PAGE, $page);
        
        $patients = $this->userModel->getPatients();
        $doctors = $this->doctorModel->getAll();
        
        require 'views/appointments/index.php';
    }
    
    public function store() {
        Auth::requireRole('admin');
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=appointments');
        }
        
        $patientId = (int)$_POST['patient_id'];
        $doctorId = (int)$_POST['doctor_id'];
        $date = $_POST['appt_date'];
        $time = $_POST['appt_time'];
        $reason = trim($_POST['reason']);
        
        if ($this->appointmentModel->hasConflict($doctorId, $date, $time)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'This slot is already booked'];
            redirect('index.php?page=appointments');
        }
        
        $data = [
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'appt_date' => $date,
            'appt_time' => $time,
            'reason' => $reason
        ];
        
        $this->appointmentModel->book($data);
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment booked successfully'];
        redirect('index.php?page=appointments');
    }
    
    public function updateStatus() {
        $user = Auth::currentUser();
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=dashboard');
        }
        
        $id = (int)$_POST['appointment_id'];
        $status = $_POST['status'];
        
        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid status'];
            redirect('index.php?page=dashboard');
        }
        
        if ($user['role'] == 'doctor') {
            $appointment = $this->appointmentModel->findById($id);
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if (!$appointment || $appointment['doctor_id'] != $doctor['id']) {
                redirect('index.php?page=403');
            }
        }
        
        $this->appointmentModel->updateStatus($id, $status);
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Status updated'];
        
        if ($user['role'] == 'doctor') {
            redirect('index.php?page=doctor_dashboard');
        } else {
            redirect('index.php?page=appointments');
        }
    }
    
    public function delete() {
        Auth::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=appointments');
        }
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=appointments');
        }
        
        $id = (int)($_POST['appointment_id'] ?? 0);
        
        if ($id <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid appointment ID'];
            redirect('index.php?page=appointments');
        }
        
        $this->appointmentModel->deleteAppointment($id);
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment deleted'];
        redirect('index.php?page=appointments');
    }
    
    public function exportCSV() {
        Auth::requireRole('admin');
        
        $appointments = $this->appointmentModel->getAll(1, []);
        
        header('Content-Type: text/csv');
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
                $app['status'],
                $app['reason']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    public function book() {
        Auth::requireRole('patient');
        $doctors = $this->doctorModel->getAll();
        require 'views/appointments/book.php';
    }
    
    public function myAppointments() {
        Auth::requireRole('patient');
        
        $page = (int)($_GET['p'] ?? 1);
        $filters = ['status' => $_GET['status'] ?? ''];
        
        $appointments = $this->appointmentModel->getByPatient(Auth::userId(), $page, $filters);
        $totalItems = $this->appointmentModel->countFiltered('patient', Auth::userId(), $filters);
        $paginator = new Paginator($totalItems, ITEMS_PER_PAGE, $page);
        
        require 'views/appointments/my_appointments.php';
    }
    
    public function cancel() {
        Auth::requireRole('patient');
        
        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid CSRF token'];
            redirect('index.php?page=my_appointments');
        }
        
        $id = (int)$_POST['appointment_id'];
        $appointment = $this->appointmentModel->findById($id);
        
        if (!$appointment || $appointment['patient_id'] != Auth::userId()) {
            redirect('index.php?page=403');
        }
        
        if ($appointment['status'] != 'pending') {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Only pending appointments can be cancelled'];
            redirect('index.php?page=my_appointments');
        }
        
        $this->appointmentModel->updateStatus($id, 'cancelled');
        
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Appointment cancelled'];
        redirect('index.php?page=my_appointments');
    }
}