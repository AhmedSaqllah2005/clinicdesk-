<?php

class SpecializationController
{
    private $specializationModel;

    public function __construct()
    {
        $this->specializationModel = new SpecializationModel();
    }

    // =========================================================================
    // index — عرض القائمة
    // =========================================================================

    public function index()
    {
        Auth::requireRole('admin');

        $specializations = $this->specializationModel->getAll();

        require 'views/specializations/index.php';
    }

    // =========================================================================
    // store — إضافة تخصص جديد (POST)
    // =========================================================================

    public function store()
    {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=specializations');
        }

        $name = trim($_POST['name'] ?? '');

        if (empty($name)) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Specialization name is required.'];
            redirect('index.php?page=specializations');
        }

        if (strlen($name) > 100) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Name cannot exceed 100 characters.'];
            redirect('index.php?page=specializations');
        }

        $this->specializationModel->create($name);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Specialization added successfully.'];
        redirect('index.php?page=specializations');
    }

    // =========================================================================
    // delete — حذف تخصص (POST) مع فحص الأمان
    // =========================================================================

    public function delete()
    {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=specializations');
        }

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid security token.'];
            redirect('index.php?page=specializations');
        }

        $id = (int) ($_POST['specialization_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Invalid specialization.'];
            redirect('index.php?page=specializations');
        }

        // ── فحص: هل يوجد أطباء يستخدمون هذا التخصص؟ ─────────────────────────
        if (!$this->specializationModel->isSafeToDelete($id)) {
            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'Cannot delete this specialization — doctors are assigned to it.'
            ];
            redirect('index.php?page=specializations');
        }

        $this->specializationModel->delete($id);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Specialization deleted.'];
        redirect('index.php?page=specializations');
    }
}