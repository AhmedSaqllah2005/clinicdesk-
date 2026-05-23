<?php
class ReportController
{
    private $appointmentModel;
    private $doctorModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
    }

    public function appointments()
    {
        Auth::requireRole('admin');

        $filters = [
            'doctor_id' => (int) ($_GET['doctor_id'] ?? 0),
            'status' => $_GET['status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? ''
        ];

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            if ($filters['start_date'] > $filters['end_date']) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Start date must be before end date'];
                $filters['start_date'] = '';
                $filters['end_date'] = '';
            }
        }

        $appointments = $this->appointmentModel->getAll(1, $filters);
        $doctors = $this->doctorModel->getAll();

        $summary = [
            'total' => count($appointments),
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];

        foreach ($appointments as $app) {
            $summary[$app['status']]++;
        }

        require 'views/reports/appointments.php';
    }

    public function exportCSV()
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        Auth::requireRole('admin');

        $filters = [
            'doctor_id' => (int) ($_GET['doctor_id'] ?? 0),
            'status' => $_GET['status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? ''
        ];

        $appointments = $this->appointmentModel->getAllForExport($filters);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="appointments_report.csv"');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // رؤوس الأعمدة
        fputcsv($output, [
            'ID',
            'Patient',
            'Doctor',
            'Specialization',
            'Date',
            'Time',
            'Status',
            'Reason'
        ], ';');

        // البيانات
        foreach ($appointments as $app) {

            fputcsv($output, [
                $app['id'],
                $app['patient_name'],
                $app['doctor_name'],
                $app['specialization'] ?? '',
                "'" . date('Y-m-d', strtotime($app['appt_date'])),
                $app['appt_time'],
                ucfirst($app['status']),
                $app['reason']
            ], ';');
        }

        fclose($output);
        exit;
    }
}