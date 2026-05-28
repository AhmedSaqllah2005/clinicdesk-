<?php

session_start();

require_once 'config/config.php';
require_once 'config/database.php';

require_once 'core/helpers.php';
require_once 'core/CSRF.php';
require_once 'core/Database.php';
require_once 'core/Auth.php';
require_once 'core/Paginator.php';

// ================= MODELS =================
require_once 'models/BaseModel.php';
require_once 'models/UserModel.php';
require_once 'models/DoctorModel.php';
require_once 'models/AppointmentModel.php';
require_once 'models/SpecializationModel.php';
require_once 'models/PrescriptionModel.php';
require_once 'models/PatientModel.php';

// ================= CONTROLLERS =================
require_once 'controllers/AuthController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/AppointmentController.php';
require_once 'controllers/DoctorController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/PatientsController.php';
require_once 'controllers/SpecializationController.php';
require_once 'controllers/PrescriptionController.php';
require_once 'controllers/ReportController.php';

// ================= ROUTER VARIABLES =================
$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'index';

// ================= PUBLIC PAGES =================
$publicPages = ['login'];

if (!isset($_SESSION['user']) && !in_array($page, $publicPages)) {
    header('Location: index.php?page=login');
    exit;
}

// ================= ROUTER =================
switch ($page) {

    // =========================================================
    // AUTH
    // =========================================================
    case 'login':

        $controller = new AuthController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }

        break;

    case 'logout':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $controller = new AuthController();
        $controller->logout();

        break;

    // =========================================================
    // DASHBOARD
    // =========================================================
    case 'dashboard':

        $controller = new DashboardController();
        $controller->index();

        break;

    // =========================================================
    // APPOINTMENTS
    // =========================================================
    case 'appointments':

        $controller = new AppointmentController();

        if ($action === 'book') {

            // patient booking page
            $controller->book();

        } else {

            // admin appointments list
            $controller->index();
        }

        break;

    // ===== FIX FOR 403 =====
    case 'book_appointment':

        Auth::requireRole('patient');

        $controller = new AppointmentController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // save appointment
            $controller->store();

        } else {

            // show booking form
            $controller->book();
        }

        break;

    case 'store_appointment':

        Auth::requireRole('patient');

        $controller = new AppointmentController();
        $controller->store();

        break;

    case 'update_appointment_status':

        $controller = new AppointmentController();
        $controller->updateStatus();

        break;

    case 'delete_appointment':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=appointments');
            exit;
        }

        $controller = new AppointmentController();
        $controller->delete();

        break;

    case 'export_appointments':

        $controller = new AppointmentController();
        $controller->exportCSV();

        break;

    // =========================================================
    // MY APPOINTMENTS (PATIENT)
    // =========================================================
    case 'my_appointments':

        Auth::requireRole('patient');

        $controller = new AppointmentController();
        $controller->myAppointments();

        break;

    // =========================================================
    // DOCTORS
    // =========================================================
    case 'doctors':

        $controller = new DoctorController();

        if ($action === 'edit') {
            $controller->edit();
        } else {
            $controller->index();
        }

        break;

    case 'store_doctor':

        $controller = new DoctorController();
        $controller->store();

        break;

    case 'update_doctor':

        $controller = new DoctorController();
        $controller->update();

        break;

    case 'delete_doctor':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=doctors');
            exit;
        }

        $controller = new DoctorController();
        $controller->delete();

        break;

    case 'doctor_dashboard':

        Auth::requireRole('doctor');

        $controller = new DoctorController();
        $controller->dashboard();

        break;

    case 'doctor_profile':

        Auth::requireRole('doctor');

        $controller = new DoctorController();
        $controller->profile();

        break;

    case 'update_doctor_profile':

        Auth::requireRole('doctor');

        $controller = new DoctorController();
        $controller->updateProfile();

        break;

    // =========================================================
    // USERS
    // =========================================================
    case 'users':

        $controller = new UserController();

        if ($action === 'edit') {

            $controller->edit();

        } elseif ($action === 'toggle') {

            $controller->toggleActive();

        } else {

            $controller->index();
        }

        break;

    case 'edit_user':

        $controller = new UserController();
        $controller->edit();

        break;

    case 'store_user':

        $controller = new UserController();
        $controller->store();

        break;

    case 'update_user':

        $controller = new UserController();
        $controller->update();

        break;

    case 'delete_user':

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=users');
            exit;
        }

        $controller = new UserController();
        $controller->delete();

        break;

    case 'toggle_user':

        $controller = new UserController();
        $controller->toggleActive();

        break;

    // =========================================================
    // PATIENTS
    // =========================================================
    case 'patients':

        Auth::requireRole('admin');

        $controller = new PatientsController();
        $controller->index();

        break;

    case 'edit_patient':

        Auth::requireRole('admin');

        $controller = new PatientsController();
        $controller->edit();

        break;

    case 'store_patient':

        Auth::requireRole('admin');

        $controller = new PatientsController();
        $controller->store();

        break;

    case 'update_patient':

        Auth::requireRole('admin');

        $controller = new PatientsController();
        $controller->update();

        break;

    case 'delete_patient':

        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=patients');
            exit;
        }

        $controller = new PatientsController();
        $controller->delete();

        break;

    // =========================================================
    // PATIENT PROFILE
    // =========================================================
    case 'patient_profile':

        Auth::requireRole('patient');

        $controller = new PatientsController();
        $controller->profile();

        break;

    case 'patient_update_profile':

        Auth::requireRole('patient');

        $controller = new PatientsController();
        $controller->updateProfile();

        break;

    case 'my_prescriptions':

        Auth::requireRole('patient');

        $controller = new PatientsController();
        $controller->prescriptions();

        break;

    // =========================================================
    // PRESCRIPTIONS
    // =========================================================
    case 'prescriptions':

        $controller = new PrescriptionController();
        $controller->index();

        break;

    case 'create_prescription':

        $controller = new PrescriptionController();
        $controller->create();

        break;

    case 'store_prescription':

        $controller = new PrescriptionController();
        $controller->store();

        break;

    // =========================================================
    // SPECIALIZATIONS
    // =========================================================
    case 'specializations':

        $controller = new SpecializationController();
        $controller->index();

        break;

    case 'store_specialization':

        $controller = new SpecializationController();
        $controller->store();

        break;

    case 'delete_specialization':

        $controller = new SpecializationController();
        $controller->delete();

        break;

    // =========================================================
    // REPORTS
    // =========================================================
    case 'reports':

        Auth::requireRole('admin');

        $controller = new ReportController();
        $controller->appointments();

        break;

    case 'export_report_csv':

        Auth::requireRole('admin');

        $controller = new ReportController();
        $controller->exportCSV();

        break;

    // =========================================================
    // ERRORS
    // =========================================================
    case '403':

        require 'views/errors/403.php';

        break;

    default:

        require 'views/errors/404.php';

        break;
}