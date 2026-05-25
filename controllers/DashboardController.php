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
        
        // ── نقل من Database Query إلى AppointmentModel ──────────────────────
        $weeklyStats = $this->appointmentModel->getWeeklyStats();
        
        require 'views/dashboard/admin.php';
    }
    
    private function doctorDashboard() {
        $userId = Auth::userId();
        $doctor = $this->doctorModel->findByUserId($userId);
        
        if (!$doctor) {
            die("Doctor profile not found");
        }
        
        $doctorId = $doctor['id'];
        
        // ── نقل من Database Query إلى AppointmentModel ──────────────────────
        $todayAppointments = $this->appointmentModel->getTodayAppointmentsByDoctor($doctorId);
        
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
        
        // ── نقل من Database Queries إلى AppointmentModel ──────────────────
        $activeCount = $this->appointmentModel->getActiveCountForPatient($userId);
        $completedCount = $this->appointmentModel->getCompletedCountForPatient($userId);
        $nextAppointment = $this->appointmentModel->getNextAppointmentForPatient($userId);
        $recentAppointments = $this->appointmentModel->getRecentAppointmentsForPatient($userId, 5);
        
        require 'views/dashboard/patient.php';
    }
}