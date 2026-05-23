<?php
class SpecializationController {
    private $specializationModel;
    
    public function __construct() {
        $this->specializationModel = new SpecializationModel();
    }
    
    public function index() {
        Auth::requireRole('admin');
        
        $specializations = $this->specializationModel->getAll();
        require 'views/specializations/index.php';
    }
}