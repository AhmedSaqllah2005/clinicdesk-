<?php
class DashboardController {
    private $userModel;
    private $appointmentModel;
    private $doctorModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
    }
    
    public function index() {
        $user = Auth::currentUser();
        
        if (!$user) {
            redirect('index.php?page=login');
        }
        
        if ($user['role'] == 'admin') {
            $this->adminDashboard();
        } elseif ($user['role'] == 'doctor') {
            $this->doctorDashboard();
        } else {
            $this->patientDashboard();
        }
    }
    
    private function adminDashboard() {
        $totalPatients = $this->userModel->countByRole('patient');
        $totalDoctors = $this->userModel->countByRole('doctor');
        $todayAppointments = $this->appointmentModel->getTodayCount();
        $totalAppointments = $this->appointmentModel->countAll();
        
        $recentAppointments = $this->appointmentModel->getAll(1, []);
        $recentAppointments = array_slice($recentAppointments, 0, 5);
        
        $db = Database::getInstance();
        $result = $db->query("
            SELECT status, COUNT(*) as total 
            FROM appointments 
            WHERE WEEK(appt_date) = WEEK(NOW()) 
            GROUP BY status
        ");
        $weeklyStats = [];
        while ($row = $result->fetch_assoc()) {
            $weeklyStats[$row['status']] = $row['total'];
        }
        
        require 'views/dashboard/admin.php';
    }
    
    private function doctorDashboard() {
        $userId = Auth::userId();
        $doctor = $this->doctorModel->findByUserId($userId);
        
        if (!$doctor) {
            die("Doctor profile not found");
        }
        
        $doctorId = $doctor['id'];
        
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
        
        $page = (int)($_GET['p'] ?? 1);
        $filters = ['status' => $_GET['status'] ?? ''];
        
        $appointments = $this->appointmentModel->getByDoctor($doctorId, $page, $filters);
        $totalItems = $this->appointmentModel->countFiltered('doctor', $doctorId, $filters);
        $paginator = new Paginator($totalItems, ITEMS_PER_PAGE, $page);
        
        $today = $todayCount;
        $monthly = $stats['total'] ?? 0;
        $pending = $stats['pending'] ?? 0;
        $completed = $stats['completed'] ?? 0;
        $upcoming = $todayAppointments;
        $schedule = $appointments;
        
        require 'views/dashboard/doctor.php';
    }
    
    private function patientDashboard() {
        $userId = Auth::userId();
        
        $db = Database::getInstance();
        
        $result = $db->query("
            SELECT COUNT(*) as total 
            FROM appointments 
            WHERE patient_id = ? AND status IN ('pending', 'confirmed')
        ", 'i', [$userId]);
        $activeCount = $result->fetch_assoc()['total'];
        
        $result = $db->query("
            SELECT COUNT(*) as total 
            FROM appointments 
            WHERE patient_id = ? AND status = 'completed'
        ", 'i', [$userId]);
        $completedCount = $result->fetch_assoc()['total'];
        
        $result = $db->query("
            SELECT a.*, u.name as doctor_name, s.name as specialization 
            FROM appointments a 
            JOIN doctors d ON a.doctor_id = d.id 
            JOIN users u ON d.user_id = u.id 
            LEFT JOIN specializations s ON d.specialization_id = s.id 
            WHERE a.patient_id = ? AND a.appt_date >= CURDATE() 
            ORDER BY a.appt_date ASC LIMIT 1
        ", 'i', [$userId]);
        $nextAppointment = $result->fetch_assoc();
        
        $result = $db->query("
            SELECT a.*, u.name as doctor_name 
            FROM appointments a 
            JOIN doctors d ON a.doctor_id = d.id 
            JOIN users u ON d.user_id = u.id 
            WHERE a.patient_id = ? 
            ORDER BY a.appt_date DESC LIMIT 5
        ", 'i', [$userId]);
        $recentAppointments = $result->fetch_all(MYSQLI_ASSOC);
        
        require 'views/dashboard/patient.php';
    }
}