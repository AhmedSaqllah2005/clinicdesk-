<?php

class PatientMedicineController
{
    private $patientMedicineModel;
    private $medicineModel;
    private $doctorModel;

    public function __construct()
    {
        $this->patientMedicineModel = new PatientMedicineModel();
        $this->medicineModel        = new MedicineModel();
        $this->doctorModel          = new DoctorModel();
    }

    // =========================================================================
    // store — الطبيب يضيف دواء لمريض (POST)
    // =========================================================================
    public function store()
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=doctor_dashboard');
        }

        $doctor = $this->doctorModel->findByUserId(Auth::userId());
        if (!$doctor) redirect('index.php?page=403');

        $patientId  = (int) ($_POST['patient_id']  ?? 0);
        $medicineId = (int) ($_POST['medicine_id'] ?? 0);
        $dosage     = trim($_POST['dosage']         ?? '');

        if (!$patientId || !$medicineId || empty($dosage)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Patient, medicine and dosage are required.'];
            redirect('index.php?page=doctor_dashboard');
        }

        $this->patientMedicineModel->create([
            'doctor_id'   => $doctor['id'],
            'patient_id'  => $patientId,
            'medicine_id' => $medicineId,
            'dosage'      => $dosage,
            'duration'    => trim($_POST['duration'] ?? ''),
            'notes'       => trim($_POST['notes']    ?? ''),
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medicine assigned to patient successfully.'];
        redirect('index.php?page=doctor_dashboard');
    }

    // =========================================================================
    // update — الطبيب يعدّل دواء (POST)
    // =========================================================================
    public function update()
    {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=doctor_dashboard');
        }

        $doctor = $this->doctorModel->findByUserId(Auth::userId());
        if (!$doctor) redirect('index.php?page=403');

        $id         = (int) ($_POST['id']          ?? 0);
        $medicineId = (int) ($_POST['medicine_id'] ?? 0);
        $dosage     = trim($_POST['dosage']         ?? '');

        if (!$id || !$medicineId || empty($dosage)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid data.'];
            redirect('index.php?page=doctor_dashboard');
        }

        // التحقق من الملكية — الطبيب يعدّل فقط ما وصفه هو
        $record = $this->patientMedicineModel->findById($id);
        if (!$record || $record['doctor_id'] != $doctor['id']) {
            redirect('index.php?page=403');
        }

        $this->patientMedicineModel->update($id, $doctor['id'], [
            'medicine_id' => $medicineId,
            'dosage'      => $dosage,
            'duration'    => trim($_POST['duration'] ?? ''),
            'notes'       => trim($_POST['notes']    ?? ''),
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medicine updated successfully.'];
        redirect('index.php?page=doctor_dashboard');
    }

    // =========================================================================
    // delete — الطبيب يحذف دواء (POST)
    // =========================================================================
    public function delete()
    {
        Auth::requireRole('doctor');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=doctor_dashboard');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=doctor_dashboard');
        }

        $doctor = $this->doctorModel->findByUserId(Auth::userId());
        if (!$doctor) redirect('index.php?page=403');

        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) redirect('index.php?page=doctor_dashboard');

        // التحقق من الملكية قبل الحذف
        $record = $this->patientMedicineModel->findById($id);
        if (!$record || $record['doctor_id'] != $doctor['id']) {
            redirect('index.php?page=403');
        }

        $this->patientMedicineModel->delete($id, $doctor['id']);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medicine removed.'];
        redirect('index.php?page=doctor_dashboard');
    }
}
