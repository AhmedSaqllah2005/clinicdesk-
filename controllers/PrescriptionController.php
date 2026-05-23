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

    /*
    |--------------------------------------------------------------------------
    | Show Prescriptions
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $role = Auth::currentUser()['role'];

        /*
        |--------------------------------------------------------------------------
        | Admin يرى جميع الوصفات
        |--------------------------------------------------------------------------
        */

        if ($role === 'admin') {

            $prescriptions = $this->prescriptionModel->getAll();
        }

        /*
        |--------------------------------------------------------------------------
        | Patient يرى وصفاته فقط
        |--------------------------------------------------------------------------
        */

        elseif ($role === 'patient') {

            $userId = Auth::userId();

            $prescriptions = $this->prescriptionModel
                ->getByPatient($userId);
        }

        /*
        |--------------------------------------------------------------------------
        | غير مصرح
        |--------------------------------------------------------------------------
        */

        else {

            redirect('index.php?page=403');
        }

        require 'views/prescriptions/index.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Create Prescription
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        Auth::requireRole('doctor');

        $appointmentId = (int) $_GET['appointment_id'];

        $appointment = $this->appointmentModel
            ->findById($appointmentId);

        $doctor = $this->doctorModel
            ->findByUserId(Auth::userId());

        if (
            !$appointment ||
            $appointment['doctor_id'] != $doctor['id']
        ) {

            redirect('index.php?page=403');
        }

        if ($appointment['status'] != 'completed') {

            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Prescription can only be added to completed appointments'
            ];

            redirect('index.php?page=doctor_dashboard');
        }

        if (
            $this->prescriptionModel
                ->existsForAppointment($appointmentId)
        ) {

            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Prescription already exists'
            ];

            redirect('index.php?page=doctor_dashboard');
        }

        require 'views/prescriptions/create.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Store Prescription
    |--------------------------------------------------------------------------
    */

    public function store()
    {
        Auth::requireRole('doctor');

        if (
            !CSRF::validateToken(
                $_POST['csrf_token'] ?? ''
            )
        ) {

            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Invalid CSRF token'
            ];

            redirect('index.php?page=doctor_dashboard');
        }

        $appointmentId = (int) $_POST['appointment_id'];

        $data = [

            'appointment_id' => $appointmentId,

            'diagnosis' => trim($_POST['diagnosis']),

            'medications' => trim($_POST['medications']),

            'notes' => trim($_POST['notes'] ?? ''),

            'file_path' => null
        ];

        $this->prescriptionModel->create($data);

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Prescription added successfully'
        ];

        redirect('index.php?page=doctor_dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | Download Prescription
    |--------------------------------------------------------------------------
    */

    public function download()
    {
        echo "Download function - coming soon";
    }
}