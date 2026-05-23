<?php

require_once 'BaseModel.php';

class PatientModel extends BaseModel
{
    public function findByUserId($userId)
    {
       return $this->fetchOne("
            SELECT *
            FROM users
            WHERE id = ?
            AND role = 'patient'
        ", "i", [$userId]);
    }
}       