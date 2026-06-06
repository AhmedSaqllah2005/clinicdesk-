<?php

class MedicineController
{
    private $medicineModel;

    public function __construct()
    {
        $this->medicineModel = new MedicineModel();
    }

    // قائمة الأدوية — Admin فقط
    public function index()
    {
        Auth::requireRole('admin');
        $medicines = $this->medicineModel->getAll();
        require 'views/medicines/index.php';
    }

    // حفظ دواء جديد
    public function store()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=medicines');
        }

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Medicine name is required.'];
            redirect('index.php?page=medicines');
        }

        $this->medicineModel->create([
            'name'            => $name,
            'dosage_forms'    => trim($_POST['dosage_forms'] ?? ''),
            'default_dosage'  => trim($_POST['default_dosage'] ?? ''),
            'unit'            => trim($_POST['unit'] ?? ''),
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medicine added successfully.'];
        redirect('index.php?page=medicines');
    }

    // تعديل دواء
    public function update()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=medicines');
        }

        $id   = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if (!$id || empty($name)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid data.'];
            redirect('index.php?page=medicines');
        }

        $this->medicineModel->update($id, [
            'name'            => $name,
            'dosage_forms'    => trim($_POST['dosage_forms'] ?? ''),
            'default_dosage'  => trim($_POST['default_dosage'] ?? ''),
            'unit'            => trim($_POST['unit'] ?? ''),
            'is_active'       => (int) ($_POST['is_active'] ?? 1),
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medicine updated successfully.'];
        redirect('index.php?page=medicines');
    }

    // حذف دواء
    public function delete()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            redirect('index.php?page=medicines');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $this->medicineModel->delete($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Medicine deleted.'];
        }

        redirect('index.php?page=medicines');
    }

    // تفعيل/تعطيل
    public function toggle()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            redirect('index.php?page=medicines');
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id) {
            $this->medicineModel->toggleActive($id);
        }

        redirect('index.php?page=medicines');
    }

    // API endpoint — يرجع الأدوية كـ JSON للـ doctor prescription form
    public function apiList()
    {
        Auth::requireRole('doctor');
        header('Content-Type: application/json');
        $medicines = $this->medicineModel->getActive();
        echo json_encode($medicines);
        exit;
    }
}
